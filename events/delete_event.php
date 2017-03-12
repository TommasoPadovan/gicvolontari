<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 09/03/2017
 * Time: 21:45
 */
session_start();
require_once('../lib/permission.php');
require_once('../lib/sqlLib.php');
require_once('../lib/command.php');

class DeleteEventCommand extends Command {

    public function __construct($permission){
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();

        $eventDate = $db->select('events', ['id' => $_GET['id']]);
        $eventDate = $eventDate[0]['date'];
        $eventDate = explode('-', $eventDate);
        $calendarDateId = $db->select('calendar', [
            'year'  =>  $eventDate[0],
            'month' =>  $eventDate[1],
            'day'   =>  $eventDate[2]
        ]);
        $calendarDateId = $calendarDateId[0]['id'];

        //removing the events -> cascades on eventattendants
        $db->deleteRows('events', ['id' => $_GET['id']]);
        //removing the placeholder from turns table
        $db->deleteRows('turni', ['day' => $calendarDateId]);


        header("Location: eventsandcourses.php");
    }
}

try {
    (new DeleteEventCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}