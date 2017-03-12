<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 07/03/2017
 * Time: 21:05
 */
require_once('lib/generalLayout.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');
require_once('lib/permissionString.php');

$db = new DbConnection();


function content(DbConnection $db) {
    $aux=adminAddMonthButton();
    $aux.= <<<EOF
	<h1>Turni</h1>
EOF;
    $aux.= generateTable($db);
    return $aux;
}


function adminAddMonthButton() {
    return (new PermissionString([
        PermissionPage::ADMIN => "<a href='events.php' class='btn btn-default pull-right'>Gestisci Mesi</a>"
    ]))->out();
}

/**
 * Genera i bottoni per visualizzare nascondere le tabelle dei turni.
 * La grafica dei bottoni è delegata a monthHideLink
 * La politica di visualizzazione di default o meno dei mesi è delegata a isVisible
 * La creazione vera e propria della tabella è delegata a monthTable
 * @param DbConnection $db
 * @return string
 */
function generateTable(DbConnection $db) {
    $yearMonths = $db->query("SELECT DISTINCT year, month FROM calendar");
    $allMonths=array();
    foreach ($yearMonths as $row)
        array_push($allMonths, new Month( intval($row['month']), intval($row['year']) ));

    $now = new Month(date('m'), date("Y"));

    $aux='';
    foreach ($allMonths as $month) {
        $monthString= '<p>'.monthHideLink($month).'</p>';
        $monthString.="
            <div id=\"month{$month->getMonth()}-{$month->getYear()}\" style=\"display: " . isVisible($month) . "\">
                <hr />
                " . monthTable($month, $db) . "
                <hr />
            </div>";

        if ($month->isInFuture()) {
            $aux.=$monthString;
        } else {
            $aux.=(new PermissionString([PermissionPage::ADMIN => $monthString]))->out();
        }
    }

    return $aux;
}


/**
 * Disegna il bottone per visualizzare nascondere le tabelle dei mesi
 * @param Month $month
 * @return string
 */
function monthHideLink(Month $month) {
    return "<a class=\"btn btn-block btn-default\"  onclick=\"toggle_visibility('month{$month->getMonth()}-{$month->getYear()}')\">".$month->getMonthName().' '.$month->getYear()."</a>";
}

/**
 * decide se un mese è o meno visibile di default
 * @param Month $month
 * @return string
 */
function isVisible(Month $month) {
    if ($month->isNow()) return "block";
    return "none";
}


/**
 *
 * @param Month $month
 * @param DbConnection $db
 * @return string
 */
function monthTable(Month $month, DbConnection $db) {
    $workingDays = $month->getAllWorking();
    $firstDay = $month->dayOfWeek($workingDays[0]);
    $padding = $firstDay-1;

    $dayTable = "<div class='row'>\n";
    for ($i=0; $i<$padding; $i++)
        $dayTable.="<div class='col-md-5ths'>.</div>\n";
    foreach ($workingDays as $day) {
        if ($month->dayOfWeek($day)==1 && $day!=min($workingDays)) $dayTable.= "<div class='row'>\n";
        $dayTable .= "<div class='col-md-5ths'>" . calendarContent($month, $day, $db) . "</div>\n";
        if ($month->dayOfWeek($day)==5 && $day!=max($workingDays)) $dayTable.= "</div>\n";
    }

    $dayTable.= "</div>\n";
    return $dayTable;
}


//function calendarContent(Month $month, $day, DbConnection $db) {
//    return "{$month->getYear()}-{$month->getMonth()}-$day";
//}

