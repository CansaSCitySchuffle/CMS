<?php

class Application {
	static private $instance;
	private $listeners;	
	private $settings;
	private $views = array();
	private $currentView = null;
	private $viewOptions = array();

	private function __construct() {
		
	}
	
	public function getInstance() {
		if(!isset(self::$instance)) {
			self::$instance = new Application();
		}

		return self::$instance;
	}
	
	public function addController(Controller $listener, $action) {
		$this->listeners[$action][] = $listener;
	}
	
	public function addSettings($array) {
		foreach ($array as $name => $value) {
			$this->settings[$name] = $value;
		}
	}
	
	/**
         * TODO: check duplicates
	 */
	public function getHeadTag() {
		$javascript = array();
		$css = array();
		
		$tmp = $this->currentView->getJavascripts();
		foreach($tmp as $script) {
			$javascript[] = $script; 
		}		
		$tmp = $this->currentView->getCss();
		foreach($tmp as $script) {
			$css[] = $script;
		}
		

		$output = "";

		foreach($javascript as $script) {
			$output .= '<script type="text/javascript" src="'.$script.'"></script>';
		}

		foreach($css as $t_css) {
			$output.= '<link type="text/css" rel="stylesheet" href="'.$t_css.'">';
		}

		return $output;
	}

	public function getUser() {
		$user = null;
		if (isset($_SESSION['username'])) {			
			$user = BO_LightUser::searchByAttribute("name", $_SESSION['username']);
			if(count($user)>0) {
				return $user[0];
			}
		} 
			return "guest";
	}	

	public function isAdmin() {
		$user = $this->getUser();
		
		return ($user != "invalid") && ($user->getUserGroup() == "admin");
		
	}
	
	public function addSetting($name, $value) {
		$this->settings[$name] = $value;
	}

	public function addComponent($component) {
		$this->components[] = $component;
	}

	public function addComponents($components) {
		foreach($components as $comp) {
			$this->addComponent($comp);
		}
	}

	public function getComponents() {
		return $this->components;
	}

	public function addViewWithKey(View $view, $key, $useStandardViewKey=true) {
		if ($useStandardViewKey) 
			$this->addView($view);

		$this->views[$key] = $view;
	}	
	
	public function addView(View $view) {
		$this->views[$view->getViewKey()] = $view;
	}

	public function printView($name) {
		if (isset($this->views[$name])) {
			$view = $this->views[$name];	
		} else {
			$view = new NotFoundView();
		}
		$this->currentView = $view;

		//TODO: check Rights
		$view->printView();
	}

	public function getViewOption($key) {
		if (isset($this->viewOptions[$key])) {
			return $this->viewOptions[$key];
		}
		return null;
	}

	public function setViewOption($key, $value) {
		$this->viewOptions[$key] = $value;
	}

	public function getSetting($name) {
		if (isset($this->settings[$name]))
			return $this->settings[$name];

		return null;
	}
	
	private function processPost($action) {
		$redirect = "";

		if(isset($this->listeners[$action]) && is_array($this->listeners[$action])) {
			foreach($this->listeners[$action] as $listener) {
				$result = $listener->process();

				//redirect to another View
				if($result != "")
					$redirect = $result;
			} 
		}
		return $redirect;
	}

	public function process() {
	
		//Try Post-Parameter
		if(isset($_POST['site'])) {
			$this->viewOptions['site'] =$_POST['site'];
		}

		//If there is a Get-Parameter take this one
		foreach($_GET as $key=>$option) {
			$this->viewOptions[$key] = $option;			
		}

		
		//Process Form-Actions and watch for redirections
		$redirect = "";
		if (!empty($_POST)) {
			foreach($_POST as $key=>$value) {
				$redirect = $this->processPost($key);				
			}	
		
		}		

		//If there is a redirection request, take that
		if($redirect != "") {
			$this->viewOptions["site"] = $redirect;
			
			header("Location: ".$this->createRedirectUri());
		}

		if(!isset($this->viewOptions['site'])) {
			$user = $this->getUser();
			if ($user == "guest") {
				$this->viewOptions['site'] = "index";	
			} else {
				$status = $user->getUserGroup();
				if ($status == "User" || $status == "Premium User") {
					$this->viewOptions['site'] = "user";	
				} else {
					$this->viewOptions['site'] = "admin";
				}
			}
			
		}

		$this->printView($this->viewOptions['site']);
	}	

	public function changeView(View $site) {
		$this->viewOptions["site"] = $site->getViewKey();
		header("Location: ".$this->createRedirectUri());
	}

	private function createRedirectUri() {
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = $_SERVER['PHP_SELF'];
		$params = "";
		print_r($this->viewOptions);
		$first = true;
		foreach ($this->viewOptions as $key=>$value) {
			if($first) {
				$params = $key."=".$value;
				$first = false;
			} else {
				$params .= "&".$key."=".$value;
			}
		}

		return "$uri?$params";
		
	}
	
}

?>
