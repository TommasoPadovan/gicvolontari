<?php
require_once('../lib/generalLayout.php');
require_once('../lib/permissionsMng.php');
require_once('../lib/sqlLib.php');

$db = new DbConnection();

$user = $db->getUser($_GET['id']);


$Nome = $user['firstname'];
$Cognome = $user['lastname'];
$Email = $user['email'];
$NumeroTelefono = $user['phone'];
$Idirizzo = $user['address'];
$Indirizzo2 = $user['address2'];
$Citta = $user['city'];
$Provincia = $user['prov'];
$CAP = $user['cap'];
$Stato = $user['state'];

$position = $user['position'];
$positionCheck=array();
for ($i=1; $i<=3; $i++)
    $positionCheck[$i] = '';
$positionCheck[$position] = "selected=\"selected\"";


$permessi = $user['permessi'];
$permessiCheck=array();
for ($i=1; $i<=4; $i++)
    $permessiCheck[$i] = '';
$permessiCheck[$permessi] = "selected=\"selected\"";

$content = <<< HTML
<h1>Modifica il profilo di $Nome $Cognome</h1>
<hr />
<form action='volunteer_edit.php' method="POST">
    <input type="hidden" name="id" value="{$_GET['id']}">
	<div class="row" id="newVolunteerForm">
		<div class="col-sm-6">
			<div class="form-group">
				<label for="Nome">Nome</label>
				<input type="text" class="form-control" id="Nome" value="$Nome" name="Nome">
			</div>
			<div class="form-group">
				<label for="Cognome">Cognome</label>
				<input type="text" class="form-control" id="Cognome" value="$Cognome" name="Cognome">
			</div>
			<div class="form-group">
				<label for="Email">Email</label>
				<input type="email" class="form-control" id="Email" value="$Email" name="Email">
			</div>
			<div class="form-group">
				<label for="NumeroTelefono">Numero di telefono</label>
				<input type="tel" class="form-control" id="NumeroTelefono" value="$NumeroTelefono" name="NumeroTelefono">
			</div>
			<div class="form-group">
				<label for="Posizione">Posizione</label>
				<div class="select">
					<select class="form-control" name="Posizione">
						<option {$positionCheck[1]} value="1">1</option>
						<option {$positionCheck[2]} value="2">2</option>
						<option {$positionCheck[3]} value="3">3</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="Permessi">Permessi</label>
				<div class="select">
					<select class="form-control" name="Permessi">
						<option {$permessiCheck[1]} value="1">Amministratore</option>
						<option {$permessiCheck[2]} value="2">Sera</option>
						<option {$permessiCheck[3]} value="3">Pomeriggio</option>
						<option {$permessiCheck[4]} value="4">Mattina</option>
					</select>
				</div>
			</div>

		</div>
		<div class="col-sm-6">
			<div class="form-group">
				<label for="Idirizzo">Idirizzo</label>
				<input type="text" class="form-control" id="Idirizzo" value="$Idirizzo" name="Idirizzo">
			</div>
			<div class="form-group">
				<label for="Indirizzo2">Indirizzo (riga 2)</label>
				<input type="text" class="form-control" id="Indirizzo2" value="$Indirizzo2" name="Indirizzo2">
			</div>
			<div class="form-group">
				<label for="Citta">Città</label>
				<input type="text" class="form-control" id="Citta" value="$Citta" name="Citta">
			</div>
			<div class="form-group">
				<label for="Provincia">Provincia</label>
				<input type="text" class="form-control" id="Provincia" value="$Provincia" name="Provincia">
			</div>
			<div class="form-group">
				<label for="CAP">CAP</label>
				<input type="text" class="form-control" id="CAP" value="$CAP" name="CAP">
			</div>
			<div class="form-group">
				<label for="Stato">Stato</label>
				<input type="text" class="form-control" id="Stato" value="$Stato" name="Stato">
			</div>
		</div>
	</div>
	<button type="submit" class="btn btn-default">Submit</button>
    <a class="btn btn-default" href="volunteers.php">Indietro</a>

</form>
<hr />
HTML
;

try {
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."volunteers/volunteers.php", PermissionPage::ADMIN);
    $generalLayout->yieldElem('title', 'Modifica Utente');
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}