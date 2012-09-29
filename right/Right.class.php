<?php

interface Right {
	const NOTHING = 0;

	//create is maybe not needed in dynamic(UserRight)Context or should be interpreted as create other objects
	const READ = 1;
	const CREATE = 2;
	const CHANGE = 4;
	const DELETE = 8;

	const ALL_CRUD = 15;

	const SPECIAL1 = 16;
	const SPECIAL2 = 32;
	const SPECIAL3 = 64;

	const ALL = 127;


	public static function getUserObjectRights(BO_User $user, Accessable $object);
}


?>
