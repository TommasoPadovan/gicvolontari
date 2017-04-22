<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 15/03/2017
 * Time: 17:55
 */

require_once('../lib/sqlLib.php');
require_once('../lib/permissionsMng.php');
require_once('../lib/personalCommand.php');

class UserModifyOwnProfileCommand extends PersonalCommand {

    public function __construct($permission, $id){
        parent::__construct($permission, $id);
    }

    protected function template() {
        $db = new DbConnection();

        $id = $this->onlyAuthorizedId;

        $firstname = $_POST['Nome'];
        $lastname = $_POST['Cognome'];
        $email = $_POST['Email'];
        $CF = $_POST['CodiceFiscale'];
        $birthDate = $_POST['DataDiNascita'];
        $NumeroTelefono = $_POST['NumeroTelefono'];
        $Indirizzo = $_POST['Indirizzo'];
        $Indirizzo2 = $_POST['Indirizzo2'];
        $Citta = $_POST['Citta'];
        $Provincia = $_POST['Provincia'];
        $CAP = $_POST['CAP'];
        $Stato = $_POST['Stato'];

        $db->update(
            'users',
            array(
                'firstname'	=> $firstname,
                'lastname'	=> $lastname,
                'email'		=> $email,
                'CF'        => $CF,
                'birthdate' => $birthDate,
                'phone'		=> $NumeroTelefono,
                'address'	=> $Indirizzo,
                'address2'	=> $Indirizzo2,
                'city'		=> $Citta,
                'prov'		=> $Provincia,
                'cap'		=> $CAP,
                'state'		=> $Stato
            ),
            array('id'  =>  $id)
        );

        header("Location: ../home.php");
    }
}

try {
    (new UserModifyOwnProfileCommand(PermissionPage::MORNING, $_SESSION['id']))->execute();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
}