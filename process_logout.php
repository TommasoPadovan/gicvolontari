<?php
session_start();

if ( isset($_POST['logout']) ) {
	unset($_SESSION['id']);
	unset($_SESSION['permessi']);
}



header("Location: home.php");


?>