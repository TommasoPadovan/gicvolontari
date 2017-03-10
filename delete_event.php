<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 09/03/2017
 * Time: 21:45
 */
session_start();
require_once('lib/permission.php');
require_once('lib/sqlLib.php');
require_once('lib/command.php');

class DeleteEventCommand extends Command {

    public function __construct($permission){
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();
        $db->deleteRows('events', ['id' => $_GET['id']]);

        header("Location: eventsandcourses.php");
    }
}

try {
    (new DeleteEventCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}