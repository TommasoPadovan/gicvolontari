<?php
require_once('lib/sqlLib.php');
session_start();

$db = new DbConnection();

if ( isset($_POST['login']) && isset($_POST['Email']) && isset($_POST['Password']) ) {
	foreach ($db->select('users') as $row) {
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

?>