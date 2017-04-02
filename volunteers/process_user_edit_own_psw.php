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
                echo("<script> alert('Password aggiornata con successo'); window.location='../home.php'; </script>");
            } else echo("<script> alert('La vecchia password è errata'); window.location='user_edit_own_psw.php'; </script>");
        } echo("<script> alert('Le due password non corrispondono'); window.location='user_edit_own_psw.php'; </script>");

    }
}

try {
    (new UserModifyOwnPswCommand(PermissionPage::MORNING, $_SESSION['id']))->execute();
}
catch (AttemptToModifySomeoneElseDataOnPersonalCommandException $e) {
    $e->echoAlert();
}