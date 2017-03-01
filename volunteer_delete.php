<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 01/03/2017
 * Time: 18:58
 */
require_once('lib/sqlLib.php');
require_once('lib/permissionsMng.php');
require_once('lib/command.php');


class DeleteVolunteerCommand extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();

        $db->deleteRows('users', array('id' => $_GET['id']));

        header("Location: volunteers.php");
    }
}
try {
    (new DeleteVolunteerCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}