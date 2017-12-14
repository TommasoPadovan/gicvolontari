<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 10/03/2017
 * Time: 12:16
 */
require_once('../lib/command.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/date.php');
require_once('../lib/JsLib.php');

class ReserveForEventOrCourseCommand extends Command {

    private $lastPage;

    public function __construct($permission){
        parent::__construct($permission);
         if (isset($_SERVER['HTTP_REFERER']))
            $this->lastPage = $_SERVER['HTTP_REFERER'];
        else $this->lastPage = 'eventsandcourses.php';
    }

    protected function template(){
        $db = new DbConnection();


        if ( isset($_SESSION['id']) && isset($_GET['event'])) {
            $multiplicity = 1;
            if ( isset($_GET['multiplicity']) ) $multiplicity = $_GET['multiplicity'];
            $note = '';
            if ( isset($_GET['note']) ) $note = $_GET['note'];

            //can i reserve?
            $me = $db->getUser($_SESSION['id']);
            $permission = $me['permessi'];
            switch ($permission) {
                case 1:
                    $permissionStr = 'admin';
                    break;
                case 2:
                    $permissionStr = 'sera';
                    break;
                case 3:
                    $permissionStr = 'pomeriggio';
                    break;
                case 4:
                    $permissionStr = 'mattina';
                    break;
                default:
                    $permissionStr = '-';
                    break;
            }
            $position = $me['position'];

            //SANITY CHECK

            $event = $db->select('events', ['id' => $_GET['event']]);
            $whoCanReserve = unserialize($event[0]['who']);
            $eventDate = new Date($event[0]['date']);


            //sei già iscritto?
            if (count($db->select('eventsattendants', [
                    'event' => $_GET['event'],
                    'volunteer' => $me['id']
                ]))!=0) {
                JS::alertAndRedirect('Sei già iscritto a questo evento.', $this->lastPage);
                exit;
            }

            //l'evento è nel futuro o nel passato?
            if ($eventDate->inPast()) {
                JS::alertAndRedirect('Non puoi iscriverti perché questo evento è già passato.', $this->lastPage);
                exit;
            }

            if ($permissionStr == 'admin' || in_array($permissionStr.$position, $whoCanReserve)) {
                $db->insert('eventsattendants', [
                    'event' => $_GET['event'],
                    'volunteer' => $_SESSION['id'],
                    'multiplicity' => $multiplicity,
                    'note' => $note,
                    'timestamp' => time()
                ]);

                //sei iscritto con riserva?
//                var_dump(count($db->select('events', [
//                    'id' => $_GET['event']
//                ])));
//                echo "<br />";
//                var_dump($event[0]['maxAttendants']);
//                exit;
                if (count($db->select('eventsattendants', [
                    'event' => $_GET['event']
                ])) > $event[0]['maxAttendants'] ) {
                    JS::alertAndRedirect('Prenotazione effettuata ma con riserva, potrai partecipare solo se si libera un posto.', $this->lastPage);
                } else {    //sei iscritto regolarmente?
                    JS::alertAndRedirect('Prenotazione effettuata con successo.', $this->lastPage);
                }
            }
            else
                JS::alertAndRedirect('I volontari con il tuo ruolo non possono iscriversi a questo evento', $this->lastPage);
        }


    }
}

try {
    (new ReserveForEventOrCourseCommand(PermissionPage::MORNING))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}