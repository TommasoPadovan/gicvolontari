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

    private $lastPage;

    public function __construct($permission) {
        parent::__construct($permission);
        if (isset($_SERVER['HTTP_REFERER']))
            $this->lastPage = $_SERVER['HTTP_REFERER'];
        else $this->lastPage = 'eventsandcourses.php';
    }

    protected function template(){
        $db = new DbConnection();
        $event = $_POST['eventId'];
        $user = $_POST['userId'];

        if ( count( $db->select('eventsattendants', [
                'event' => $event,
                'volunteer' => $user
            ]) )!=0 ) {
            echo("<script> alert('Il volontario selezionato è gia iscritto a questo evento.'); window.location='{$this->lastPage}'; </script>");
        } else {
            $db->insert('eventsattendants', [
                'event' => $event,
                'volunteer' => $user
            ]);

            echo("<script> window.location='{$this->lastPage}'; </script>");
        }

    }
}



try {
    (new AdminReserveUserForEventOrCourseCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}