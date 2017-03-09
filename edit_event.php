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

        $db->insert('events', [
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
        ]);

        header("Location: eventsandcourses.php");
    }
}

try {
    (new EditEventCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}














