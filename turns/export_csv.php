<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 13/03/2017
 * Time: 18:36
 */
require_once('../lib/datetime/month.php');
require_once('../lib/sqlLib.php');


class ExportCsvTurnsCommand extends Command {

    public function __construct($permission) {
        parent::__construct($permission);
    }

    protected function template() {
        $db = new DbConnection();
        
        if (isset($_GET['Mese'])) {
            header('Content-Disposition: attachment; filename="riepilogo_turni_sera_"' . $_GET['Mese'] . '.csv');
            header('Content-type: text/plain charset=utf-8');

            $month = Month::getMonthFromInternational($_GET['Mese']);

            $numberLine = '';
            for ($i = 1; $i <= $month->dayThisMonth(); $i++) {

                $numberLine .= $month->dayOfWeekName($i) . " $i;";
            }

            $arrayTask = [
                'fiabe' => [1, 2, 3],
                'oasi' => [1, 2, 3],
                'clown' => [1, 2, 3]
            ];

            $arrayString = [
                'fiabe' => [1 => [], 2 => [], 3 => []],
                'oasi' => [1 => [], 2 => [], 3 => []],
                'clown' => [1 => [], 2 => [], 3 => []]
            ];
            foreach ($arrayString as $task => $arrPosition) {
                foreach ($arrPosition as $position => $arrStrings) {
                    for ($i = 1; $i <= $month->dayThisMonth(); $i++)
                        $arrayString[$task][$position][$i] = "//";
                }
            }

            foreach ($arrayTask as $task => $arrPosition) {
                foreach ($arrPosition as $position) {
                    $statement = $db->prepare("
                        SELECT t.volunteer AS volunteer, c.day AS daynumber
                        FROM calendar AS c JOIN turni AS t ON (c.id=t.day)
                        WHERE c.year = :year AND c.month = :month AND t.task = :task AND t.position = :position
                        ORDER BY c.day ASC
                    ");
                    $statement->execute([
                        ':year' => $month->getYear(),
                        ':month' => $month->getMonth(),
                        ':task' => $task,
                        ':position' => $position
                    ]);

                    foreach ($statement as $row) {
                        $arrayString[$task][$position][$row['daynumber']] = $db->getUserName($row['volunteer']);
                    }

                }
            }


            function getLine($array)
            {
                $aux = '';
                foreach ($array as $str) {
                    $aux .= $str . ';';
                }
                return $aux;
            }

            $getLine = 'getLine';
            echo <<<CSV
            {$month->getMonthName()} {$month->getYear()};$numberLine;
            Fiabe1;{$getLine($arrayString['fiabe'][1])}
            Fiabe2;{$getLine($arrayString['fiabe'][2])}
            Fiabe3;{$getLine($arrayString['fiabe'][3])}
            Oasi1;{$getLine($arrayString['oasi'][1])}
            Oasi2;{$getLine($arrayString['oasi'][2])}
            Oasi3;{$getLine($arrayString['oasi'][3])}
            Clown1;{$getLine($arrayString['clown'][1])}
            Clown2;{$getLine($arrayString['clown'][2])}
            Clown3;{$getLine($arrayString['clown'][3])}

CSV;

        }
    }
}

try {
    (new ExportCsvTurnsCommand(PermissionPage::ADMIN))->execute();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}

