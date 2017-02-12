<?php
require_once('lib/sqlLib.php');
require_once('lib/permissionsMng.php');
PermissionsMng::atMostAuthorizationLevel(1);

$conn=connect();

var_dump($_POST);

$firstname = $_POST['Nome'];
$lastname = $_POST['Cognome'];
$email = $_POST['Email'];
$psw = md5( $_POST['Password'] );
$position = $_POST['Posizione'];
$permessi = $_POST['Permessi'];

$query = "INSERT INTO `liltvolontari`.`users` (`firstname`, `lastname`, `email`, `psw`, `position`, `permessi`) VALUES ('$firstname', '$lastname', '$email', '$psw', '$position', '$permessi');";
$r = queryThis($query,$conn);

header("Location: volunteers.php");


?>