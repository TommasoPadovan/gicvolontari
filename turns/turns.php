<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 07/03/2017
 * Time: 21:05
 */

//se uno cerca di accedere direttamente turni senza specificare mettiamo per primo il mese attuale

require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/permissionString.php');

$db = new DbConnection();


function content(DbConnection $db) {
    $currentMonth = date("Y-m");

    if ( count($db->select('calendar')) != 0 )
        $maxMonth = getMaxMonth($db);
    else
        $maxMonth=$currentMonth;

    if (isset($_GET['Mese']))
        $shownMonth = $_GET['Mese'];
    else
        $shownMonth = date("Y-m");

    $nextMonthButton = nextMonthButton($shownMonth, $maxMonth);
    $prevMonthButton = prevMonthButton($shownMonth, $currentMonth);

    $monthObj = Month::getMonthFromInternational($shownMonth);

    //calculating number of reservation left this month
    $reservationsLeft = 2;
    $d = explode('-', $shownMonth);
    if ($d[1] == date("m")) $reservationsLeft++;
    $volunteerTurnThisMonth = $db->prepare("SELECT * FROM turni AS t JOIN calendar as c ON t.day = c.id WHERE c.year = :year AND c.month = :month AND t.volunteer = :userID");
    $volunteerTurnThisMonth->execute(array(
        ':year' => $d[0],
        ':month' => $d[1],
        ':userID' => $_SESSION['id']
    ));
    $reservationsLeft -= $volunteerTurnThisMonth->rowCount();

    $aux=adminAddMonthButton();
    $aux.=adminCsvExportButton($shownMonth);

    $puoiPrenotartiAncoraTotVolte = new PermissionString([
        PermissionPage::EVENING => <<<WELL
        <div class="pull-right well well-sm">
            Puoi prenotarti ancora <strong>$reservationsLeft</strong> volte questo mese
        </div>
WELL
    ]);

    $aux.= <<<EOF
	<h1>Turni</h1>
	{$puoiPrenotartiAncoraTotVolte->out()}
	<p>Selezionare il mese dal calendario qui sotto per iscriversi ai turni serali</p>
	<form action='#' method="get">
		<div class="row">
			<div class="form-group col-sm-3 col-xs-12">
				<input class="form-control" value="$shownMonth" type="month" name="Mese" min="$currentMonth" max="$maxMonth">
			</div>
			<div class="col-sm-2 col-xs-12">
                <button type="submit" value="Vai al Mese" class="btn btn-default btn-block">Vai al Mese</button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1 col-xs-3">
                $prevMonthButton
            </div>
            <div class="col-sm-3 col-xs-6 text-center">
	            <h2>{$monthObj->getMonthName()} {$monthObj->getYear()}</h2>
	        </div>
            <div class="col-sm-1 col-xs-3">
                $nextMonthButton
            </div>
		</div>
	</form>
EOF;
    if (isset($shownMonth))
        $aux .= generateTable($monthObj, $db);
    else
        $aux.= "Seleziona un mese";
    return $aux;
}


function nextMonthButton($shownMonth, $maxMonth) {
    $shownMonth = explode('-', $shownMonth);
    $maxMonth = explode('-', $maxMonth);

    $shownMonth[1]++;
    if ($shownMonth[1]>12) {
        $shownMonth[1] = 1;
        $shownMonth[0]++;
    }
    if ($shownMonth[0]<=$maxMonth[0] && $shownMonth[1]<=$maxMonth[1]) {
        $targetMonth = $shownMonth[0].'-'.str_pad($shownMonth[1], 2, '0', STR_PAD_LEFT);

        return "<a href='turns.php?Mese=$targetMonth' class='btn btn-default btn-block'><img src=\"../img/rArrow.png\" width='30em' height='50em' alt='vai al mese successivo'></a>";
    }
    return "<a class='btn btn-default disabled btn-block'><img src=\"../img/rArrow.png\" width='30em' height='50em' alt='vai al mese successivo'></a>";
}

