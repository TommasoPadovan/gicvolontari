<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');

PermissionsMng::atMostAuthorizationLevel(2);

$conn=connect();

//general layout of one page
$generalLayout = new GeneralLayout("mycommittments.php");

//setting the title
$generalLayout->yieldElem('title', "Impegni");

//fetching turn data
$result=queryThis("
	SELECT c.month as month, c.year as year, c.day as day, t.task as task,  t.position as position
	FROM calendar AS c JOIN turni AS t
		ON c.id=t.day
	WHERE t.volunteer = {$_SESSION['id']};
	",$conn);
$myCommittments=array();
for ($i=0; $i < mysql_num_rows($result); $i++) { 
 	$row=mysql_fetch_assoc($result);
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
		array_push($strCommittments, "<li>{$committment['day']} {$committment['month']->getMonthName()} {$committment['month']->getYear()} - - - {$committment['task']} posizione {$committment['position']} </li>");
	}
}

$turniCorsia="<ul>\n";
foreach ($strCommittments as $li)
	$turniCorsia.=$li."\n";
$turniCorsia.="</ul>\n";


$content = <<<HTML
<h1>I miei impegni</h1>
<div class="row">
	<div class="col-sm-6">
		<h2>Turni corsia</h2>
		$turniCorsia
	</div>

	<div class="col-sm-6">
	.
	</div>
</div>



HTML;



$generalLayout->yieldElem('content', $content);

$generalLayout->pprint();

?>