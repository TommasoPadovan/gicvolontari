<?php
require_once('lib/generalLayout.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');
require_once('lib/permissionString.php');

$db = new DbConnection();



//fetching turn data
$result = $db->prepare('
	SELECT c.month as month, c.year as year, c.day as day, t.task as task,  t.position as position
	FROM calendar AS c JOIN turni AS t
		ON c.id=t.day
	WHERE t.volunteer = :id
');
$result->execute(array('id' => $_SESSION['id']));
$myCommittments=array();
foreach ($result as $row) {
	array_push(
		$myCommittments,
		array(
			'month' => new Month( intval($row['month']), intval($row['year']) ),
			'day' => $row['day'],
			'task' => $row['task'],
			'position' => $row['position']
		)
	);
}



$strCommittments=array();
foreach ($myCommittments as $committment) {
	if ($committment['month']->isInFuture()) {
		array_push(
			$strCommittments,
			"<li>{$committment['day']} {$committment['month']->getMonthName()} {$committment['month']->getYear()} - - - {$committment['task']} posizione {$committment['position']} </li>");
	}
}

$turniCorsia="<ul>\n";
foreach ($strCommittments as $li)
	$turniCorsia.="$li\n";
$turniCorsia.="</ul>\n";

$eveningTurns = (new PermissionString([
	PermissionPage::EVENING => "<h2>Turni corsia</h2>\n$turniCorsia"
]))->out();




$allEvents = $db->prepare("
	SELECT *
	FROM eventsattendants AS ea JOIN events AS e ON ea.event = e.id
	WHERE ea.volunteer = :volunteerId
");
$allEvents->execute([':volunteerId' => $_SESSION['id']]);


$meetings=[];
$events=[];
foreach ($allEvents as $e) {
	if ($e['type'] == "riunione")
		array_push($meetings, $e);
	if ($e['type'] == "evento")
		array_push($events, $e);
}

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
	<h3>{$meeting['title']}</h3>
	<p>Il {$event['date']} dalle {$event['timeStart']} alle {$event['timeEnd']} presso {$event['location']}</p>
EVENT;
	if ($event['requirements'] != '' && $event['requirements'] != null )
		$eventsList.= "<p>Sono stati indicati i seguenti requisiti: {$event['requirements']}</p>";
	$eventsList.= "</li>\n";
}


$content = <<<HTML
<a class="pull-right" href="print_committments.php">
	<img src="img/print.png" alt="stampa" height="30" width="30">
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
	$generalLayout = new GeneralLayout("mycommittments.php", PermissionPage::MORNING);

	//setting the title
	$generalLayout->yieldElem('title', "Impegni");

	$generalLayout->yieldElem('content', $content);

	echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
	$e->echoAlert();
}
