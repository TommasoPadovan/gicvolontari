<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 26/05/2017
 * Time: 10:10
 */


require_once('../lib/generalLayout.php');
require_once('../lib/permissionsMng.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/GetConstraints.php');
require_once('../lib/command.php');
require_once('../lib/JsLib.php');




$constraints = new GetConstraints(
    [$_GET['id'] => ['users', 'id']],
    []
);


if (!$constraints->areOk()) {
    $content = $constraints->getErrorContent();
    try {
        $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."volunteers/volunteers.php", PermissionPage::ADMIN);
        $generalLayout->yieldElem('title', "Errore selezione volontario");
        $generalLayout->yieldElem('content', $content);
        echo $generalLayout->getPage();
    }
    catch (UnhautorizedException $e){
        $e->echoAlert();
        exit;
    }
} else {

    class ResetPswCommand extends Command {

        public function __construct($permission) {
            parent::__construct($permission);
        }

        protected function template() {
            $db = new DbConnection;

            $db->update('users',
                ['psw' => md5("cammello")],
                ['id' => $_GET['id']]);

            JS::alertAndRedirect("Password resettata a quella di default", "volunteers.php");
        }
    }


    try {
        (new ResetPswCommand(PermissionPage::ADMIN))->execute();
    }
    catch (UnhautorizedException $e) {
        $e->echoAlert();
    }


}