<?php

abstract class BO_User extends BO {

	protected function __construct() {
	}
	
	/**
	 * salt to change hash of Password
	 */
	protected static abstract function getSalt();
	
	public static abstract function register($username, $passwort);	
	
	/**
 	 * @return true if the user exists, the password is correct and the user is activated
	 */
	public static abstract function login($username, $passwort);
	

	/**
	 * Try to use this one to set the Password.
	 *
	 */
	public function setPassword($value) {
		$this->setPassword("password", $value);
	}
	
	
	public function setAttribute($name, $value) {
		if($name == "password" && !$this->isPersistant()) {
			parent::setAttribute($name, md5($value&static::getSalt()));
		} else
			parent::setAttribute($name, $value);		
	}


	/**
	 * Returns a String
	 *
	 * @return String qualified String to find out which UserGroup this User belongs to
	 */
	public abstract function getUserGroup();
	
}

?>
