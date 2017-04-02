<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 15/03/2017
 * Time: 10:54
 */

require_once('eventDetail.php');
require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/GetConstraints.php');


$db = new DbConnection();


if (isset($_SERVER['HTTP_REFERER']))
    $back = $_SERVER['HTTP_REFERER'];
else
    $back = '../home.php';


$constraints = new GetConstraints(
    [$_GET['id'] => ['events', 'id']],
    []
);


if ($constraints->areOk()) {
    $content = (new EventDetail($_GET['id']))->getEventDescription();
    $content.= <<<BACKBUTTON
        <!--<a href="$back" class="btn btn-default">Indietro</a>-->
BACKBUTTON;
} else {
    $content = $constraints->getErrorContent();
}

try {
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."events/eventsandcourses.php", PermissionPage::MORNING);
    $generalLayout->yieldElem('title', "Dettagli Evento");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
    exit;
}