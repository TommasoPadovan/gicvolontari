<?php
require_once('../lib/generalLayout.php');
require_once('../lib/permission.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/command.php');


class UserAddTurnCommand extends Command {

	private $lastPage;

	public function __construct($permission){
		parent::__construct($permission);
		if (isset($_SERVER['HTTP_REFERER']))
            $this->lastPage = $_SERVER['HTTP_REFERER'];
        else $this->lastPage = 'turns.php';
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
			$this->abortMission('Non puoi prenotarti per questo turno, non hai sufficienti privilegi.');
			exit;
		}

		//c'è un altro volontario che fa esattamente la stessa roba?
		$sameTask = $db->select('turni', array(
			'task'		=>	$task,
			'position'	=>	$position,
			'day'		=>	$dayID
		));
		if (count($sameTask)!=0) {
			$this->abortMission('Non puoi prenotarti per questo turno, c è già un altro volontario assegnato.');
			exit;
		}

		//hanno tattarato con l'url cambiando il task?
		if ($task!="oasi" && $task!="clown" && $task!="fiabe" ){
			$this->abortMission('Non puoi prenotarti per questo turno: il ruolo non è valido');
			exit;
		}

		//il volontario è sotto il suo massimo di turni questo mese?
		$volunteerTurnThisMonth = $db->prepare("SELECT * FROM turni AS t JOIN calendar as c ON t.day = c.id WHERE c.year = :year AND c.month = :month AND t.volunteer = :userID");
		$volunteerTurnThisMonth->execute(array(
			':year' => $year,
			':month' => $month,
			':userID' => $userID
		));

		$maxReservations = 2;
		if ($month == date("m")) $maxReservations++; //if the month is the current one you can reserve one time more

		if ( $volunteerTurnThisMonth->rowCount() >= $maxReservations ) {
			$this->abortMission('Non puoi prenotarti per questo turno, sei già al massimo possibile di prenotazioni questo mese.');
			exit;
		}


		$db->insert('turni', array(
			'day'	=> $dayID,
			'task'	=> $task,
			'position'	=> $position,
			'volunteer'	=> $userID
		));
		$this->abortMission('Prenotazione effettuta con successo.');
	}


	private function abortMission($msg='Operazione non valida') {
		echo("<script> alert('$msg'); window.location='{$this->lastPage}'; </script>");
	}

}


try {
	(new UserAddTurnCommand(PermissionPage::EVENING))->execute();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}




