<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 18/03/2017
 * Time: 00:07
 */

require_once('../lib/sqlLib.php');
require_once('../lib/command.php');
require_once('../lib/datetime/date.php');


class ExportCsvVolunteersListCommand extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }


    protected function template(){
        $db = new DbConnection();

        $users = $db->select('users');


        header('Content-Disposition: attachment; filename="Lista_volontari_'. date("d-m-Y") .'.csv"');
        header('Content-type: text/plain charset=utf-8');

        echo <<<CSV
Nome;Cognome;Email;Codice Fiscale;Data di Nascita;Numero di telefono;Indirizzo;Ruolo;\n
CSV;

        foreach ($users as $user) {
            if ($user['id'] == 0)
                continue;

            $nome = $user['firstname'];
            $cognome = $user['lastname'];
            $email = $user['email'];
            $CF = $user['CF'];
            if (isset($user['birthdate']) && $user['birthdate']!='')
                $birthDate = (new Date($user['birthdate']))->getItalianDate();
            else $birthDate = '';
            $phone = $user['phone'];

            $address='';
            if (isset($user['address']) && $user['address']!='')
                $address.= "{$user['address']}";
            if (isset($user['address2']) && $user['address2']!='')
                $address.=" {$user['address2']}";
            if ($address!='')
                $address.=', ';
            if (isset($user['city']) && $user['city']!='')
                $address.="{$user['city']} ";
            if (isset($user['prov']) && $user['prov']!='')
                $address.="({$user['prov']}) ";
            if (isset($user['state']) && $user['state']!='')
                $address.="{$user['state']} ";


            $role='';
            switch ($user['permessi']) {
                case 1:
                    $role = "Amministratore {$user['position']}";
                    break;
                case 2:
                    $role = "Sera {$user['position']}";
                    break;
                case 3:
                    $role = "Pomeriggio {$user['position']}";
                    break;
                case 4:
                    $role = "Mattina {$user['position']}";
                    break;
            }

            echo <<<CSV
$nome;$cognome;$email;$CF;$birthDate;$phone;$address;$role;\n
CSV;
        }
    }
}


try {
    (new ExportCsvVolunteersListCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}