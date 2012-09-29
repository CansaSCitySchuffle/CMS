<?php
/**
	public static function getClass();

	protected static function getCreateSQL();
	
	protected function getName();	
	
	protected static function getAttributeNames();	

	protected function getIndexedAttributes();

 */

abstract class BO implements Accessable{
	
	protected $persistant = false;	
	static private $instances = array();
	protected $attributes = array();
	
	private function __construct() {
	}

	public static function checkTable() {
		$db = DB_Connection::getInstance();
		if(!$db->existTable(static::getName())) {
			$db->instantQuery(static::getCreateSQL());
		}
	}

	public function save() {
		$db = DB_Connection::getInstance();
		self::checkTable();

		$sql = "INSERT INTO ".static::getName()." (
			`id` ";
		foreach ($this->attributes as $key => $value) {
			$sql .= ", `".$key."`";
		}
			
		$sql .=	")
			VALUES (
			NULL ";

		foreach ($this->attributes as $key => $value) {
			$output = in_array($key, $this->getSaveAttributes())?$db->escapeString($value):$value; 
			$sql .= ", '".$output."'";
		}
		$sql .= ");";
		
		//echo $sql ." <br />";		

		$result = $db->instantQuery($sql);	
		
		$this->attributes['id'] = $db->getLastId();
		$this->persistant = true; 	
	}


	public function update() {
		$db = DB_Connection::getInstance();
		$sql = "UPDATE ".static::getName()." 
			SET ";
		$nosepStr = array();

		foreach ($this->attributes as $key=>$value) {
			$output = in_array($key, $this->getSaveAttributes())?$db->escapeString($value):$value; 
			$nosepStr[] = " `$key` = '".$output."'";
		}

		for($n = 0; $n < count($nosepStr); $n++) {
			$sql .= $nosepStr[$n];
			if($n+1 < count($nosepStr)) {
				$sql.= ", ";
			}
		}
		
		$sql .= " WHERE `id` = '".$this->attributes['id']."';
		";

		$result = $db->instantQuery($sql);		
	}

	public function delete() {
		self::deleteById($this->attributes['id']);
	}

	public static function deleteById($id) {
		$db = DB_Connection::getInstance();
		$sql = "DELETE FROM ".static::getName()."
			WHERE `id` = '".$id."';";
	
		$db->instantQuery($sql);
	}
	
	public function setPersistant() {
		$this->persistant = true;
	}

	public function setAttribute($name, $value) {
		if ($value instanceof BO) 
			$this->attributes[$name] = $value->getId();
		else
			$this->attributes[$name] = $value;
	}	

	public function getAttribute($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		} else 
			return null;
	}

	public function getId() {
		return $this->getAttribute("id");
	}
	
	public abstract static function getClass();

	protected abstract static function getCreateSQL();
	
	/**
	 * @return String name of the Table and unique identifier of this BO-Class
	 */
	protected abstract function getName();	
	
	protected abstract static function getAttributeNames();	

	protected abstract function getIndexedAttributes();

	protected function getSaveAttributes() {
		return $this->getAttributeNames();
	}

	protected function getAttributes() {
		return $this->attributes;
	}

	public static function getById($id) {
		static::checkTable();
		
		$object = static::searchByAttribute("id", $id);
		if (count($object) ==0) {
			return null;
		} 

		$object = $object[0];
		$object->persistant = true;
		return $object;
	}	
	
	public function isPersistant() {
		return $this->persistant;
	}

	public static function searchByAttribute($attribute, $value) {
		static::checkTable();

		$sql = "SELECT * 
			FROM ".static::getName()."
			WHERE $attribute = \"$value\"		
			;";
		
		$db = DB_Connection::getInstance();
		$result = $db->instantQuery($sql);

		return self::convertResult($result);
	}	

	public static function getAll() {
		static::checkTable();

		$attr = "";
		$sql = "SELECT *
			FROM ".static::getName().";";

		$db = DB_Connection::getInstance();
		$result = $db->instantQuery($sql);
		
		return self::convertResult($result);
	}

	public function serialize() {
		return json_encode($this->attributes);
	}

	public function equals(BO $other) {
		return $other->serialize() == $this->serialize();
	}

	private static function convertResult($result) {
		$objects = array();
		$className = static::getClass();
		while($row = $result->fetch_assoc()) {
			if (is_array($row)) {
				$object = new $className(); 
				$object->setPersistant();
				foreach ($row as $key=>$value) {
					
					$object->setAttribute($key, in_array($key, $object->getSaveAttributes())?stripslashes($value):$value);
				}
				$objects[] = $object;
			}
		}
		return $objects;			
	}
}
?>