function prevMonthButton($shownMonth, $currentMonth) {
    $shownMonth = explode('-', $shownMonth);
    $currentMonth = explode('-', $currentMonth);

    $shownMonth[1]--;
    if ($shownMonth[1]<1) {
        $shownMonth[1] = 12;
        $shownMonth[0]--;
    }
    if ($shownMonth[0]>=$currentMonth[0] && $shownMonth[1]>=$currentMonth[1]) {
        $targetMonth = $shownMonth[0].'-'.str_pad($shownMonth[1], 2, '0', STR_PAD_LEFT);

        return "<a href='turns.php?Mese=$targetMonth' class='btn btn-default btn-block'><img src=\"../img/lArrow.png\" width='30em' height='50em' alt='vai al mese precedente'></a>";
    }
    return "<a class='btn btn-default disabled btn-block'><img src=\"../img/lArrow.png\" width='30em' height='50em' alt='vai al mese precedente'></a>";
}


function getMaxMonth(DbConnection $db) {
    $yearMonths = $db->query("SELECT year, month FROM calendar ORDER BY year, month DESC")->fetchAll(PDO::FETCH_ASSOC);
    $year = $yearMonths[0]['year'];
    $month = str_pad($yearMonths[0]['month'], 2, '0', STR_PAD_LEFT);
    return "$year-$month";
}


function adminAddMonthButton() {
    return (new PermissionString([
        PermissionPage::ADMIN => "<a href='events.php' class='btn btn-default pull-right'>Gestisci Mesi</a>"
    ]))->out();
}

function adminCsvExportButton($shownMonth) {
    return (new PermissionString([
        PermissionPage::ADMIN => "<a href='export_csv.php?Mese=$shownMonth' class='pull-right btn btn-default'>Esporta In Excel</a>"
    ]))->out();
}

/**
 * Genera i bottoni per visualizzare nascondere le tabelle dei turni.
 * La grafica dei bottoni è delegata a monthHideLink
 * La politica di visualizzazione di default o meno dei mesi è delegata a isVisible
 * La creazione vera e propria della tabella è delegata a monthTable
 * @param Month $month
 * @param DbConnection $db
 * @return string
 */
