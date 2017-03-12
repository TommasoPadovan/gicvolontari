<?php
require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/permissionString.php');

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

$eveningTurns = (new PermissionString([
	PermissionPage::EVENING => "<h2>Turni corsia</h2>\n$turniCorsia"
]))->out();




$meetings = $commitments->getMeetingsArray();
$events = $commitments->getEventsArray();

$meetingsList='';
foreach ($meetings as $meeting) {
	$meetingsList.= "<li>\n";
	$meetingsList.= <<<MEETING
	<h3>{$meeting['title']}</h3>
	<p>Il {$meeting['date']} dalle {$meeting['timeStart']} alle {$meeting['timeEnd']} presso {$meeting['location']}</p>
MEETING;
	if ($meeting['requirements'] != '' && $meeting['requirements'] != null )
		$meetingsList.= "<p>Sono stati indicati i seguenti requisiti: {$meeting['requirements']}</p>";
	$meetingsList.= "</li>\n";
}

$eventsList='';
foreach ($events as $event) {
	$eventsList.= "<li>\n";
	$eventsList.= <<<EVENT
	<h3>{$event['title']}</h3>
	<p>Il {$event['date']} dalle {$event['timeStart']} alle {$event['timeEnd']} presso {$event['location']}</p>
EVENT;
	if ($event['requirements'] != '' && $event['requirements'] != null )
		$eventsList.= "<p>Sono stati indicati i seguenti requisiti: {$event['requirements']}</p>";
	$eventsList.= "</li>\n";
}


$content = <<<HTML
<a class="pull-right" href="print_committments.php">
	<img src="../img/print.png" alt="stampa" height="30" width="30">
</a>
<h1>I miei impegni</h1>
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
