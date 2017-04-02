<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 12/03/2017
 * Time: 17:36
 */

require_once('../lib/command.php');
require_once('../lib/sqlLib.php');
require_once('../lib/pdf/PDF.php');
require_once('../lib/datetime/month.php');
require_once('../lib/GetConstraints.php');




$constraints = new GetConstraints(
    [$_GET['id'] => ['users', 'id']],
    []
);


if (!$constraints->areOk()) {
    $content = $constraints->getErrorContent();
    try {
        $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."volunteers/volunteers.php", PermissionPage::ADMIN);
        $generalLayout->yieldElem('title', "Stampa Volontario");
        $generalLayout->yieldElem('content', $content);
        echo $generalLayout->getPage();
    }
    catch (UnhautorizedException $e){
        $e->echoAlert();
        exit;
    }


} else {


    class GenerateVolunteerDetailsCPdfCommand extends Command{

        public function __construct($permission){
            parent::__construct($permission);
        }

        protected function template(){
            $db = new DbConnection();
            $user = $db->getUser($_GET['id']);

            /**
             * /////////////////////////////////////
             * Count presenze turni serali
             * /////////////////////////////////////
             */
            $statement = $db->prepare(<<<TAG
                SELECT c.year AS year, c.month AS month, COUNT(c.day) as count
                FROM turni AS t JOIN calendar AS c ON (t.day = c.id)
                  JOIN users AS u ON (t.volunteer = u.id)
                WHERE u.id = :id AND c.year = :year
                GROUP BY c.year, c.month
                ORDER BY c.year, c.month
TAG
            );

            $statement->execute([':id' => $_GET['id'], ':year' => $_GET['year']]);
            $allVolunteerTurns = $statement->fetchAll(PDO::FETCH_ASSOC);


            /**
             * /////////////////////////////////////
             * Dettagli presenze turni serali
             * /////////////////////////////////////
             */

            $allVolunteerTurnsDetail = $db->prepare(<<<TAG
                SELECT c.year AS year, c.month AS month, c.day AS day, t.task AS task, t.position AS position
                FROM turni AS t JOIN calendar AS c ON (t.day = c.id)
                  JOIN users AS u ON (t.volunteer = u.id)
                WHERE u.id = :id AND c.year = :year
                ORDER BY c.year, c.month, c.day
TAG
            );

            $allVolunteerTurnsDetail->execute([':id' => $_GET['id'], ':year' => $_GET['year']]);


            /**
             * /////////////////////////////////////
             * Dettagli presenze riunioni/eventi
             * /////////////////////////////////////
             */
            $meetingsEventsDetail = $db->prepare(<<<QUERY
                SELECT e.date AS date, e.type AS type, e.title AS title, e.location AS location, e.description AS description
                FROM events AS e JOIN eventsattendants AS ea ON (e.id = ea.event)
                WHERE ea.volunteer = :id AND (e.date BETWEEN :dateStart AND :dateEnd)
                ORDER BY e.date
QUERY
            );
            $meetingsEventsDetail->execute([
                ':id' => $_GET['id'],
                ':dateStart' => $_GET['year'] . '-01-01',
                ':dateEnd' => $_GET['year'] . '-12-31'
            ]);


            //intestazione comune
            $pdf = new PDF();
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Times', '', 12);
            $pdf->Cell(0, 10, ' ', 0, 1);


            //titolone pagina
            $pdf->SetFont('Times', 'b', 20);
            $pdf->Cell(0, 10, "Storico di {$user['firstname']} {$user['lastname']} per l'anno {$_GET['year']}", 0, 1);
            $pdf->SetFont('Times', '', 12);

            //sottotitolo Riassunto presenze ai turni serali
            $pdf->Cell(0, 10, ' ', 0, 1);
            $pdf->SetFont('Times', 'iu', 16);
            $pdf->Cell(0, 10, 'Riassunto presenze ai turni serali', 0, 1);
            $pdf->SetFont('Times', '', 12);

            foreach ($allVolunteerTurns as $row) {
                $monthObj = new Month($row['month'], $row['year']);
                $turnCount = $row['count'];
                $pdf->MultiCell(0, 10, "{$monthObj->getMonthName()} {$monthObj->getYear()} - - - $turnCount presenze", 0, 1);
            }

            //sottotitolo Date e Ruoli nei turni serali
            $pdf->Cell(0, 10, ' ', 0, 1);
            $pdf->SetFont('Times', 'iu', 16);
            $pdf->Cell(0, 10, 'Date e Ruoli nei turni serali', 0, 1);
            $pdf->SetFont('Times', '', 12);

            foreach ($allVolunteerTurnsDetail as $row) {
                $monthObj = new Month($row['month'], $row['year']);
                $pdf->MultiCell(0, 10, "{$row['day']} {$monthObj->getMonthName()} {$monthObj->getYear()} - - - {$row['task']} posizione {$row['position']}", 0, 1);
            }

            //sottotitolo Presenze a riunioni/corsi
            $pdf->Cell(0, 10, ' ', 0, 1);
            $pdf->SetFont('Times', 'iu', 16);
            $pdf->Cell(0, 10, 'Presenze a riunioni/corsi', 0, 1);
            $pdf->SetFont('Times', '', 12);

            foreach ($meetingsEventsDetail as $row) {
                $data = explode('-', $row['date']);
                $monthObj = new Month($data[1], $data[0]);
                $pdf->MultiCell(0, 10, "{$data[2]} {$monthObj->getMonthName()} {$monthObj->getYear()} - - - {$row['type']}: \"{$row['title']}\" presso \"{$row['location']}\"", 0, 1);
            }


            $pdf->Output();

        }
    }

    try {
        (new GenerateVolunteerDetailsCPdfCommand(PermissionPage::ADMIN))->execute();
    }
    catch (UnhautorizedException $e) {
        $e->echoAlert();
        exit;
    }
}











