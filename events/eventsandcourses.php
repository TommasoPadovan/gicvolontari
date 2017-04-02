<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 09/03/2017
 * Time: 17:09
 */
require_once('../lib/generalLayout.php');
require_once('../lib/permission.php');
require_once('../lib/permissionString.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/date.php');
require_once('../lib/datetime/time.php');

require_once('eventDetail.php');


$db = new DbConnection();


$addEventAdminButton =
    (new PermissionString([PermissionPage::ADMIN => "<a class=\"btn btn-default pull-right\" href=\"add_event.php\">Aggiungi evento</a>"]))->out();





$eventList='';
$allEvents = $db->select('events');
foreach ($allEvents as $row) {
    $eventList.=(new EventDetail($row['id']))->getCard();
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
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."events/eventsandcourses.php", PermissionPage::MORNING);
    $generalLayout->yieldElem('title', "Eventi");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
    exit;
}