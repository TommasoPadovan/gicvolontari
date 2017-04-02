<?php
require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/GetConstraints.php');




$constraints = new GetConstraints(
    [$_GET['id'] => ['users', 'id']],
    []
);


if (!$constraints->areOk()) {
    $content = $constraints->getErrorContent();
} else {

    $db = new DbConnection();

    $user = $db->getUser($_GET['id']);


    $Nome = $user['firstname'];
    $Cognome = $user['lastname'];
    $Email = $user['email'];
    $CF = $user['CF'];
    $birthDate = $user['birthdate'];
    $NumeroTelefono = $user['phone'];
    $Indirizzo = $user['address'];
    $Indirizzo2 = $user['address2'];
    $Citta = $user['city'];
    $Provincia = $user['prov'];
    $CAP = $user['cap'];
    $Stato = $user['state'];

    $position = $user['position'];
    $positionCheck = array();
    for ($i = 1; $i <= 3; $i++)
        $positionCheck[$i] = '';
    $positionCheck[$position] = "selected=\"selected\"";


    $permessi = $user['permessi'];
    $permessiCheck = array();
    for ($i = 1; $i <= 4; $i++)
        $permessiCheck[$i] = '';
    $permessiCheck[$permessi] = "selected=\"selected\"";

    $content = <<< HTML
<h1>Modifica il profilo di $Nome $Cognome</h1>
<hr />
<form action='volunteer_edit.php' method="POST">
	<div id="editVolunteerForm">
	    <input type="hidden" name="id" value="{$user['id']}">
		<div class="row">
			<div class="form-group col-sm-3">
				<label for="Nome">Nome</label>
				<input type="text" class="form-control" id="Nome" placeholder="Nome" name="Nome" value="$Nome" >
			</div>
			<div class="form-group col-sm-3">
				<label for="Cognome">Cognome</label>
				<input type="text" class="form-control" id="Cognome" placeholder="Cognome" name="Cognome" value="$Cognome" >
			</div>
			<div class="form-group col-sm-4">
				<label for="Email">Email</label>
				<input type="email" class="form-control" id="Email" placeholder="Email" name="Email" value="$Email" >
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-4">
				<label for="CodiceFiscale">Codice Fiscale</label>
				<input type="text" class="form-control" id="CodiceFiscale" placeholder="Codice Fiscale" name="CodiceFiscale" value="$CF" >
			</div>
			<div class="form-group col-sm-2">
				<label for="NumeroTelefono">Numero di telefono</label>
				<input type="tel" class="form-control" id="NumeroTelefono" placeholder="Numero di telefono" name="NumeroTelefono" value="$NumeroTelefono" >
			</div>
			<div class="form-group col-sm-4">
				<label for="DataDiNascita">Data Di Nascita</label>
				<input type="date" class="form-control" id="DataDiNascita" placeholder="Data Di Nascita" name="DataDiNascita" value="$birthDate" >
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="Indirizzo">Indirizzo</label>
				<input type="text" class="form-control" id="Indirizzo" placeholder="Indirizzo" name="Indirizzo" value="$Indirizzo" >
			</div>
			<div class="form-group col-sm-3">
				<label for="Citta">Città</label>
				<input type="text" class="form-control" id="Citta" placeholder="Città" name="Citta" value="$Citta" >
			</div>
			<div class="form-group col-sm-1">
				<label for="Provincia">Provincia</label>
				<input type="text" class="form-control" id="Provincia" placeholder="Prov" name="Provincia" value="$Provincia" >
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="Indirizzo2">Indirizzo (riga 2)</label>
				<input type="text" class="form-control" id="Indirizzo2" placeholder="Indirizzo (riga 2)" name="Indirizzo2" value="$Indirizzo2" >
			</div>
			<div class="form-group col-sm-2">
				<label for="CAP">CAP</label>
				<input type="text" class="form-control" id="CAP" placeholder="CAP" name="CAP" value="$CAP" >
			</div>
			<div class="form-group col-sm-2">
				<label for="Stato">Stato</label>
				<input type="text" class="form-control" id="Stato" placeholder="Stato" name="Stato" value="$Stato" >
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-2">
				<label for="Permessi">Permessi</label>
				<select class="form-control" name="Permessi">
					<option value="1" {$permessiCheck[1]} >Amministratore</option>
					<option value="2" {$permessiCheck[2]} >Sera</option>
					<option value="3" {$permessiCheck[3]} >Pomeriggio</option>
					<option value="4" {$permessiCheck[4]} >Mattina</option>
				</select>
			</div>

			<div class="form-group col-sm-1">
			<label for="Posizione">Posizione</label>
				<select class="form-control" name="Posizione">
					<option value="1" {$positionCheck[1]} >1</option>
					<option value="2" {$positionCheck[2]} >2</option>
					<option value="3" {$positionCheck[3]} >3</option>
				</select>
			</div>
		</div>
		<button type="submit" class="btn btn-default">Submit</button>
		<a href="volunteers.php" class="btn btn-default">Indietro</a>

	</div>
</form>
<hr />
HTML;

}

try {
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."volunteers/volunteers.php", PermissionPage::ADMIN);
    $generalLayout->yieldElem('title', 'Modifica Utente');
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}