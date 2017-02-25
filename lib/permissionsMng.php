<?php


class PermissionsMng {
	public static function atMostAuthorizationLevel($l) {
		if ( !isset($_SESSION['permessi']) || $_SESSION['permessi'] > $l) {
			echo "unauthorized";
			exit;
		}
	}
}


?>