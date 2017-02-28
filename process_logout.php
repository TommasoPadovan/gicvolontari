<?php
require_once('lib/command.php');
session_start();

class Logout extends Command {

	public function __construct($permission) {
		parent::__construct($permission);
	}

	protected function template() {

		if ( isset($_POST['logout']) ) {
			unset($_SESSION['id']);
			unset($_SESSION['permessi']);
		}
		header("Location: home.php");
	}
}

try {
	(new Logout(PermissionPage::PUBLICPAGE))->execute();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}