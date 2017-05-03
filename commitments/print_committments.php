<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 12/03/2017
 * Time: 14:48
 */

require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');
require_once('../lib/permissionString.php');
require_once('../lib/pdf/PDF.php');


require_once('classCommitments.php');


$commitments = new Commitments();

$turns = $commitments->getTurnsArray();
$meetings = $commitments->getMeetingsArray();
$events = $commitments->getEventsArray();






//header comune
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);


$pdf->Cell(0,10,' ',0,1);
$xMargin = $pdf->GetX();

//title
$pdf->SetFont('Times', 'b', 20);
$pdf->Cell(0,10,'Promemoria impegni',0,1);
$pdf->SetFont('Times','',12);

//turni sera
$pdf->Cell(0,10,' ',0,1);
$pdf->SetFont('Times', 'iu', 16);
$pdf->Cell(0,10,'Turni Sera',0,1);
$pdf->SetFont('Times','',12);
foreach ($turns as $turn) {
    if ($turn['month']->isInFuture())
        $pdf->Cell(0,10, "{$turn['day']} {$turn['month']->getMonthName()} {$turn['month']->getYear()} - - - {$turn['task']} posizione {$turn['position']} " ,0,1);
}


//riunioni
$pdf->Cell(0,10,' ',0,1);
$pdf->SetFont('Times', 'iu', 16);
$pdf->Cell(0,10,'Riunioni',0,1);
foreach ($meetings as $meeting) {
    $data = explode('-', $meeting['date']);
    if ( (new Month($data[1], $data[0]))->isInFuture() ) {
        $pdf->SetFont('Times','b',12);
        $pdf->Cell(0,10,$meeting['title'],0,1);
        $pdf->SetFont('Times','',12);
        $pdf->MultiCell(0,10,"Il {$meeting['date']} dalle {$meeting['timeStart']} alle {$meeting['timeEnd']} presso {$meeting['location']}",0,1);
        if ($meeting['requirements'] != '' && $meeting['requirements'] != null )
            $pdf->Write(5, "Sono stati indicati i seguenti requisiti: {$meeting['requirements']} \n");
        if ($commitments->isOverbooked($_SESSION['id'], $meeting['event']))
            $pdf->Cell(0,10,"(Riserva)",0,1);
    }
}


//eventi
$pdf->Cell(0,10,' ',0,1);
$pdf->SetFont('Times', 'iu', 16);
$pdf->Cell(0,10,'Eventi',0,1);
foreach ($events as $event) {
    $data = explode('-', $event['date']);
    if ( (new Month($data[1], $data[0]))->isInFuture() ) {
        $pdf->SetFont('Times','b',12);
        $pdf->Cell(0,10,$event['title'],0,1);
        $pdf->SetFont('Times','',12);
        $pdf->MultiCell(0,10,"Il {$event['date']} dalle {$event['timeStart']} alle {$event['timeEnd']} presso {$event['location']}",0,1);
        if ($event['requirements'] != '' && $event['requirements'] != null )
            $pdf->Write(5, "Sono stati indicati i seguenti requisiti: {$event['requirements']}\n");
        if ($commitments->isOverbooked($_SESSION['id'], $event['event']))
            $pdf->Cell(0,10,"(Riserva)",0,1);
    }
}





$pdf->Output();

