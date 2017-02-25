<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');

PermissionsMng::atMostAuthorizationLevel(2);


$db = new DbConnection();


$volunteer = $_GET['volunteer'];
$day = $_GET['day'];
$task = $_GET['task'];
$position = $_GET['position'];

if ($volunteer == $_SESSION['id'] || $_SESSION['id']<=1) {
	$db->deleteRows('turni', array(
		'volunteer' => $volunteer,
		'day' => $day,
		'task' => $task,
		'position' => $position
	));

	
}

header("Location: turns.php");

?>