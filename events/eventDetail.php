<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 15/03/2017
 * Time: 10:29
 */
require_once('../lib/generalLayout.php');
require_once('../lib/permission.php');
require_once('../lib/permissionString.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/date.php');
require_once('../lib/datetime/time.php');
require_once('../commitments/classCommitments.php');

class EventDetail {

    private $event;
    private $db;

    public function __construct($eventId) {
        $this->db = new DbConnection();

        $this->event = $this->db->select('events', ['id' => $eventId]);
        $this->event = $this->event[0];
    }

    /**
     * @return string A card view of an event, that have to be a small view that can fit in a page with several cards
     *          it's title should be a link to a detailed page with all the other thing about the event
     */
    public function getCard() {
        /**
         * here i get variables i will use in the pdf template
         */
        $type = $this->event['type'];
        $title = $this->event['title'];
        $date = (new Date($this->event['date']))->getItalianDate();
        $timeStart = (new Time($this->event['timeStart']))->getSimpleTime();
        $timeEnd = (new Time($this->event['timeEnd']))->getSimpleTime();
        $location = $this->event['location'];
        $nPartecipanti = count($this->db->select('eventsattendants', [
            'event' => $this->event['id']
        ]));
        $amIReserved = count($this->db->select('eventsattendants', [
            'event' => $this->event['id'],
            'volunteer' => $_SESSION['id']
        ]));
        if ($amIReserved == 0){
            $amIReserved= <<<END
    <a href="process_reserve_for_event_or_course.php?event={$this->event['id']}"
    class="btn btn-default">Iscriviti all'evento!</a>
END;
            $style = '';
        }
        else {
            if ( (new Commitments())->isOverbooked($_SESSION['id'], $this->event['id']) ) {
                $amIReserved = <<<END
    Sei iscritto con riserva <a href='process_remove_reservation.php?event={$this->event['id']}'><img src='../img/bin.png' alt='cancella' height='15' width='15'
                        onclick="return confirm('Sei sicuro di voler cancellare la prenotazione?')"/>
                    </a>
END;
                $style = 'registered_reserve';
            } else {
                $amIReserved = <<<END
    Sei iscritto! <a href='process_remove_reservation.php?event={$this->event['id']}'><img src='../img/bin.png' alt='cancella' height='15' width='15'
                        onclick="return confirm('Sei sicuro di voler cancellare la prenotazione?')"/>
                    </a>
END;
                $style = 'registered';
            }
        }

        $adminEditButton = (new PermissionString([
            PermissionPage::ADMIN => "<a class='pull-right' href='add_event.php?id={$this->event['id']}'><img src='../img/pencil.png' alt='modifica' height='15' width='15'></a> "
        ]))->out();

        $adminRemoveButton = (new PermissionString([
            PermissionPage::ADMIN => "<a class='pull-right' href='delete_event.php?id={$this->event['id']}'  onclick=\"return confirm('Sei sicuro di voler eliminare l\'evento $title?')\">
            <img src='../img/bin.png' alt='cancella' height='15' width='15'>
        </a> "
        ]))->out();

        $reservationDiv = $this->getReservationDiv();


        return <<<TAG
            <div class="col-sm-4 panel panel-default $style">
                <div class="panel-body">
                    <div>
                        <h4><a href="eventDescriptionPage.php?id={$this->event['id']}">$title</a></h4>
                        $adminEditButton
                        $adminRemoveButton
                        <!--<p><label>Tipo: </label> $type</p>-->
                        <p><label>Data e Ora: </label> il $date dalle ore $timeStart alle ore $timeEnd</p>
                        <!--<p><label>Inizio: </label> $timeStart</p>-->
                        <!--<p><label>Fine: </label> $timeEnd</p>-->
                        <p><label>Luogo: </label> $location</p>
                        <p><label>Iscritti: </label> $nPartecipanti</p>
                        <p>$amIReserved</p>
                    </div>
                </div>
            </div>

TAG;
    }


