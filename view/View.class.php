<?php

/**
	Here you can copy out all functions you need to override
	
	protected function getTemplateName() {

	}

	protected function allowUserGroup($userGroup) {

	}

	public function getViewName() {

	}

	public function getViewKey() {
		
	}



**/
abstract class View implements Accessable {

	public static $guest = 0;
	public static $user = 1;
	public static $premium = 2;
	public static $admin = 3;

	abstract protected function getTemplateName();

	/**
         * @param $userGroup use static Variable
	 * @return boolean true if it is accessable for the specific Usergroup
	 */
	abstract protected function allowUserGroup($userGroup);

	abstract public function getViewName();

	public function getViewKey() {
		return $this->getViewName();
	}

	public function getId() {
		return $this->getViewKey();
	}

	public function printView() {
		RightWatcher::checkRights($this, Right::READ);		

		$this->prepare();
		$content = "";
		if($this->checkRights()) {
			$content = $this->printContent();
		} else {
			$content = $this->getNoAccessContent();
		}
		
	
	
		include_once("view/templates/layout.php");	
	}
	
	/**
	 * is called bevore page will be print
	 */
	protected function prepare() {
		
	}

	public function getNoAccessContent() {
		$_POST['result'] = "fail";
		$_POST['error_messages'][] = "Zugriff auf die Seite wurde verweigert. Sie müssen sich entweder neu einloggen oder den Benutzer wechseln.";
		//TODO: create a login field or sth. like that		
		return "";
	}

	public function printContent() {
		$page = $this->getTemplateName();
		if(file_exists("view/templates/".$page) && file_exists("view/templates/"."layout.php")) {
			ob_start();
			require("view/templates/".$page);
			$content = ob_get_contents();
			ob_end_clean();
		} else 
			$content = "404: Seite $page existiert nicht.";
		return $content;
	}

	public function checkRights() {
		$app =	Application::getInstance();
		$user = $app->getUser();

		if ($user == "guest") {
			return $this->allowUserGroup(0);
		}

		return $this->allowUserGroup($user->getAttribute("Status"));
	}

	public function getLink() {
		if ($this->checkRights()) {
			return '<a href="?site='.$this->getViewKey().'">'.$this->getViewName().'</a>';
		} else 
			return "";
	}

	public function getJavascripts() {
		return array();
	}

	public function getCss() {
		return array();
	}

}

?>
