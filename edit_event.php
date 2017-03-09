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

        if ( isset($_GET['type']) )$type = $_GET['type'];
        else $type = '';
        if ( isset($_GET['title']) )$title = $_GET['title'];
        else $title = '';
        if ( isset($_GET['date']) )$date = $_GET['date'];
        else $date = '';
        if ( isset($_GET['timeStart']) )$timeStart = $_GET['timeStart'];
        else $timeStart = '';
        if ( isset($_GET['timeEnd']) )$timeEnd = $_GET['timeEnd'];
        else $timeEnd = '';
        if ( isset($_GET['location']) )$location = $_GET['location'];
        else $location = '';
        if ( isset($_GET['description']) )$description = $_GET['description'];
        else $description = '';
        if ( isset($_GET['requirements']) )$requirements = $_GET['requirements'];
        else $requirements = '';
        if ( isset($_GET['minAttendants']) )$minAttendants = $_GET['minAttendants'];
        else $minAttendants = '';
        if ( isset($_GET['maxAttendants']) )$maxAttendants = $_GET['maxAttendants'];
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

        header("Location: eventandcourses.php");
    }
}

try {
    (new EditEventCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}














