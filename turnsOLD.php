<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');
require_once('lib/permissionString.php');


$db = new DbConnection();


function content($db) {
	$generateTable='generateTable';		//trick malvagio per chiamare le funzioni dentro lo herdoc
	return <<<EOF
	<h1>Turni</h1>
	{$generateTable($db)}
EOF;
}



function generateTable(DbConnection $db) {
	$monthTable='monthTable';		//trick malvagio per chiamare le funzioni dentro lo herdoc
	$monthHideLink='monthHideLink';
	$aux='';

	$yearMonths = $db->query("SELECT DISTINCT year, month FROM calendar");
	$allMonths=array();
	foreach ($yearMonths as $row)
		array_push($allMonths, new Month( intval($row['month']), intval($row['year']) ));

	$now = new Month(date('m'), date("Y"));

	foreach ($allMonths as $month) {
		if ($now->getMonth() != $month->getMonth() || $now->getYear() != $month->getYear() )
			$display = 'none';
		else
			$display = 'block';

		$monthString = <<<HTML
		<p>{$monthHideLink($month)}</p>
		<div id="month{$month->getMonth()}-{$month->getYear()}" style="display: $display">
			<hr />
			{$monthTable($month, $db)}
			<hr />
		</div>
HTML;
		if ( $month->isInFuture() ) {
			$aux.=$monthString;
		} else {
			$permissionMonthString = new PermissionString(array(PermissionPage::ADMIN => $monthString));
			$aux.=$permissionMonthString->out();
		}
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

/**
 * @param Month $month
 * @return string
 */
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

	if ($userPosition == 1) {
		$fiabe[1] = "<a href=\"process_prenota.php?task=fiabe&position=1&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">fiabe 1</a>"
			.adminSelectUserSelect($db, 'fiabe', 1, $month->getYear(), $month->getMonth(), $i);
		$oasi[1] = "<a href=\"process_prenota.php?task=oasi&position=1&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">oasi 1</a>"
			.adminSelectUserSelect($db, 'oasi', 1, $month->getYear(), $month->getMonth(), $i);
		$clown[1] = "<a href=\"process_prenota.php?task=clown&position=1&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">clown 1</a>"
			.adminSelectUserSelect($db, 'clown', 1, $month->getYear(), $month->getMonth(), $i);
		$fiabe[2] = "<a href=\"process_prenota.php?task=fiabe&position=2&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">fiabe 2</a>"
			.adminSelectUserSelect($db, 'fiabe', 2, $month->getYear(), $month->getMonth(), $i);
		$oasi[2] = "<a href=\"process_prenota.php?task=oasi&position=2&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">oasi 2</a>"
			.adminSelectUserSelect($db, 'oasi', 2, $month->getYear(), $month->getMonth(), $i);
		$clown[2] = "<a href=\"process_prenota.php?task=clown&position=2&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">clown 2</a>"
			.adminSelectUserSelect($db, 'clown', 2, $month->getYear(), $month->getMonth(), $i);
	}
	if ($userPosition == 2) {
		$fiabe[2] = "<a href=\"process_prenota.php?task=fiabe&position=2&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">fiabe 2</a>"
			.adminSelectUserSelect($db, 'fiabe', 2, $month->getYear(), $month->getMonth(), $i);
		$oasi[2] = "<a href=\"process_prenota.php?task=oasi&position=2&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">oasi 2</a>"
			.adminSelectUserSelect($db, 'oasi', 2, $month->getYear(), $month->getMonth(), $i);
		$clown[2] = "<a href=\"process_prenota.php?task=clown&position=2&year={$month->getYear()}&month={$month->getMonth()}&day=$i\">clown 2</a>"
			.adminSelectUserSelect($db, 'clown', 2, $month->getYear(), $month->getMonth(), $i);
	}




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


function adminSelectUserSelect(DbConnection $db, $task, $position, $year, $month, $day) {

	$selectString = "<select name='user'>";
	$allUsers = $db->select('users');
	foreach	($allUsers as $user)
		$selectString.= "<option value='{$user['id']}'>{$user['lastname']}</option> \n";
	$selectString.='</select>';

	return (new PermissionString([
		PermissionPage::ADMIN => "
		<a onclick='toggle_visibility(\"adduser-$task-$position-$year-$month-$day\")'><img src=\"img/add.png\" alt='add user' width='20' height='20'></a>
		<div style='display: none' id='adduser-$task-$position-$year-$month-$day'>
			<form method='POST' action='admin_add_user.php'>
				<input type='hidden' name='task' value='$task' />
				<input type='hidden' name='position' value='$position' />
				<input type='hidden' name='year' value='$year' />
				<input type='hidden' name='month' value='$month' />
				<input type='hidden' name='day' value='$day' />
				$selectString
				<input type='submit' class='btn btn-xs'>
			</form>
		</div>
		"
	]))->out();
}



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

try {
	//general layout of one page
	$generalLayout = new GeneralLayout("turns.php", PermissionPage::EVENING);

	//setting the title
	$generalLayout->yieldElem('title', "Turni");

	//setting the title
	$generalLayout->yieldElem('content', content($db));


	echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}



