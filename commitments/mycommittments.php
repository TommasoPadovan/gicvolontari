<?php
require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/permissionString.php');
require_once('../lib/datetime/date.php');
require_once('../lib/datetime/time.php');

require_once('classCommitments.php');

$db = new DbConnection();

$commitments = new Commitments();



//fetching turn data
$myTurns = $commitments->getTurnsArray();

$myTurnsStr=array();
foreach ($myTurns as $turn) {
	if ($turn['month']->isInFuture()) {
		array_push(
			$myTurnsStr,
			"<li>{$turn['day']} {$turn['month']->getMonthName()} {$turn['month']->getYear()} - - - {$turn['task']} posizione {$turn['position']} </li>");
	}
}

$turniCorsia="<ul>\n";
foreach ($myTurnsStr as $li)
	$turniCorsia.="$li\n";
$turniCorsia.="</ul>\n";

//$eveningTurns = (new PermissionString([
//	PermissionPage::EVENING => "<h2>Turni corsia</h2>\n$turniCorsia"
//]))->out();

$eveningTurns = "<h2>Turni corsia</h2>\n$turniCorsia";


$meetings = $commitments->getMeetingsArray();
$events = $commitments->getEventsArray();

$meetingsList='';
foreach ($meetings as $meeting) {
	$meetingsList.= "<li>\n";
	$date = (new Date($meeting['date']))->getItalianDate();
	$timeStart = (new Time($meeting['timeStart']))->getSimpleTime();
	$timeEnd = (new Time($meeting['timeEnd']))->getSimpleTime();
	$meetingsList.= <<<MEETING
	<h3><a href="../events/eventDescriptionPage.php?id={$meeting['id']}">{$meeting['title']}</a></h3>
	<p>Il $date dalle $timeStart alle $timeEnd presso {$meeting['location']}</p>
MEETING;
	if ($meeting['requirements'] != '' && $meeting['requirements'] != null )
		$meetingsList.= "<p>Sono stati indicati i seguenti requisiti: {$meeting['requirements']}</p>";

    //vedo se è iscritto come riserva o no
    if ($commitments->isOverbooked($_SESSION['id'], $meeting['event']))
        $meetingsList.='(Riserva)';

    $meetingsList.= "</li>\n";
}

$eventsList='';
foreach ($events as $event) {
	$eventsList.= "<li>\n";
	$date = (new Date($event['date']))->getItalianDate();
	$timeStart = (new Time($event['timeStart']))->getSimpleTime();
	$timeEnd = (new Time($event['timeEnd']))->getSimpleTime();
	$eventsList.= <<<EVENT
	<h3><a href="../events/eventDescriptionPage.php?id={$event['id']}">{$event['title']}</a></h3>
	<p>Il $date dalle $timeStart alle $timeEnd presso {$event['location']}</p>
EVENT;
	if ($event['requirements'] != '' && $event['requirements'] != null )
		$eventsList.= "<p>Sono stati indicati i seguenti requisiti: {$event['requirements']}</p>";

    //vedo se è iscritto come riserva o no
    if ($commitments->isOverbooked($_SESSION['id'], $event['event']))
        $eventsList.='(Riserva)';

	$eventsList.= "</li>\n";
}


$content = <<<HTML
<a class="pull-right" href="print_committments.php">
	<img src="../img/print.png" alt="stampa" height="30" width="30">
</a>
<h1>I miei impegni</h1>
<div class="pull-right">
	<a class="btn btn-default" href="export_google_cal_csv.php">Esporta In Google Calendar</a>
	<a href="https://support.google.com/calendar/answer/37118?hl=it" target="_blank"><img src="../img/info.png" alt='info' height='30' width='30'></a>
</div>
<div class="row">
	<div class="col-sm-6">
		$eveningTurns
		<h2>Riunioni</h2>
		<ul>
			$meetingsList
		</ul>
		<h2>Eventi</h2>
		<ul>
			$eventsList
		</ul>
	</div>

	<div class="col-sm-6">
	.
	</div>
</div>



HTML;


try {
	//general layout of one page
	$generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."commitments/mycommittments.php", PermissionPage::MORNING);

	//setting the title
	$generalLayout->yieldElem('title', "Impegni");

	$generalLayout->yieldElem('content', $content);

	echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}
