<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 10/03/2017
 * Time: 12:16
 */
require_once('../lib/command.php');
require_once('../lib/sqlLib.php');

class ReserveForEventOrCourseCommand extends Command {

    public function __construct($permission){
        parent::__construct($permission);
    }

    protected function template(){
        $db = new DbConnection();

        if ( isset($_SESSION['id']) && isset($_GET['event'])) {
            $db->insert('eventsattendants', [
                'event' => $_GET['event'],
                'volunteer' => $_SESSION['id']
            ]);
        }

        header("Location: eventsandcourses.php");
    }
}

try {
    (new ReserveForEventOrCourseCommand(PermissionPage::AFTERNOON))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}