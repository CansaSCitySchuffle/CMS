<?php

class BO_UserRight extends BO implements Right {

	private function __construct() {

	}

	public static function getClass() {
		return __CLASS__;
	}

	protected static function getCreateSQL() {
		$sql = "CREATE TABLE IF NOT EXISTS `".static::getName()."` (
			`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`user_id` INT NOT NULL ,
			`objectclass` VARCHAR( 128 ) NOT NULL ,
			`objectid` VARCHAR(64) NOT NULL,
			`value` INT NOT NULL,
			UNIQUE KEY `unique` (`user_id`,`objectclass`, `objectid`),
			KEY `user_id` (`user_id`,`objectclass`, `objectid`)
			)";

		return $sql;
	}
	
	protected function getName() {
		return "UserRight";
	}

	public static function createUserToken(BO_User $user, $bo, $permission) {
		$right = new BO_UserRight();
		$right->setAttribute("user_id", $user);		
		$right->setAttribute("objectclass", get_class($bo));
		$right->setAttribute("objectid", $bo->getId());
		$right->setAttribute("value", $permission);
		$right->save();

		return $right;
	}

	public static function getUserObjectRights(BO_User $user, Accessable $object) {
		static::checkTable();
		$objectid = $object->getId();		
		print_r($objectid);
		$sql = "SELECT * 
			FROM ".static::getName()."
			WHERE `user_id` = \"".$user->getId()."\"
			 AND `objectclass` = \"getclass($object)\"
			 AND `objectid` = \"".$objectid."\"		
			;";
		
		$db = DB_Connection::getInstance();
		$result = $db->instantQuery($sql);

		$rights = self::convertResult($result);

		$summed = 0;
		foreach ($right as $rights) {
			$summed += $rights->getAttribute("value");
		}

		return $summed;
	}
	
	protected static function getAttributeNames() {
		return array("user_id", "objectclass", "objectid", "value");
	}

	protected function getIndexedAttributes() {
		return array("user_id", "objectclass", "objectid");
	}

}


?>