function calendarContent (Month $month, $day, DbConnection $db) {
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
        ':day'		=>	$day
    ));
    $turniVolontari = $turniVolontari->fetchAll(PDO::FETCH_ASSOC);

    $userID=$_SESSION['id'];
    $user = $db->getUser($userID);

    $userPosition = intval($user['position']);

    //tabella vuota solo scritte niente link
    $dayTurns = [
        'fiabe' => [1 => 'fiabe 1', 2 => 'fiabe 2', 3 => 'fiabe 3'],
        'oasi' => [1 => 'oasi 1', 2 => 'oasi 2', 3 => 'oasi 3'],
        'clown' => [1 => 'clown 1', 2 => 'clown 2', 3 => 'clown 3']
    ];

    //link se posso prenotarmi + link di prenotazione solo per l'admin
    foreach ($dayTurns as $task => $value) {
        if ($userPosition <= 2)
            $dayTurns[$task][2] = reservationLink($db, $task, 2, $month, $day).
                adminSelectUserSelect($db, $task, 2, $month, $day);

        if ($userPosition <= 1)
            $dayTurns[$task][1] = reservationLink($db, $task, 1, $month, $day).
                adminSelectUserSelect($db, $task, 1, $month, $day);
    }

    //nomi di quelli già prenotati
    foreach ($turniVolontari as $row) {
        $dayTurns[$row['task']][$row['position']] = $db->getUserName($row['volunteer'])
            .eventuallyAddDelete($row, $dayTurns[$row['task']]);
    }

    $nVolunteer = $db->select('calendar', array('year' => $month->getYear(), 'month' =>  $month->getMonth()))[0]['maxVolunteerNumber'];


    if ($nVolunteer==3)
        $thirdRow = "</tr><tr>
				<td class='fiabe'>{$dayTurns['fiabe'][3]}</td>
				<td class='oasi'>{$dayTurns['oasi'][3]}</td>
				<td class='clown'>{$dayTurns['clown'][3]}</td>";
    else $thirdRow='';

    $numeroENomeGiorno = "<strong>$day</strong> - " . $month->getDayName($day);

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
				<td class='fiabe'>{$dayTurns['fiabe'][1]}</td>
				<td class='oasi'>{$dayTurns['oasi'][1]}</td>
				<td class='clown'>{$dayTurns['clown'][1]}</td>
			</tr><tr>
				<td class='fiabe'>{$dayTurns['fiabe'][2]}</td>
				<td class='oasi'>{$dayTurns['oasi'][2]}</td>
				<td class='clown'>{$dayTurns['clown'][2]}</td>
			$thirdRow
			</tr>
		</tbody>
	</table>
END;
}


function reservationLink(DbConnection $db, $task, $position, Month $month, $day) {
    return "<a href=\"process_prenota.php?task=$task&position=$position&year={$month->getYear()}&month={$month->getMonth()}&day=$day\">$task $position</a>";
}


function adminSelectUserSelect(DbConnection $db, $task, $position, Month $month, $day) {
    $y = $month->getYear();
    $m = $month->getMonth();

    $selectString = "<select name='user'>";
    $allUsers = $db->select('users');
    foreach	($allUsers as $user)
        $selectString.= "<option value='{$user['id']}'>{$user['lastname']}</option> \n";
    $selectString.='</select>';

    return (new PermissionString([
        PermissionPage::ADMIN => "
		<a onclick='toggle_visibility(\"adduser-$task-$position-$y-$m-$day\")'><img src=\"img/add.png\" alt='add user' width='20' height='20'></a>
		<div style='display: none' id='adduser-$task-$position-$y-$m-$day'>
			<form method='POST' action='admin_add_user.php'>
				<input type='hidden' name='task' value='$task' />
				<input type='hidden' name='position' value='$position' />
				<input type='hidden' name='year' value='$y' />
				<input type='hidden' name='month' value='$m' />
				<input type='hidden' name='day' value='$day' />
				$selectString
				<input type='submit' class='btn btn-xs'>
			</form>
		</div>
		"
    ]))->out();
}


function eventuallyAddDelete($row, $taskColumn) {
    if ($row['volunteer'] == $_SESSION['id'] or $_SESSION['permessi']<=1)
        return "<a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\" >
                <img border='0' alt='cancella prenotazione' src='img/bin.png' width='15' height='15'>
            </a>";
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