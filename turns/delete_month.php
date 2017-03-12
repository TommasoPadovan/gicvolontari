<?php
require_once('../lib/command.php');
require_once('../lib/sqlLib.php');

class DeleteMonthCommand extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();

        $year = $_GET['year'];
        $month = $_GET['month'];

        $db->deleteRows('calendar', array(
            'year'  =>  $year,
            'month' =>  $month
        ));

        header("Location: events.php");
    }
}




try {
    (new DeleteMonthCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}