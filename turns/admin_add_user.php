<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 07/03/2017
 * Time: 18:22
 */
require_once('../lib/command.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');

class AddTurn extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();

        $task = $_POST['task'];
        $position = $_POST['position'];
        $year = $_POST['year'];
        $month = $_POST['month'];
        $day = $_POST['day'];


        $dayID = $db->select('calendar',array(
            'year'	=>	$year,
            'month'	=>	$month,
            'day'	=>	$day
        ))[0]['id'];

        $userID = $_POST['user'];


        //sanity checks
        /**
         * ho spento questo controllo perché secondo me l'admin può mettere chiunque in qualsiasi posto
         */
        //è la giusta posizione del volontario?
        $row = $db->select('users', array('id' => $userID))[0];
        /*if ($position != $row['position']){
            $this->abortMission();
            exit;
        }*/

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
//        $volunteerTurnThisMonth = $db->prepare("SELECT * FROM turni AS t JOIN calendar as c ON t.day = c.id WHERE c.year = :year AND c.month = :month AND t.volunteer = :userID");
//        $volunteerTurnThisMonth->execute(array(
//            ':year' => $year,
//            ':month' => $month,
//            ':userID' => $userID
//        ));
//        if ( $volunteerTurnThisMonth->rowCount() >=2 ) {
//            $this->abortMission();
//            exit;
//        }


        $db->insert('turni', array(
            'day'	=> $dayID,
            'task'	=> $task,
            'position'	=> $position,
            'volunteer'	=> $userID
        ));

        header("Location: turns.php?Mese=$year-$month");
    }


    private function abortMission() {
        header("Location: turns.php");
    }
}



try {
    (new AddTurn(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}









