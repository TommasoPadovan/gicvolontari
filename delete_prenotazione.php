<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');

PermissionsMng::atMostAuthorizationLevel(2);


$conn=connect();


$volunteer = $_GET['volunteer'];
$day = $_GET['day'];
$task = $_GET['task'];
$position = $_GET['position'];

if ($volunteer == $_SESSION['id'] || $_SESSION['id']<=1) {
	queryThis("DELETE FROM turni WHERE volunteer=$volunteer AND day=$day AND task='$task' AND position=$position", $conn);
}

header("Location: turns.php");

?>