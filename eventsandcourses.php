<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 09/03/2017
 * Time: 17:09
 */
require_once('lib/generalLayout.php');
require_once('lib/permission.php');
require_once('lib/permissionString.php');
require_once('lib/sqlLib.php');


$db = new DbConnection();


$addEventAdminButton =
    (new PermissionString([PermissionPage::ADMIN => "<a class=\"btn btn-default pull-right\" href=\"add_event.php\">Aggiungi evento</a>"]))->out();





$eventList='';
$allEvents = $db->select('events');
foreach ($allEvents as $row) {
    $type = $row['type'];
    $title = $row['title'];
    $date = $row['date'];
    $timeStart = $row['timeStart'];
    $timeEnd = $row['timeEnd'];
    $location = $row['location'];
    $description = $row['description'];
    $requirements = $row['requirements'];
    $minAttendants = $row['minAttendants'];
    $maxAttendants = $row['maxAttendants'];

    $eventDescription = <<<TAG
    <div class="panel panel-default">
        <div class="panel-body">
            <h2>$title</h2>
            <p><label>Tipo: </label> $type</p>
            <p><label>Data: </label> $date</p>
            <p><label>Inizio: </label> $timeStart</p>
            <p><label>Fine: </label> $timeEnd</p>
            <p><label>Luogo: </label> $location</p>
            <p><label>Descrizione: </label> $description</p>
            <p><label>Requisiti: </label> $requirements</p>
            <p><label>Minimo partecipanti: </label> $minAttendants</p>
            <p><label>Massimo participanti: </label> $maxAttendants</p>
        </div>
    </div>
TAG;

    $eventList.=$eventDescription;
}


$content = <<<HTML
<h1>Eventi</h1>
<div>
    $addEventAdminButton
</div>
<hr />
<div>
    $eventList
</div>
HTML;












try {
    $generalLayout = new GeneralLayout("eventsandcourses.php", PermissionPage::AFTERNOON);
    $generalLayout->yieldElem('title', "Eventi");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
    exit;
}