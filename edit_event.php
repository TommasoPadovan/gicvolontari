<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 09/03/2017
 * Time: 18:10
 */
require_once('lib/sqlLib.php');
require_once('lib/permission.php');
require_once('lib/command.php');


class EditEventCommand extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();

        if ( isset($_POST['type']) )$type = $_POST['type'];
        else $type = '';
        if ( isset($_POST['title']) )$title = $_POST['title'];
        else $title = '';
        if ( isset($_POST['date']) )$date = $_POST['date'];
        else $date = '';
        if ( isset($_POST['timeStart']) )$timeStart = $_POST['timeStart'];
        else $timeStart = '';
        if ( isset($_POST['timeEnd']) )$timeEnd = $_POST['timeEnd'];
        else $timeEnd = '';
        if ( isset($_POST['location']) )$location = $_POST['location'];
        else $location = '';
        if ( isset($_POST['description']) )$description = $_POST['description'];
        else $description = '';
        if ( isset($_POST['requirements']) )$requirements = $_POST['requirements'];
        else $requirements = '';
        if ( isset($_POST['minAttendants']) )$minAttendants = $_POST['minAttendants'];
        else $minAttendants = '';
        if ( isset($_POST['maxAttendants']) )$maxAttendants = $_POST['maxAttendants'];
        else $maxAttendants = '';

        $dateArray = explode('-',$date);
        $dayRow = $db->select('calendar', [
           'year' => $dateArray[0],
           'month' => $dateArray[1],
           'day' => $dateArray[2],
        ]);
        $dayId = $dayRow[0]['id'];
        $maxVolunteerNumber = $dayRow[0]['maxVolunteerNumber'];

        $newDataArray = [
            'type' => $type,
            'title' => $title,
            'date' => $date,
            'timeStart' => $timeStart,
            'timeEnd' => $timeEnd,
            'location' => $location,
            'description' => $description,
            'requirements' => $requirements,
            'minAttendants' => $minAttendants,
            'maxAttendants' => $maxAttendants
        ];

        if ($_POST['id'] == null) {     //sto creando un evento nuovo
            echo ("into the then");
            $db->insert('events', $newDataArray);           //inserisco l'evento
            $db->deleteRows('turni', ['day' => $dayId]);    //rimuovo eventuali prenotazioni
            foreach (['fiabe', 'oasi', 'clown'] as $task)
                for ($i = 1; $i <= $maxVolunteerNumber; $i++)
                    $db->insert('turni', [
                        'day' => $dayId,
                        'task' => $task,
                        'position' => $i,
                        'volunteer' => 0
                    ]);
        } else {                        //sto modificando un evento esistente
            $oldDate = $db->select('events', ['id' => $_POST['id']]);
            $oldDateId = $oldDate[0]['id'];

            $db->update('events', $newDataArray, ['id' => $_POST['id']]);

            $db->deleteRows('turni', ['day' => $oldDateId]);    //rimuovo eventuali prenotazioni
            $db->deleteRows('turni', ['day' => $dayId]);    //rimuovo eventuali prenotazioni
            foreach (['fiabe', 'oasi', 'clown'] as $task)
                for ($i = 1; $i <= $maxVolunteerNumber; $i++)
                    $db->insert('turni', [
                        'day' => $dayId,
                        'task' => $task,
                        'position' => $i,
                        'volunteer' => 0
                    ]);
        }

        header("Location: eventsandcourses.php");
    }
}

try {
    (new EditEventCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}














