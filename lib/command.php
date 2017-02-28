<?php
require_once("permission.php");
require_once("exceptions.php");
session_start();

abstract class Command extends PermissionPage {

	public function __construct($permission) {
		parent::__construct($permission);
	}


	protected abstract function template();

	public function execute() {
		if (!$this->checkPermission()) {
			throw new UnhautorizedException();
		}
		$this->template();
	}

}



?>