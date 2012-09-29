<?php

/**
 * Implements the singleton pattern, so only one Instance will be created
 * at runtime. Additionaly Persistant MySQL Connection technology is used
 * and Querys will be collected and send after Processing the whole 
 * Request
 *
 * TODO: Fehlerabfragen, change DBCaller interface and queryQueue to indent 
 * the query out of the DBCaller
 */
class DB_Connection {
	private $host = "";
	private $db = "";
	private $user = "";
	private $pass = "";

	private $dbHandle;
	private $queryQueue = Array(); //tupel of (query, callback)

	static private $instance;

	/**
	 * is needed to get an Instance of DB_Connection because the 
	 * Constructor is private
	 * @param $host (optional) hostname
	 * @param $db (optional) DB name
	 * @param $user (optional) DB user
	 * @param $pass (optional) DB password
	 */
	public static function getInstance($host=Null , $db=Null ,
			$user=Null , $pass=Null ) {
		$application = Application::getInstance();
		$host = isset($host)?$host:$application->getSetting("dbHost");
		$db = isset($db)?$db:$application->getSetting("dbName");
		$user = isset($user)?$user:$application->getSetting("dbUser");
		$pass = isset($pass)?$pass:$application->getSetting("dbPassword");

		 if (!isset(self::$instance)) 
			 self::$instance = new DB_Connection($host, $db, $user, $pass);
		
		return self::$instance;
	}


	/**
	 *
	 *
	 */
	private function __construct($host, $db, $user, $pass) {
		$this->host = $host;
		$this->db = $db;
		$this->user = $user;
		$this->pass = $pass;
	} 

	/**
	 *
	 *
	 /
	public function __call($function, $arguments) {
				$src = method_exists($this, $function) ? $this : $this->dbHandle;
						return call_user_func_array(array($src, $function), $arguments);
	}*/

	/**
	 * Opens a connection, if allready a Connection exists it will keep the
	 * old one
	 * 
	 */
	private function connect() {
		if(!isset($this->dbHandle)) {
			$this->dbHandle = new mysqli($this->host, $this->user, $this->pass,
					$this->db);
		}
	}
	
	/**
	 *
	 *
	 */
	public function existTable($table) {
		$sql = "SHOW Tables WHERE Tables_in_".
			$this->db." = '".$table."';";

		$res = $this->instantQuery($sql);
		if ($res->num_rows > 0) 
			return true;
		else
			return false;
	}

	/**
	 *
	 * @param $query the query wich should be send to the DB
	 * @param $resultCallback the function called after processing the Queue
	 */
	public function sendQuery($query, DBCaller $resultCallback = Null) {
		$this->queryQueue[] =  Array($query, $resultCallback);
	}

	/**
	 * TODO: if too many Connections stay open
	 * i should think about another way to handle
	 * resolutions, so i can close them here
	 * escape parameters
	 */
	 public function instantQuery($query) {
			$this->connect();
			//here would be a good place to log querys
			$res = $this->dbHandle->query($query);
			//$res->close();
			//$this->dbHandle->close();
			if($res) {
			 return $res;
			} else {
				throw new Exception($this->dbHandle->error);
			}
	 }

	public function escapeString($str) {
		$this->connect();
		return $this->dbHandle->real_escape_string($str);
	}

	/**
	 * Its important to call this Method only if needed
	 * or only at the End to keep this performand.
	 *
	 * Otherwise use the sendQuery Method to put your Query into the Queue,
	 * so	it will be processed at the end of the Userrequest
	 *
	 * Calls after processing the query from queryQueue the originobject 
	 * with the MySQL Result
	 *
	 * Todo: implement a QueryCache with Resultcaching. Maybe by ordering the queryQueue and group the Statements
	 * 
	 * TODO: If there are open MySQL Connections the problem is maybe that
	 * they arent closed here
	 */
	public function processQuerys() {
		$this->connect();

		if (!isset($this->queryQueue))
			return;
		
		//process the QueryQueue
		for($i = 0; $i < sizeof($this->queryQueue); $i++) {
			$query = $this->queryQueue[$i];
			$res = $this->dbHandle->query($query[0]);

			if (isset($query[1]))
				$query[1]->getResult($res);
		}

	}

	/**
	 * 
	 *
	 */
	public function getLastId() {
		return $this->dbHandle->insert_id;
	}

	public static function dateToTimestamp($date) {
		return date('Y-m-d H:i:s', $date);
	}

	public static function timestampToDate($timestamp) {
		return strtotime($timestamp);
	}

}


?>
