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


$db = new DbConnection();


if (isset($_SERVER['HTTP_REFERER']))
    $back = $_SERVER['HTTP_REFERER'];
else
    $back = '../home.php';


if (isset($_GET['id']) && count($db->select('events', ['id' => $_GET['id']]))!=0 ) {
    $content = (new EventDetail($_GET['id']))->getEventDescription();
    $content.= <<<BACKBUTTON
        <a href="$back" class="btn btn-default">Indietro</a>
BACKBUTTON;
} else {
    $time = time();
    $content = <<<ERRORPAGE
    <div class="row">
        <div class="col-sm-6">
            <img src="../img/something-wrong.jpg" width="100%" height="100%">
        </div>
        <div class="col-sm-6">
            <p>Oooops, l'evento che hai selzionato non esiste più. Ci deve essere una problema.</p>
            <p>Se vuoi segnalare il problema segnati questi dati.</p>
            <ol>
                <li>Page: eventDescriptionPage.php</li>
                <li>getId: {$_GET['id']}</li>
                <li>timestamp: $time</li>
            </ol>
            <a href="$back" class="btn btn-default">Indietro</a>
        </div>
    </div>
ERRORPAGE;
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