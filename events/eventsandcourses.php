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


    $adminEditButton = (new PermissionString([
        PermissionPage::ADMIN => "<a class='pull-right' href='add_event.php?id={$row['id']}'><img src='../img/pencil.png' alt='modifica' height='15' width='15'></a> "
    ]))->out();

    $adminRemoveButton = (new PermissionString([
        PermissionPage::ADMIN => "<a class='pull-right' href='delete_event.php?id={$row['id']}'  onclick=\"return confirm('Sei sicuro di voler eliminare l\'evento $title?')\">
            <img src='../img/bin.png' alt='cancella' height='15' width='15'>
        </a> "
    ]))->out();

    $options='';
    $allUsers = $db->select('users');
    foreach ($allUsers as $user)
        $options.= "<option value='{$user['id']}'>{$user['lastname']} {$user['firstname']}</option>";
    $adminReserveUserForm = (new PermissionString([
        PermissionPage::ADMIN => <<<FORM
            <form action="admin_reserve_user_for_event_or_course.php" method="POST">
                <input type="hidden" value="{$row['id']}" name="eventId" />
                <div class="form-group col-sm-6">
                    <label for="RegistraUtente">Registra Utente</label>
                    <div class="select">
                        <select class="form-control" name="userId">
                            $options
                        </select>
                    </div>
                    <input type="submit" class="btn btn-default">
                </div>
			</form>
FORM

    ]))->out();


    $liAttendants = "";
    $volunteerThisEvent = $db->select('eventsattendants', ['event' => $row['id']]);
    foreach ($volunteerThisEvent as $item) {
        $volunteerDetail = $db->getUser($item['volunteer']);

        $removeOwnReservationLink = '';
        if ( isset($_SESSION['id']) && $_SESSION['id'] == $volunteerDetail['id'])
            $removeOwnReservationLink = " alt='cancella' height='15' width='15' />
                </a>";
        $removeReservationLink = (new PermissionString([
            PermissionPage::ADMIN =>
                "<a href='process_admin_remove_reservation.php?event={$row['id']}&volunteer={$volunteerDetail['id']}'
                    onclick='return confirm(\"Sicuro di voler rimuovere la prenotazione di {$volunteerDetail['firstname']} {$volunteerDetail['lastname']}?\")'>
                    <img src=\"../img/bin.png\" alt='cancella' height='15' width='15' />
                </a>",
            PermissionPage::MORNING => $removeOwnReservationLink
        ]))->out();
        $liAttendants.= "<li>$removeReservationLink {$volunteerDetail['firstname']} {$volunteerDetail['lastname']}</li>";
    }

    $eventDescription = <<<TAG
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <h2>$title</h2>
                    $adminEditButton
                    $adminRemoveButton
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
                <div class="col-sm-6 vertical_line">
                    <h2>Partecipanti</h2>
                    <ol>
                        $liAttendants
                    </ol>
                    <a href="process_reserve_for_event_or_course.php?event={$row['id']}" class="btn btn-default">Iscriviti all'evento!</a>
                    $adminReserveUserForm
                </div>
            </div>
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
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."events/eventsandcourses.php", PermissionPage::AFTERNOON);
    $generalLayout->yieldElem('title', "Eventi");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
    exit;
}