<?php
require_once('lib/sqlLib.php');
require_once('lib/command.php');
session_start();




class Login extends Command {
	private $db;

	public function __construct($permission) {
		parent::__construct($permission);
		$this->db = new DbConnection();
	}

	protected function template() {
		if ( isset($_POST['login']) && isset($_POST['Email']) && isset($_POST['Password']) ) {
			foreach ($this->db->select('users') as $row) {
				if ($row['email'] == $_POST['Email'] && $row['psw'] == md5($_POST['Password'])) {
					if ($row['psw'] == md5($_POST['Password'])) {
						$_SESSION['id'] = $row['id'];
						$_SESSION['permessi'] = $row['permessi'];
					} else {
						echo "<script>>alert(\"Password errata\")</script>";
					}
				} else {
					echo "<script>>alert(\"Username errato\")</script>";
				}
			}
		}

		header("Location: home.php");		
	}
}



try {
	(new Login(PermissionPage::PUBLICPAGE))->execute();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}



?>