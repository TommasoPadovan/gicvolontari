<?php
require_once('../lib/generalLayout.php');
require_once('../lib/permission.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/command.php');


class UserAddTurnCommand extends Command {

	public function __construct($permission){
		parent::__construct($permission);
	}

	protected function template(){
		$db = new DbConnection();


		$task = $_GET['task'];
		$position = $_GET['position'];
		$year = $_GET['year'];
		$month = $_GET['month'];
		$day = $_GET['day'];


		$dayID = $db->select('calendar',array(
			'year'	=>	$year,
			'month'	=>	$month,
			'day'	=>	$day
		))[0]['id'];

		$userID = $_SESSION['id'];


		//sanity check
		//è la giusta posizione del volontario?
		$row = $db->select('users', array('id' => $userID))[0];
		if (
			($row['position']==1 && $position==3) ||
			($row['position']==2 && ($position==1 || $position==3)) ||
			$row['position']==3
		){
			$this->abortMission();
			exit;
		}

		//c'è un altro volontario che fa esattamente la stessa roba?
		$sameTask = $db->select('turni', array(
			'task'		=>	$task,
			'position'	=>	$position,
			'day'		=>	$dayID
		));
		if (!count($sameTask)==0) {
			$this->abortMission();
			exit;
		}

		//hanno tattarato con l'url cambiando il task?
		if ($task!="oasi" && $task!="clown" && $task!="fiabe" ){
			$this->abortMission();
			exit;
		}

		//il volontario è sotto il suo massimo di turni questo mese?
		$volunteerTurnThisMonth = $db->prepare("SELECT * FROM turni AS t JOIN calendar as c ON t.day = c.id WHERE c.year = :year AND c.month = :month AND t.volunteer = :userID");
		$volunteerTurnThisMonth->execute(array(
			':year' => $year,
			':month' => $month,
			':userID' => $userID
		));
		if ( $volunteerTurnThisMonth->rowCount() >=2 ) {
			$this->abortMission();
			exit;
		}


		$db->insert('turni', array(
			'day'	=> $dayID,
			'task'	=> $task,
			'position'	=> $position,
			'volunteer'	=> $userID
		));
		echo "<script>alert(\"Prenotazione effettuta con successo\")</script>";
		$this->abortMission();
	}


	private function abortMission() {
		header("Location: turns.php");
	}

}


try {
	(new UserAddTurnCommand(PermissionPage::EVENING))->execute();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}




