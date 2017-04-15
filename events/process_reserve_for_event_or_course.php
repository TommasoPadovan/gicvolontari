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
                echo("<script> alert('Sei già iscritto a questo evento.'); window.location='{$this->lastPage}'; </script>");
                exit;
            }

            //l'evento è nel futuro o nel passato?
            if ($eventDate->inPast()) {
                echo("<script>
                    alert('Non puoi iscriverti perché questo evento è già passato.');
                    window.location='{$this->lastPage}';
                </script>");
                exit;
            }

            if ($permissionStr == 'admin' || in_array($permissionStr.$position, $whoCanReserve)) {
                $db->insert('eventsattendants', [
                    'event' => $_GET['event'],
                    'volunteer' => $_SESSION['id'],
                    'timestamp' => time()
                ]);


                //sei iscritto con riserva?
                if (count($db->select('events', [
                    'id' => $_GET['event']
                ])) >= $event[0]['maxAttendants'] ) {
                    echo("<script>
                        alert('Prenotazione effettuata ma con riserva, potrai partecipare solo se si libera un posto.');
                        window.location='{$this->lastPage}';
                    </script>");
                } else {    //sei iscritto regolarmente?
                    echo("<script>
                        alert('Prenotazione effettuata con successo.');
                        window.location='{$this->lastPage}';
                    </script>");
                }
            }
            else
                echo("<script> alert('I volontari con il tuo ruolo non possono iscriversi a questo evento'); window.location='{$this->lastPage}'; </script>");
        }


    }
}

try {
    (new ReserveForEventOrCourseCommand(PermissionPage::MORNING))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}