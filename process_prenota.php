<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');

PermissionsMng::atMostAuthorizationLevel(2);


$conn=connect();


$task = $_GET['task'];
$position = $_GET['position'];
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];

$dayID = mysql_fetch_assoc( queryThis("SELECT * FROM calendar WHERE year=$year AND month=$month AND day=$day",$conn) );
$dayID = $dayID['id'];

$userID = $_SESSION['id'];

//sanity check
//è la giusta posizione del volontario?
$row=mysql_fetch_assoc( queryThis("SELECT * FROM users WHERE id = $userID",$conn) );
if ($position != $row['position']){
	abortMission();
	exit;
}

//c'è un altro volontario che fa esattamente la stessa roba?
if (!mysql_num_rows(queryThis("SELECT * FROM turni WHERE task='$task' AND position=$position AND day=$dayID",$conn))==0) {
	abortMission();
	exit;
}

//hanno tattarato con l'url cambiando il task?
if ($task!="oasi" && $task!="clown" && $task!="fiabe" ){
	abortMission();
	exit;
}

//il volontario è sotto il suo massimo di turni questo mese?
if ( mysql_num_rows( queryThis("SELECT * FROM turni AS t JOIN calendar as c ON t.day = c.id WHERE c.month = $month AND t.volunteer = $userID ", $conn) )>=2 ) {
	abortMission();
	exit;
}



queryThis("INSERT INTO `liltvolontari`.`turni` (`day`, `task`, `position`, `volunteer`) VALUES ($dayID, '$task', $position, $userID);",$conn);
echo "<script>alert(\"Prenotazione effettuta con successo\")</script>";
abortMission();




function abortMission() {
	header("Location: turns.php");
}

?>