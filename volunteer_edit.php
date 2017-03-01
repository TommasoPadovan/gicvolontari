<?php
session_start();
require_once('lib/sqlLib.php');
require_once('lib/permissionsMng.php');
require_once('lib/command.php');

class InsertNewUserCommand extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();

        $id = $_POST['id'];

        $firstname = $_POST['Nome'];
        $lastname = $_POST['Cognome'];
        $email = $_POST['Email'];
        $NumeroTelefono = $_POST['NumeroTelefono'];
        $Idirizzo = $_POST['Idirizzo'];
        $Indirizzo2 = $_POST['Indirizzo2'];
        $Citta = $_POST['Citta'];
        $Provincia = $_POST['Provincia'];
        $CAP = $_POST['CAP'];
        $Stato = $_POST['Stato'];
        $position = $_POST['Posizione'];
        $permessi = $_POST['Permessi'];

        $db->update(
            'users',
            array(
                'firstname'	=> $firstname,
                'lastname'	=> $lastname,
                'email'		=> $email,
                'phone'		=> $NumeroTelefono,
                'address'	=> $Idirizzo,
                'address2'	=> $Indirizzo2,
                'city'		=> $Citta,
                'prov'		=> $Provincia,
                'cap'		=> $CAP,
                'state'		=> $Stato,
                'position'	=> $position,
                'permessi'	=> $permessi
            ),
            array('id'  =>  $id)
        );

        header("Location: volunteers.php");
    }
}

try {
    (new InsertNewUserCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
}





