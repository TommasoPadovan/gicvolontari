<?php
require_once('../lib/generalLayout.php');
require_once('../lib/permissionsMng.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/command.php');


class DeleteReservationCommand extends Command {

	private $lastPage;

	public function __construct($permission) {
		parent::__construct($permission);
		if (isset($_SERVER['HTTP_REFERER']))
            $this->lastPage = $_SERVER['HTTP_REFERER'];
        else $this->lastPage = 'turns.php';
	}

	protected function template() {
		$db = new DbConnection();


		$volunteer = $_GET['volunteer'];
		$day = $_GET['day'];
		$task = $_GET['task'];
		$position = $_GET['position'];

		if ($volunteer == $_SESSION['id'] || $_SESSION['id']<=1) {
			$db->deleteRows('turni', array(
				'volunteer' => $volunteer,
				'day' => $day,
				'task' => $task,
				'position' => $position
			));


		}

		header("Location: {$this->lastPage}");
	}
}


try {
	(new DeleteReservationCommand(PermissionPage::EVENING))->execute();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}