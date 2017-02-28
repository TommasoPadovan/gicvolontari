<?php
require_once("permission.php");
require_once("exceptions.php");


abstract class Process extends PermissionPage {

	public function __construct($permission) {
		parent::__construct($permission);
	}


	protected abstract function template();

	public function execute() {
		if (!$this->checkPermission()) {
			throw new UnhautorizedException();
			exit;
		}
		$this->template();
	}

}



?>