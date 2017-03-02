<?php

class PermissionPage {

	private $permissionLevel;

	const PUBLICPAGE = -1;
	const ADMIN = 1;
	const USER = 2;

	public function __construct($p=NULL) {
		$this->permissionLevel = $p;
	}

	public function checkPermission() {
		if ($this->permissionLevel == self::PUBLICPAGE)
			return true;
		if (!isset($_SESSION) || $_SESSION==NULL || !isset($_SESSION['permessi']) || $_SESSION['permessi']==NULL || $this->permissionLevel==NULL)
			return false;
		$requiredPermission = intval($_SESSION['permessi']);;
		return $requiredPermission <= $this->permissionLevel;
	}

	/**
	 * @return
	 */
	public function getPermissionLevel(){
		return $this->permissionLevel;
	}

	public static function getCurrentPermission() {
		if (!isset($_SESSION) || $_SESSION==NULL || !isset($_SESSION['permessi']) || $_SESSION['permessi']==NULL)
			return null;
		return $_SESSION['permessi'];
	}
}

