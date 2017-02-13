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
$NumeroTelefono = $_POST['NumeroTelefono'];
$Idirizzo = $_POST['Idirizzo'];
$Indirizzo2 = $_POST['Indirizzo2'];
$Citta = $_POST['Citta'];
$Provincia = $_POST['Provincia'];
$CAP = $_POST['CAP'];
$Stato = $_POST['Stato'];
$position = $_POST['Posizione'];
$permessi = $_POST['Permessi'];


$query = "INSERT INTO `liltvolontari`.`users` (`firstname`, `lastname`, `email`, `psw`, `phone`, `address`, `address2`, `city`, `prov`, `cap`, `state`, `position`, `permessi`)
	VALUES ('$firstname', '$lastname', '$email', '$psw', '$NumeroTelefono', '$Idirizzo', '$Indirizzo2', '$Citta', '$Provincia', '$CAP', '$Stato', '$position', '$permessi');";

$r = queryThis($query,$conn);

header("Location: volunteers.php");


?>