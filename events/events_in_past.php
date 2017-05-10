<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 21/04/17
 * Time: 12.45
 */

require_once('../lib/generalLayout.php');
require_once('../lib/permission.php');
require_once('../lib/permissionString.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/date.php');
require_once('../lib/datetime/time.php');

require_once('eventDetail.php');

$db = new DbConnection();

//check if there is a get request about the year to show, or show all
$today = date("Y-m-d");
$thisYear = date ("Y");
if (!isset($_GET['year'])) {
    $from = '0000-00-00 00:00:00';
    $to = "$today 00:00:00";
} else {
    if ($_GET['year'] == date("Y")) {
        $from = "{$_GET['year']}-00-00 00:00:00";
        $to = "$today 00:00:00";
    } else {
        $from = "{$_GET['year']}-00-00 00:00:00";
        $to = "{$_GET['year']}-12-31 23:59:59";
    }
}


$eventList='';

//$allEvents = $db->select('events');
$allEvents = $db->prepare("
SELECT *
FROM events
WHERE (date BETWEEN :dayFrom AND :dayTo)
");
$allEvents->execute([
    ':dayFrom' => $from,
    ':dayTo' => $to
]);
foreach ($allEvents as $row) {
    $eventList.=(new EventDetail($row['id']))->getCard();
}


$yearOptions='';
for($i=date("Y"); $i>2000; $i--)
    $yearOptions.="<option value='$i'>$i</option>";

$content = <<<HTML
<h1>Eventi Passati</h1>
<div>
    <a href="eventsandcourses.php" class="btn btn-default">Vai agli Eventi Futuri</a>
</div>
<hr />
<form method="GET" action="events_in_past.php">
    <div class="form-group row">
		<div class="select col-sm-2 col-xs-6">
            <select class="form-control" name="year">
                $yearOptions
            </select>
        </div>
        <div class="col-sm-2 col-xs-6">
            <input class="btn btn-default btn-block" type="submit" value="Filtra per anno">
        </div>
    </div>
</form>
<div class="row">
    $eventList
</div>
HTML;



try {
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."events/eventsandcourses.php", PermissionPage::MORNING);
    $generalLayout->yieldElem('title', "Eventi Passati");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
    exit;
}
