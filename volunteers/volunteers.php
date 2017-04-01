<?php
require_once('../lib/generalLayout.php');
require_once('../lib/permission.php');
require_once('../lib/sqlLib.php');

$db = new DbConnection;






//table of users in the db
$usersTable='';
$users = $db->query('SELECT * FROM users ORDER BY permessi, lastname, firstname');
foreach ($users as $row) {
	if ($row['id'] != 0)	//l'utente 0 è il placeholder per la riunione che va ad occupare i turni della sera
		$usersTable.=userRow($row, $db);
}

function userRow($row, $db)  {

	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$email = $row['email'];
	$position = $row['position'];
	$id = $row['id'];
	switch ($row['permessi']) {
		case PermissionPage::ADMIN:
			$permission = "Amministratore";
			break;
		case PermissionPage::EVENING:
			$permission = "Sera";
			break;
		case PermissionPage::AFTERNOON:
			$permission = "Pomeriggio";
			break;
		case PermissionPage::MORNING:
			$permission = "Mattina";
			break;
		default:
			$permission = "No permessi";
			break;
	}

	$presenze = getPresenze($db, $row['id'], intval(date("Y")), intval(date("m")));
	$currentYear = date("Y");
	$actions = <<<LINK
<a href="volunteer_form.php?id=$id"><img src="../img/pencil.png" alt="modifica" width='15' height='15'></a>
<a href="volunteer_delete.php?id=$id" onclick="return confirm('Sei sicuro di voler eliminare $firstname $lastname?')"><img src="../img/bin.png" alt="cancella" width='15' height='15' /></a>
<a href="volunteer_detail.php?id=$id&year=$currentYear"><img src="../img/details.png" alt="details" width='15' height='15'></a>

LINK;


	$row = <<<EOF
		<tr>
			<td>$lastname</th>
			<td>$firstname</th>
			<td>$email</th>
			<td>$position</th>
			<td>$permission</td>
			<td>$presenze</td>
			<td>$actions</th>
		</tr>
EOF;
	return $row;
}

//setting the content of the page
$content = <<<HTML
<a class="btn btn-block btn-default" onclick="toggle_visibility('newVolunteerForm')">Nuovo Volontario</a>

<form action='new_volunteer.php' method="POST">

	<input type="hidden" name="psw" value="cammello">

	<div id="newVolunteerForm" style="display: none">
		<div class="row">
			<div class="form-group col-sm-3">
				<label for="Nome">Nome</label>
				<input type="text" class="form-control" id="Nome" placeholder="Nome" name="Nome">
			</div>
			<div class="form-group col-sm-3">
				<label for="Cognome">Cognome</label>
				<input type="text" class="form-control" id="Cognome" placeholder="Cognome" name="Cognome">
			</div>
			<div class="form-group col-sm-4">
				<label for="Email">Email</label>
				<input type="email" class="form-control" id="Email" placeholder="Email" name="Email">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-4">
				<label for="CodiceFiscale">Codice Fiscale</label>
				<input type="text" class="form-control" id="CodiceFiscale" placeholder="Codice Fiscale" name="CodiceFiscale">
			</div>
			<div class="form-group col-sm-2">
				<label for="NumeroTelefono">Numero di telefono</label>
				<input type="tel" class="form-control" id="NumeroTelefono" placeholder="Numero di telefono" name="NumeroTelefono">
			</div>
			<div class="form-group col-sm-4">
				<label for="DataDiNascita">Data Di Nascita</label>
				<input type="date" class="form-control" id="DataDiNascita" name="DataDiNascita">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="Idirizzo">Idirizzo</label>
				<input type="text" class="form-control" id="Idirizzo" placeholder="Idirizzo" name="Idirizzo">
			</div>
			<div class="form-group col-sm-3 col-xs-8">
				<label for="Citta">Città</label>
				<input type="text" class="form-control" id="Citta" placeholder="Città" name="Citta">
			</div>
			<div class="form-group col-sm-1 col-xs-4">
				<label for="Provincia">Provincia</label>
				<input type="text" class="form-control" id="Provincia" placeholder="Prov" name="Provincia">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="Indirizzo2">Indirizzo (riga 2)</label>
				<input type="text" class="form-control" id="Indirizzo2" placeholder="Indirizzo (riga 2)" name="Indirizzo2">
			</div>
			<div class="form-group col-sm-2 col-xs-4">
				<label for="CAP">CAP</label>
				<input type="text" class="form-control" id="CAP" placeholder="CAP" name="CAP">
			</div>
			<div class="form-group col-sm-2 col-xs-8">
				<label for="Stato">Stato</label>
				<input type="text" class="form-control" id="Stato" placeholder="Stato" name="Stato">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-2 col-xs-8">
				<label for="Permessi">Permessi</label>
				<select class="form-control" name="Permessi">
					<option value="4">Mattina</option>
					<option value="3">Pomeriggio</option>
					<option value="2">Sera</option>
					<option value="1">Amministratore</option>
				</select>
			</div>

			<div class="form-group col-sm-1 col-xs-4">
			<label for="Posizione">Posizione</label>
				<select class="form-control" name="Posizione">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
				</select>
			</div>
		</div>
		<button type="submit" class="btn btn-default">Submit</button>

	</div>
</form>
<hr />

<a href="export_csv.php" class="btn btn-default pull-right">Esporta in Excell</a>
<h1>Lista Volontari</h1>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>Cognome</th>
				<th>Nome</th>
				<th>Email</th>
				<th>Posizione</th>
				<th>Permessi</th>
				<th>Presenze ultimo mese</th>
				<th>Azioni</th>
			</tr>
		</thead>
		<tbody>
			$usersTable
		</tbody>
	</table>
</div>

HTML;



function getPresenze($db, $volunteerId, $year, $month) {
	$statement = $db->prepare(<<<TAG
SELECT c.year AS year, c.month AS month, COUNT(c.day) as count
FROM turni AS t JOIN calendar AS c ON (t.day = c.id)
  JOIN users AS u ON (t.volunteer = u.id)
WHERE u.id = :id
GROUP BY c.year, c.month
TAG
	);

	$statement->execute([':id' => $volunteerId]);

	$allVolunteerTurns = $statement->fetchAll(PDO::FETCH_ASSOC);

	foreach ($allVolunteerTurns as $row)
		if ($row['year'] == $year and $row['month'] == $month) return $row['count'];
	return 0;
}


try {
	$generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."volunteers/volunteers.php", PermissionPage::ADMIN);
	$generalLayout->yieldElem('title', "Volontari");
	$generalLayout->yieldElem('content', $content);
	$generalLayout->yieldElem('scripts', <<<EEND
<script type="text/javascript">
	function toggle_visibility(id)
	{
		var e = document.getElementById(id);
		if ( e.style.display == 'none' )
			e.style.display = 'block';
		else
			e.style.display = 'none';
	}
</script>
EEND
	);
	echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
	$e->echoAlert();
	exit;
}

