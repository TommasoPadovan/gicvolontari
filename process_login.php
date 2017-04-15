<?php
require_once('lib/sqlLib.php');
require_once('lib/command.php');




class Login extends Command {
	private $db;

	public function __construct($permission) {
		parent::__construct($permission);
		$this->db = new DbConnection();
	}

	protected function template() {
		/*var_dump($_POST);
		echo '<br>'; 
		$userslist = $this->db->query('SELECT * FROM users');
		foreach ($userslist as $row) {
			echo 'riga: ';
			var_dump($row);
			echo '<br>';
		}
		echo "me here";
		exit;*/
		if ( isset($_POST['Email']) && isset($_POST['Password']))  {
			$user = $this->db->select('users', ['email' => $_POST['Email']]);

			if (count($user)==0) {
				echo("<script> alert('Username errato'); window.location='home.php'; </script>");
			} else {
				if ( $user[0]['psw'] != md5($_POST['Password']) ) {
					echo("<script> alert('Password errata'); window.location='home.php'; </script>");
				} else {
					$_SESSION['id'] = $user[0]['id'];
					$_SESSION['permessi'] = $user[0]['permessi'];
					$_SESSION['name'] = $user[0]['firstname'] . ' ' . $user[0]['lastname'];
				}
			}
		}



//		if (isset($_POST['Email']) && isset($_POST['Password']) ) {
//			foreach ($this->db->select('users') as $row) {
//				if ($row['email'] == $_POST['Email'] && $row['psw'] == md5($_POST['Password'])) {
//					if ($row['psw'] == md5($_POST['Password'])) {
//						$_SESSION['id'] = $row['id'];
//						$_SESSION['permessi'] = $row['permessi'];
//						$_SESSION['name'] = $row['firstname'] . ' ' . $row['lastname'];
//					} else {
//						echo("<script> alert('Password errata'); window.location='home.php'; </script>");
//					}
//				} else {
//					echo("<script> alert('Username errato'); window.location='home.php'; </script>");
//				}
//			}
//		}

		echo("<script> window.location='home.php'; </script>");
	}
}



try {
	(new Login(PermissionPage::PUBLICPAGE))->execute();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}
