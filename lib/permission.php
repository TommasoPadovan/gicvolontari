<?php

class PermissionPage {

	private $permissionLevel=NULL;

	const PUBLICPAGE = -1;
	const ADMIN = 1;
	const USER = 2;

	public function __construct($p=0) {
		$this->permissionLevel = $p;
	}

	public function checkPermission() {
		if ($this->permissionLevel == self::PUBLICPAGE)
			return true;
		if (!isset($_SESSION) || $_SESSION==NULL || !isset($_SESSION['permission']) || $_SESSION['permission']==NULL || $this->permissionLevel=NULL)
			return false;
		return $_SESSION['permission'] <= $this->permissionLevel;
	}
}



?>