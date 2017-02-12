<?php

class PermissionsMng {
	public static function atMostAuthorizationLevel($l) {
		if ( $_SESSION['permessi'] > $l) {
			echo "unauthorized";
			exit;
		}
	}
}


?>