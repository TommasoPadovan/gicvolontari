<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 10/03/2017
 * Time: 12:44
 */
require_once('../lib/command.php');
require_once('../lib/sqlLib.php');

class AdminReserveUserForEventOrCourseCommand extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }

    protected function template(){
        $db = new DbConnection();
        $event = $_POST['eventId'];
        $user = $_POST['userId'];

        $db->insert('eventsattendants', [
            'event' => $event,
            'volunteer' => $user
        ]);

        header('Location: eventsandcourses.php');
    }
}



try {
    (new AdminReserveUserForEventOrCourseCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}