<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');



$db = new DbConnection();


//gestisce l'immissione di un nuovo mese nel calendario
if ( isset($_POST['submit']) ) {
	$date = explode('-', $_POST['Mese']);
	$month = new Month(intval($date[1]),intval($date[0]));

	if ($month->isInFuture()) {							//controlla che il mese indicato sia nel futuro e non nel passato
		if (!isInDB($month,$db)) {						//controlla che il mese indicato non sia già presente in DB
			for ($i=1; $i <= $month->dayThisMonth(); $i++) {
				$db->insert('calendar', array(
					'year'					=>	$month->getYear(),
					'month'					=>	$month->getMonth(),
					'day'					=>	$i,
					'maxVolunteerNumber'	=>	$_POST['nVolontari']
				));
			}
			echo "<script>alert(\"Mese aggiunto con successo\")</script>";
		} else {
			echo "<script>alert(\"Il mese che hai inserito è gia abilitato alle iscrizioni per i turni\")</script>";
		}
	} else {
		echo "<script>alert(\"Il mese che hai inserito è nel passato, deve essere nel futuro\")</script>";
	}
}


function isInDB(Month $m, $db) {
	$dates = $db->select('calendar', array(
		'year' => $m->getYear(),
		'month' => $m->getMonth()
	));
	return count($dates) != 0;
}







$monthTable='';
$months = $db->query("SELECT DISTINCT year, month, maxVolunteerNumber FROM calendar");
foreach ($months as $row) {
	$monthTable.=monthRow($row);
}
function monthRow($row) {
	$year = $row['year'];
	$month = $row['month'];
	$nvolontari=$row['maxVolunteerNumber'];
	$actions = <<<LINK
<a onclick="return confirm('Sei sicuro di voler eliminare il mese selezionato? Tutte le prenotazioni e gli eventi associati verranno cancellati')" href='delete_month.php?year=$year&month=$month'>
	<img src="img/bin.png" alt="cancella" width="15" height="15">
</a>
LINK;

	$row = <<<EOF
		<tr>
			<td>$month/$year</th>
			<td>$nvolontari</td>
			<td>$actions</th>
		</tr>
EOF;
	return $row;
}

//form per inserire il mese
$content = <<<HTML
<h1>Gestione Eventi</h1>

<div style="none">
	<h2>Nuovo Mese</h2>
	<p> Selezionare dal picker qui sotto il mese per abilitare le iscrizioni ai turni di quel mese</p>
	<form action='#' method="POST">
		<div class="row">
			<div class="form-group col-sm-3">
				<label for="Mese">Mese</label>
				<input class="form-control" type="month" name="Mese">
			</div>
			<div class="col-sm-3">
				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="radio" name="nVolontari" value="2" checked>
						Due volontari per turno
					</label>
				</div>
				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" type="radio" name="nVolontari" value="3">
						Tre volontari per turno
					</label>
				</div>
			</div>
		</div>
		<button type="submit" name="submit" value="submit" class="btn btn-default">Submit</button>
	</form>

	<h2>Lista Mesi abilitati</h2>
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th>Mese</th>
					<th># volontari</th>
					<th>Azioni</th>
				</tr>
			</thead>
			<tbody>
				$monthTable
			</tbody>
		</table>
	</div>
</div>

HTML;






try {
	$generalLayout = new GeneralLayout("events.php", PermissionPage::ADMIN);
	//setting the title
	$generalLayout->yieldElem('title', "Gestione Eventi");

	//setting the body
	$generalLayout->yieldElem('content', $content);

	echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
	exit;
}

