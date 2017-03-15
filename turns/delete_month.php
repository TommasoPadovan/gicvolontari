<?php
require_once('../lib/command.php');
require_once('../lib/sqlLib.php');

class DeleteMonthCommand extends Command {

    private $lastPage;

    public function __construct($permission) {
        parent::__construct($permission);
        if (isset($_SERVER['HTTP_REFERER']))
            $this->lastPage = $_SERVER['HTTP_REFERER'];
        else $this->lastPage = 'turns.php';
    }

    protected function template() {
        $db = new DbConnection();

        $year = $_GET['year'];
        $month = $_GET['month'];

        $db->deleteRows('calendar', array(
            'year'  =>  $year,
            'month' =>  $month
        ));

        header("Location: {$this->lastPage}");
    }
}




try {
    (new DeleteMonthCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}