function generateTable(Month $month, DbConnection $db) {
    if (count($db->select('calendar', ['year' => $month->getYear(), 'month' => $month->getMonth()])) != 0)
        $monthString="
            <div id=\"month{$month->getMonth()}-{$month->getYear()}\">
                <hr />
                " . monthTable($month, $db) . "
                <hr />
            </div>
        ";
    else
        $monthString="{$month->getMonthName()} {$month->getYear()} non è abilitato per le iscrizioni ai turni serali";

    if ($month->isInFuture()) {
        return $monthString;
    } else {
        return (new PermissionString([PermissionPage::ADMIN => $monthString]))->out();
    }
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

    $userPermission = intval($user['permessi']);
    $userPosition = intval($user['position']);

    //tabella vuota solo scritte niente link
    $dayTurns = [
        'fiabe' => [1 => 'fiabe 1', 2 => 'fiabe 2', 3 => 'fiabe 3'],
        'oasi' => [1 => 'oasi 1', 2 => 'oasi 2', 3 => 'oasi 3'],
        'clown' => [1 => 'clown 1', 2 => 'clown 2', 3 => 'clown 3']
    ];

    if ($userPermission<=PermissionPage::EVENING) {     //se uno è pomeriggio o mattina non vede i link per prenotarsi
        //link se posso prenotarmi + link di prenotazione solo per l'admin
        foreach ($dayTurns as $task => $value) {
            if ($userPosition <= 2)
                $dayTurns[$task][2] = reservationLink($db, $task, 2, $month, $day) .
                    adminSelectUserSelect($db, $task, 2, $month, $day);

            if ($userPosition <= 1)
                $dayTurns[$task][1] = reservationLink($db, $task, 1, $month, $day) .
                    adminSelectUserSelect($db, $task, 1, $month, $day);
        }
    }

    //nomi di quelli già prenotati
    foreach ($turniVolontari as $row) {
        $dayTurns[$row['task']][$row['position']] = "<p class='auto-scale'>{$db->getUserName($row['volunteer'])}"
            .eventuallyAddDelete($row, $dayTurns[$row['task']]);
    }

    $nVolunteer = $db->select('calendar', array('year' => $month->getYear(), 'month' =>  $month->getMonth()))[0]['maxVolunteerNumber'];


    if ($nVolunteer==3) {
        $selectArr = [
            adminSelectUserSelect($db, 'oasi', 3, $month, $day),
            adminSelectUserSelect($db, 'fiabe', 3, $month, $day),
            adminSelectUserSelect($db, 'clown', 3, $month, $day)
        ];
        $thirdRow = "</tr><tr>
				<td>{$dayTurns['oasi'][3]} {$selectArr[0]}</td>
				<td>{$dayTurns['fiabe'][3]} {$selectArr[1]}</td>
				<td>{$dayTurns['clown'][3]} {$selectArr[2]}</td>";
    } else $thirdRow='';

    $numeroENomeGiorno = "<strong>$day</strong> - " . $month->getDayName($day);

    return <<<END
	<p>$numeroENomeGiorno</p>
	<hr />
	<table class="table table-bordered table-condensed fixed">
	    <colgroup>
	        <col class="oasi">
	        <col class="fiabe">
	        <col class="clown">
        </colgroup>
		<thead>
			<tr>
				<th>Oasi</th>
				<th>Fiabe</th>
				<th>Clown</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>{$dayTurns['oasi'][1]}</td>
				<td>{$dayTurns['fiabe'][1]}</td>
				<td>{$dayTurns['clown'][1]}</td>
			</tr><tr>
				<td>{$dayTurns['oasi'][2]}</td>
				<td>{$dayTurns['fiabe'][2]}</td>
				<td>{$dayTurns['clown'][2]}</td>
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
    $allUsers = $db->query('
        SELECT *
        FROM users
        ORDER BY lastname, firstname ASC
    ');
    foreach	($allUsers as $user)
        if ($user['id'] != 0) {
            $firstNameFirstLetter = substr($user['firstname'],0,1);
            $selectString.= "<option value='{$user['id']}'>{$user['lastname']} $firstNameFirstLetter.</option> \n";
        }
    $selectString.='</select>';

    return (new PermissionString([
        PermissionPage::ADMIN => "
        <a onclick='return toggle_visibility(\"adduser-$task-$position-$y-$m-$day\")'><img src='../img/add.png' alt='add user' width='20' height='20'></a>
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
    if (($_SESSION['permessi']==PermissionPage::EVENING && $row['volunteer'] == $_SESSION['id']) || $_SESSION['permessi']<=PermissionPage::ADMIN)
        return "<a href=\"delete_prenotazione.php?volunteer={$row['volunteer']}&day={$row['day']}&task={$row['task']}&position={$row['position']}\"
            onclick=\"return confirm('Sei sicuro di voler cancellare la prenotazione?')\">
                <img alt='cancella prenotazione' src='../img/bin.png' width='15' height='15'>
            </a>";
}

//echo <<<EEND
//<script type="text/javascript">
//	function toggle_visibility(id)
//	{
//		var e = document.getElementById(id);
//		if ( e.style.display == 'none' )
//			e.style.display = 'block';
//		else
//			e.style.display = 'none';
//	}
//</script>
//EEND;


try {
    //general layout of one page
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."turns/turns.php", PermissionPage::MORNING);

    //setting the title
    $generalLayout->yieldElem('title', "Turni");

    //setting the title
    $generalLayout->yieldElem('content', content($db));

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
catch (UnhautorizedException $e) {
    $e->echoAlert();
}