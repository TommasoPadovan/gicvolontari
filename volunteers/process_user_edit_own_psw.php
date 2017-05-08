<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 15/03/2017
 * Time: 18:13
 */

require_once('../lib/sqlLib.php');
require_once('../lib/permissionsMng.php');
require_once('../lib/personalCommand.php');
require_once('../lib/JsLib.php');

class UserModifyOwnPswCommand extends PersonalCommand {

    public function __construct($permission, $id){
        parent::__construct($permission, $id);
    }


    protected function template(){
        $db = new DbConnection();

        $id = $this->onlyAuthorizedId;

        $oldPsw = $_POST['oldpsw'];
        $newPsw = $_POST['newpsw'];
        $newPswR = $_POST['newpswr'];

        $user = $db->getUser($id);

        if ($newPsw == $newPswR) {
            if (md5($oldPsw) == $user['psw']) {
                $db->update(
                    'users',
                    ['psw' => md5($newPsw)],
                    ['id' => $id]
                );
                JS::alertAndRedirect('Password aggiornata con successo', '../home.php');
            } else JS::alertAndRedirect('La vecchia password è errata', 'user_edit_own_psw.php');
        } JS::alertAndRedirect('Le due password non corrispondono', 'user_edit_own_psw.php');

    }
}

try {
    (new UserModifyOwnPswCommand(PermissionPage::MORNING, $_SESSION['id']))->execute();
}
catch (AttemptToModifySomeoneElseDataOnPersonalCommandException $e) {
    $e->echoAlert();
}