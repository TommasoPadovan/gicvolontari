<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');

PermissionsMng::atMostAuthorizationLevel(2);

$db = new DbConnection();




$generateAllTables='generateAllTables';		//trick malvagio per chiamare le funzioni dentro lo herdoc
//genero content
$content = <<<EOF
<h1>Turni</h1>
	{$generateAllTables($db)}
EOF;





//general layout of one page
$generalLayout = new GeneralLayout("turns.php");

//setting the title
$generalLayout->yieldElem('title', "Turni");

//setting the title
$generalLayout->yieldElem('content', $content);


$generalLayout->pprint();


echo <<<EEND
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
EEND;









function generateAllTables($db) {
	$monthTable='monthTable';		//trick malvagio per chiamare le funzioni dentro lo herdoc
	$monthHideLink='monthHideLink';
	$aux='';

	$yearMonths = $db->query("SELECT DISTINCT year, month FROM calendar");
	$allMonths=array();
	foreach ($yearMonths as $row) {
		array_push($allMonths, new Month( intval($row['month']), intval($row['year']) ));
	}
	foreach ($allMonths as $month) {
		if ( $month->isInFuture() );
		$aux.= <<<HTML
		<p>{$monthHideLink($month)}</p>
		<div id="month{$month->getMonth()}-{$month->getYear()}" style="display: none">
			<!--<div class="row">
				<div class="col-md-5ths"><strong>Lunedì</strong></div>
				<div class="col-md-5ths"><strong>Martedì</strong></div>
				<div class="col-md-5ths"><strong>Mercoledì</strong></div>
				<div class="col-md-5ths"><strong>Giovedì</strong></div>
				<div class="col-md-5ths"><strong>Venerdì</strong></div>
			</div>
			<hr />-->
			{$monthTable($month, $db)}
			<hr />
		</div>
HTML;
	}
	return $aux;
}





function monthTable(Month $month, $db) {
	//genero contenuto della tabella dei giorni
	$workingDays = $month->getAllWorking();
	$firstDay = $month->dayOfWeek($workingDays[0]);

	$dayTable = "<div class='row'>\n";
	for ($i=0; $i < $firstDay-1 ; $i++) 
		$dayTable.="<div class='col-md-5ths'>.</div>\n";
	for ($i=$firstDay; $i <= 5 ; $i++) {
		$content = calendarContent($month, $i-$firstDay+1, $db);
		$dayTable.="<div class='col-md-5ths'>$content</div>\n";
	}  

	$firstDayOf2Week=6-$firstDay;
	for ($i=6-$firstDay; $i < sizeof($workingDays) ; $i++) { 
		if (($i-$firstDayOf2Week)%5==0) $dayTable.= "</div><div class='row'>\n";
		$content = calendarContent($month, $workingDays[$i], $db);
		$dayTable.="<div class='col-md-5ths'>$content</div>\n";

	}
	$dayTable.= "</div>\n";
	return $dayTable;
}

function monthHideLink(Month $month) {
	return "<a class=\"btn btn-block btn-default\"  onclick=\"toggle_visibility('month{$month->getMonth()}-{$month->getYear()}')\">".$month->getMonthName().' '.$month->getYear()."</a>";
}





function calendarContent(Month $month, $i, $db) {
	$turniVolontari = $db->prepare('
		SELECT *
		FROM turni
		WHERE day IN (
			SELECT * FROM (
				SELECT id
				FROM calendar
				WHERE year = :year AND month = :month AND day = :day
			) as T
		)
	');
	$turniVolontari->execute(array(
		':year'		=>	$month->getYear(),
		':month'	=>	$month->getMonth(),
		':day'		=>	$i
	));
	$turniVolontari = $turniVolontari->fetchAll(PDO::FETCH_ASSOC);

	$userID=$_SESSION['id'];
	$user = $db->select('users', array('id' => $userID));
	$user = $user[0];

	$userPosition = intval($user['position']);

	$fiabe=array(
		1=>"fiabe 1",
		2=>"fiabe 2",
		3=>"fiabe 3"
	);
	$oasi=array(
		1=>"oasi 1",
		2=>"oasi 2",
		3=>"oasi 3"
	);
	$clown=array(
		1=>"clown 1",
		2=>"clown 2",
		3=>"clown 3"
	);
	$fiabe[$userPosition] = "<a href=\"process_prenota.php?task=fiabe&position=$userPosition&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">fiabe $userPosition</a>";
	$oasi[$userPosition] = "<a href=\"process_prenota.php?task=oasi&position=$userPosition&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">oasi $userPosition</a>";
	$clown[$userPosition] = "<a href=\"process_prenota.php?task=clown&position=$userPosition&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">clown $userPosition</a>";





	foreach ($turniVolontari as $row) { 
		switch ($row['task']) {
			case 'fiabe':
				$fiabe[$row['position']]=$db->getUserName($row['volunteer']);
				if ($row['volunteer'] == $_SESSION['id'] or $_SESSION['permessi']<=1) $fiabe[$row['position']].=" <a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\"><img border='0' alt='cancella prenotazione' src='bin.png' width='15' height='15'></a>";
				break;
			case 'oasi':
				$oasi[$row['position']]=$db->getUserName($row['volunteer']);
				if ($row['volunteer'] == $_SESSION['id'] or $_SESSION['permessi']<=1) $oasi[$row['position']].=" <a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\"><img border='0' alt='cancella prenotazione' src='bin.png' width='15' height='15'></a>";
				break;
			case 'clown':
				$clown[$row['position']]=$db->getUserName($row['volunteer']);
				if ($row['volunteer'] == $_SESSION['id'] or $_SESSION['permessi']<=1) $clown[$row['position']].=" <a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\"><img border='0' alt='cancella prenotazione' src='bin.png' width='15' height='15'></a>";
				break;
		}
	}

	

	$nVolunteer = $db->select('calendar', array('year' => $month->getYear(), 'month' =>  $month->getMonth()))[0]['maxVolunteerNumber'];


	if ($nVolunteer==3)
		$thirdRow = "</tr><tr>
				<td class='fiabe'>{$fiabe[3]}</td>
				<td class='oasi'>{$oasi[3]}</td>
				<td class='clown'>{$clown[3]}</td>";
		else $thirdRow='';

	$numeroENomeGiorno = "<strong>$i</strong> - " . $month->getDayName($i);

	return <<<END
	<p>$numeroENomeGiorno</p>
	<hr />
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th class='fiabe'>Fiabe</th>
				<th class='oasi'>Oasi</th>
				<th class='clown'>Clown</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='fiabe'>{$fiabe[1]}</td>
				<td class='oasi'>{$oasi[1]}</td>
				<td class='clown'>{$clown[1]}</td>
			</tr><tr>
				<td class='fiabe'>{$fiabe[2]}</td>
				<td class='oasi'>{$oasi[2]}</td>
				<td class='clown'>{$clown[2]}</td>
			$thirdRow
			</tr>
		</tbody>
	</table>
END;
}

?>



