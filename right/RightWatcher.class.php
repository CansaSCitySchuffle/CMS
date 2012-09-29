<?php

class RightWatcher {

	public function __construct($staticRights) {
		
	}

	public static function checkRights(Accessable $object, $access) {
		$app = Application::getInstance();
		$user = $app->getUser();

		static::getUserObjectRights($user, $object, $access);		
	}	

	public static function getUserObjectRights(BO_User $user, Accessable $object, $access) {
		$dynRight = BO_UserRight::getUserObjectRights($user, $object);
		$globalRight = GlobalRight::getUserObjectRights($user, $object);

		if ($dynRight % $access == 0 || $gloablRight % $access == 0) {
			throw new RightException($user,$object, $access);
		}
	}

}

?>
