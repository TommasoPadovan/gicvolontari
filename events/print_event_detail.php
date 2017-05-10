<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 22/04/17
 * Time: 17.15
 */

require_once('eventDetail.php');
require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/GetConstraints.php');
require_once('../lib/pdf/PDF.php');



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
    //loading event details
    $event = $db->select('events', [
        'id' => $_GET['id']
    ])[0];

    //loading attendants IDs list
    $eventsAttendantsIDsList = $db->select('eventsattendants', [
        'event' => $event['id']
    ]);

    $type = $event['type'];
    if ($type == "riunione") $type = "Riunione";
    if ($type == "evento") $type = "Evento";

    $title = $event['title'];
    $date = (new Date($event['date']))->getItalianDate();
    $timeStart = (new Time($event['timeStart']))->getSimpleTime();
    $timeEnd = (new Time($event['timeEnd']))->getSimpleTime();
    $location = $event['location'];
    $description = $event['description'];
    $requirements = $event['requirements'];
    $resoconto = $event['resoconto'];
    $minAttendants = $event['minAttendants'];
    $maxAttendants = $event['maxAttendants'];
    $who = unserialize($event['who']);

    //intestazione comune
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(0, 10, ' ', 0, 1);

    //titolone pagina
    $pdf->SetFont('Times', 'b', 20);
    $pdf->MultiCell(0, 10, "$type: $title", 0, 1);
    $pdf->SetFont('Times', '', 12);

    //data ora
    $pdf->MultiCell(0, 10, "$date, dalle $timeStart alle $timeEnd", 0, 1);

    //luogo
    $pdf->MultiCell(0, 10, "Presso: $location", 0, 1);

    //descrizione
    if ($description != '') {
        $pdf->Cell(0, 10, ' ', 0, 1);
        $pdf->SetFont('Times', 'iu', 16);
        $pdf->Cell(0, 10, 'Descrizione', 0, 1);
        $pdf->SetFont('Times', '', 12);

        $pdf->MultiCell(0, 10, $description, 0, 1);
    }

    //requisiti
    if ($requirements != '') {
        $pdf->Cell(0, 10, ' ', 0, 1);
        $pdf->SetFont('Times', 'iu', 16);
        $pdf->Cell(0, 10, 'Requisiti', 0, 1);
        $pdf->SetFont('Times', '', 12);

        $pdf->MultiCell(0, 10, $requirements, 0, 1);
    }

    //attendants
    $pdf->Cell(0, 10, ' ', 0, 1);
    $pdf->SetFont('Times', 'iu', 16);
    $pdf->Cell(0, 10, 'Presenze', 0, 1);
    $pdf->SetFont('Times', '', 12);

    $counter=1;
    foreach ($eventsAttendantsIDsList as $row) {
        $pdf->Cell(0, 10,
            "$counter. ".$db->getUserName($row['volunteer'])."\t_____________________",
            0, 1);
        $counter++;
    }

    //resoconto/verbale
    if ($resoconto != '') {
        $pdf->Cell(0, 10, ' ', 0, 1);
        $pdf->SetFont('Times', 'iu', 16);
        $pdf->Cell(0, 10, 'Resoconto / Verbale', 0, 1);
        $pdf->SetFont('Times', '', 12);

        $pdf->MultiCell(0, 10, $resoconto, 0, 1);
    }




    $pdf->Output();
} else {
    $content = $constraints->getErrorContent();
    try {
        $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."events/eventsandcourses.php", PermissionPage::MORNING);
        $generalLayout->yieldElem('title', "Stampa Dettagli Evento");
        $generalLayout->yieldElem('content', $content);
        echo $generalLayout->getPage();
    }
    catch (UnhautorizedException $e){
        $e->echoAlert();
        exit;
    }
}