<?php
class RightException extends Exception {
	
	private $m_user;
	private $m_object;
	private $m_access;
	
	public function __construct($user, $object, $access) {
		$this->m_user = $user;
		$this->m_object = $object;
		$this->m_access = $access;
	}

	//TODO: make a better errormessage
	public function __toString() {
		return $this->m_user+" has not the Right to access "+ $object;
	}
}

?>
