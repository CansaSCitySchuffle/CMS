<?php

class NotFoundView extends View {
	
	protected function getTemplateName() {
		return "";
	}

	public function getViewName() {
		return "404";
	}

	protected function allowUserGroup($group) {
		return true;
	}

	public function printContent() {
		$application = Application::getInstance();
		$page = $application->getViewOption("site");
		unset($_POST);
		$_POST['result'] = "fail";
		$_POST['error_messages'][] = "404: Page ".$page." not found";
		return "";
	}

}
