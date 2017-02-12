<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');

PermissionsMng::atMostAuthorizationLevel(2);

$conn=connect();




$generateAllTables='generateAllTables';		//trick malvagio per chiamare le funzioni dentro lo herdoc
//genero content
$content = <<<EOF
<h1>Turni</h1>
	{$generateAllTables($conn)}
EOF;





//general layout of one page
$generalLayout = new GeneralLayout("turns.php");

//setting the title
$generalLayout->yieldElem('title', "Turni");

//setting the title
$generalLayout->yieldElem('content', $content);


$generalLayout->pprint();










function generateAllTables($conn) {
	$monthTable='monthTable';		//trick malvagio per chiamare le funzioni dentro lo herdoc
	$monthHideLink='monthHideLink';
	$aux='';

	$result = queryThis("SELECT DISTINCT year, month FROM calendar", $conn);
	$allMonths=array();
	for ($i=0; $i<mysql_num_rows($result) ; $i++) {
		$row = mysql_fetch_assoc($result);
		array_push($allMonths, new Month( intval($row['month']), intval($row['year']) ));
	}
	foreach ($allMonths as $month) {
		if ( $month->isInFuture() );
		$aux.= <<<HTML
		<div class="table-responsive">
			<p>{$monthHideLink($month)}</p>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th>Lunedì</th>
						<th>Martedì</th>
						<th>Mercoledì</th>
						<th>Giovedì</th>
						<th>Venerdì</th>
					</tr>
				</thead>
				<tbody>
					{$monthTable($month, $conn)}
				</tbody>
			</table>
		</div>
		<hr />
HTML;
	}
	return $aux;
}





function monthTable(Month $month, $conn) {
	//genero contenuto della tabella dei giorni
	$workingDays = $month->getAllWorking();
	$firstDay = $month->dayOfWeek($workingDays[0]);

	$dayTable = "<tr>\n";
	for ($i=0; $i < $firstDay-1 ; $i++) 
		$dayTable.="<td></td>\n";
	for ($i=$firstDay; $i <= 5 ; $i++) {
		$content = calendarContent($month, $i, $conn);
		$dayTable.="<td>$content</td>\n";
	}  

	$firstDayOf2Week=6-$firstDay;
	for ($i=6-$firstDay; $i < sizeof($workingDays) ; $i++) { 
		if (($i-$firstDayOf2Week)%5==0) $dayTable.= "</tr><tr>\n";
		$content = calendarContent($month, $workingDays[$i], $conn);
		$dayTable.="<td>$content</td>\n";

	}
	$dayTable.= "</tr>\n";
	return $dayTable;
}

function monthHideLink(Month $month) {
	return $month->getMonthName().' '.$month->getYear();
}



function calendarContent(Month $month, $i, $conn) {
	$turniVolontari = queryThis("
		SELECT *
		FROM turni
		WHERE day IN (
			SELECT * FROM (
				SELECT id
				FROM calendar
				WHERE year = '{$month->getYear()}' AND month = '{$month->getMonth()}' AND day = '$i'
			) as T
		)
	",$conn);


	$userID=$_SESSION['id'];
	$user = mysql_fetch_assoc( queryThis("SELECT * FROM users WHERE id = $userID",$conn) );
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





	for ($j=0; $j < mysql_num_rows($turniVolontari) ; $j++) { 
		$row=mysql_fetch_assoc($turniVolontari);
		switch ($row['task']) {
			case 'fiabe':
				$fiabe[$row['position']]=getUserName($row['volunteer'], $conn);
				if ($row['volunteer'] == $_SESSION['id'] or $_SESSION['permessi']<=1) $fiabe[$row['position']].=" <a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\"><img border='0' alt='cancella prenotazione' src='bin.png' width='15' height='15'></a>";
				break;
			case 'oasi':
				$oasi[$row['position']]=getUserName($row['volunteer'], $conn);
				if ($row['volunteer'] == $_SESSION['id'] or $_SESSION['permessi']<=1) $oasi[$row['position']].=" <a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\"><img border='0' alt='cancella prenotazione' src='bin.png' width='15' height='15'></a>";
				break;
			case 'clown':
				$clown[$row['position']]=getUserName($row['volunteer'], $conn);
				if ($row['volunteer'] == $_SESSION['id'] or $_SESSION['permessi']<=1) $clown[$row['position']].=" <a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\"><img border='0' alt='cancella prenotazione' src='bin.png' width='15' height='15'></a>";
				break;
		}
	}

	return <<<END
	<p><strong>$i</strong></p>
	<hr />
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<td>{$fiabe[1]}</td>
			<td>{$oasi[1]}</td>
			<td>{$clown[1]}</td>
		</tr><tr>
			<td>{$fiabe[2]}</td>
			<td>{$oasi[2]}</td>
			<td>{$clown[2]}</td>
		</tr><tr>
			<td>{$fiabe[3]}</td>
			<td>{$oasi[3]}</td>
			<td>{$clown[3]}</td>
		</tr>
	</table>
END;
}

?>