    public function getEventDescription() {
        /**
         * here i get variables i will use in the pdf template
         */
        $type = $this->event['type'];
        $title = $this->event['title'];
        $date = (new Date($this->event['date']))->getItalianDate();
        $timeStart = (new Time($this->event['timeStart']))->getSimpleTime();
        $timeEnd = (new Time($this->event['timeEnd']))->getSimpleTime();
        $location = $this->event['location'];
        $description = $this->event['description'];
        $requirements = $this->event['requirements'];
        $resoconto = $this->event['resoconto'];
        $minAttendants = $this->event['minAttendants'];
        $maxAttendants = $this->event['maxAttendants'];
        $who = unserialize($this->event['who']);

        $adminEditButton = (new PermissionString([
            PermissionPage::ADMIN => "<a class='pull-right' href='add_event.php?id={$this->event['id']}'><img src='../img/pencil.png' alt='modifica' height='15' width='15'></a> "
        ]))->out();

        $adminRemoveButton = (new PermissionString([
            PermissionPage::ADMIN => "<a class='pull-right' href='delete_event.php?id={$this->event['id']}'  onclick=\"return confirm('Sei sicuro di voler eliminare l\'evento $title?')\">
            <img src='../img/bin.png' alt='cancella' height='15' width='15'>
        </a> "
        ]))->out();

        $liWhoCanReserve = '';
        foreach($who as $role)
            $liWhoCanReserve.="<li>$role</li>\n";

        $adminButtonPrintResoconto = (new PermissionString([
            PermissionPage::ADMIN => "<a class=\"pull-right\" href=\"print_event_resoconto.php?id={$_GET['id']}\"><img src=\"../img/print.png\" width=\"30\" height=\"30\" alt=\"stampa solo resoconto\"></a>"
        ]))->out();

        $reservationDiv = $this->getReservationDiv();
        return <<<TAG
            <div class="panel panel-default">
                <a class="pull-right" href="print_event_detail.php?id={$_GET['id']}"><img src="../img/print.png" width="30" height="30" alt="stampa dettagli evento"></a>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>$title</h2>
                            $adminEditButton
                            $adminRemoveButton
                            <p><label>Tipo evento: </label> $type</p>
                            <p><label>Data e Ora: </label> il $date dalle ore $timeStart alle ore $timeEnd</p>
                            <!--<p><label>Inizio: </label> $timeStart</p>-->
                            <!--<p><label>Fine: </label> $timeEnd</p>-->
                            <p><label>Luogo: </label> $location</p>
                            <p><label>Descrizione: </label> $description</p>
                            <p><label>Requisiti: </label> $requirements</p>
                            <p><label>Minimo partecipanti: </label> $minAttendants</p>
                            <p><label>Massimo participanti: </label> $maxAttendants</p>
                            <p><label>Chi si può iscrivere?</label></p>
                            <ul>
                                $liWhoCanReserve
                            </ul>
                        </div>
                        <div class="col-sm-6 vertical_line">
                            $reservationDiv
                            <hr />
                            $adminButtonPrintResoconto
                            <p><label>Resoconto: </label> $resoconto</p>
                        </div>
                    </div>
                </div>
            </div>

TAG;

    }




    /**
     * Private function that generates html form for reserving and reserve place into one event
     * Admin will get more option to reserve places for other users
     * @return string
     */
    private function getReservationDiv() {
        $options='';
        $allUsers = $this->db->select('users');
        foreach ($allUsers as $user)
            if ($user['id'] != 0)
                $options.= "<option value='{$user['id']}'>{$user['lastname']} {$user['firstname']}</option>";
        $adminReserveUserForm = (new PermissionString([
            PermissionPage::ADMIN => <<<FORM
            <form action="admin_reserve_user_for_event_or_course.php" method="POST">
                <input type="hidden" value="{$this->event['id']}" name="eventId" />
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
//        $volunteerThisEvent = $this->db->select('eventsattendants', ['event' => $this->event['id']]);
        $volunteerThisEvent = $this->db->prepare("
          SELECT *
          FROM eventsattendants
          WHERE event = :event
          ORDER BY timestamp ASC");
        $volunteerThisEvent->execute([':event' => $this->event['id']]);
        $maxAttendants = $this->event['maxAttendants'];
        $attendantsCounter = 1;
        foreach ($volunteerThisEvent as $item) {
            $volunteerDetail = $this->db->getUser($item['volunteer']);

            $removeOwnReservationLink = '';
            if ( isset($_SESSION['id']) && $_SESSION['id'] == $volunteerDetail['id'])
                $removeOwnReservationLink = "<a href='process_remove_reservation.php?event={$this->event['id']}'><img src='../img/bin.png' alt='cancella' height='15' width='15'
                    onclick=\"return confirm('Sei sicuro di voler cancellare la prenotazione?')\"/>
                </a>";
            $removeReservationLink = (new PermissionString([
                PermissionPage::ADMIN =>
                    "<a href='process_admin_remove_reservation.php?event={$this->event['id']}&volunteer={$volunteerDetail['id']}'
                    onclick='return confirm(\"Sicuro di voler rimuovere la prenotazione di {$volunteerDetail['firstname']} {$volunteerDetail['lastname']}?\")'>
                    <img src=\"../img/bin.png\" alt='cancella' height='15' width='15' />
                </a>",
                PermissionPage::MORNING => $removeOwnReservationLink
            ]))->out();
            $liAttendants.= "<li>$removeReservationLink {$volunteerDetail['firstname']} {$volunteerDetail['lastname']}</li>";
            if ($attendantsCounter == $maxAttendants)
                $liAttendants.="--------------<br />Riserve:";
            $attendantsCounter++;
        }

        return <<<RESDIV
                <h2>Partecipanti</h2>
                <ol>
                    $liAttendants
                </ol>
                <div class="row">
                    <div class="col-sm-6">
                        <a href="process_reserve_for_event_or_course.php?event={$this->event['id']}" class="btn btn-default btn-block">Iscriviti all'evento!</a>
                    </div>
                </div>
                <div class="row">
                    $adminReserveUserForm
                </div>
RESDIV;
    }






}