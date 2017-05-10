<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 08/05/2017
 * Time: 15:52
 */


require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/permissionString.php');
require_once('../lib/datetime/date.php');
require_once('../lib/datetime/time.php');
require_once('../lib/command.php');
require_once('classCommitments.php');

class ExportCommitmentsGoogleCalendarCsvCommand extends Command {

    public function __construct($permission){
        parent::__construct($permission);
    }

    protected function template(){
        $db = new DbConnection();

        $commitments = new Commitments();


        $doc = "Subject,Start Date,Start Time,Description,Location\n";



//fetching turn data
        $myTurns = $commitments->getTurnsArray();
        $myTurnsStr=array();
        foreach ($myTurns as $turn) {
            if ($turn['month']->isInFuture()) {
                $doc.="Turno Serale GIC,{$turn['month']->getMonth()}/{$turn['day']}/{$turn['month']->getYear()},9:00 PM,Turno volontari {$turn['task']} posizione {$turn['position']},\n";
            }
        }


//fetching meetings and events data
        $meetings = $commitments->getMeetingsArray();
        $events = $commitments->getEventsArray();

        foreach ($meetings as $meeting) {
            $date = (new Date($meeting['date']))->getEnglishDate();
            $timeStart = (new Time($meeting['timeStart']))->getEnglishTime();
            $doc .= "Riunione: {$meeting['title']},$date,$timeStart,{$meeting['description']},{$meeting['location']}\n";
        }


        foreach ($events as $event) {
            $date = (new Date($event['date']))->getEnglishDate();
            $timeStart = (new Time($event['timeStart']))->getEnglishTime();
            $doc .= "Evento: {$event['title']},$date,$timeStart,{$event['description']},{$event['location']}\n";
        }

        header('Content-Disposition: attachment; filename="i_miei_impegni.csv"');
        header('Content-type: text/plain charset=utf-8');
        echo $doc;
    }
}


try {
    (new ExportCommitmentsGoogleCalendarCsvCommand(PermissionPage::MORNING))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}