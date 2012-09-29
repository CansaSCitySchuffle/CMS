<?php
session_start();
//session_destroy();	
require_once("controllers.php");
require_once("views.php");


$application = Application::getInstance();
$application->addSettings(require("config.php"));
$application->addSettings(require("myConfig.php"));

$application->process();		


/* class autoloader */
function __autoload($class) {	
	$folder = "";
	if (stristr($class, "Controller")) {
		$folder = "controller/";
	}else 
	if (stristr($class, "DB")) {
		$folder = "db/";
	} else
	if (stristr($class, "bo")) {
		$folder = "bo/";
	} else
	if (stristr($class, "interceptor")) {
		$folder = "Interceptor/";
	} else
	if (stristr($class,"Listener")) {
		$folder = "ActionListener/";
	} else 
	if (stristr($class,"View")) {
		$folder = "view/";
	} else
	if (stristr($class, "Right") || $class == "Accessable") {
		$folder = "right/";
	}	

	if (is_file($folder.$class.".class.php")){
		require_once($folder.$class.".class.php");
	} else
	   echo "Loading Class:".$class." failed. No such such File";
	
	return true;	
}

?>
