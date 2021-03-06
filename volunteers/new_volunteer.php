<?php
session_start();
require_once('../lib/sqlLib.php');
require_once('../lib/permissionsMng.php');
require_once('../lib/command.php');

class EditUserCommand extends Command {

	public function __construct($permission) {
		parent::__construct($permission);
	}

	protected function template() {
		$db = new DbConnection();

		$firstname = $_POST['Nome'];
		$lastname = $_POST['Cognome'];
		$email = $_POST['Email'];
		$psw = md5( "cammello" );
		$CodiceFiscale = $_POST['CodiceFiscale'];
		$NumeroTelefono = $_POST['NumeroTelefono'];
		$DataDiNascita = $_POST['DataDiNascita'];
		$Idirizzo = $_POST['Indirizzo'];
		$Indirizzo2 = $_POST['Indirizzo2'];
		$Citta = $_POST['Citta'];
		$Provincia = $_POST['Provincia'];
		$CAP = $_POST['CAP'];
		$Stato = $_POST['Stato'];
		$position = $_POST['Posizione'];
		$permessi = $_POST['Permessi'];



		$db->insert('users', array(
			'firstname'	=> $firstname,
			'lastname'	=> $lastname,
			'email'		=> $email,
			'CF'		=> $CodiceFiscale,
			'birthdate' => $DataDiNascita,
			'psw'		=> $psw,
			'phone'		=> $NumeroTelefono,
			'address'	=> $Idirizzo,
			'address2'	=> $Indirizzo2,
			'city'		=> $Citta,
			'prov'		=> $Provincia,
			'cap'		=> $CAP,
			'state'		=> $Stato,
			'position'	=> $position,
			'permessi'	=> $permessi
		));

		header("Location: volunteers.php");
	}
}

try {
	(new EditUserCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e){
	$e->echoAlert();
}





