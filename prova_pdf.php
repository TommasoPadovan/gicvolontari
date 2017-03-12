<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 12/03/2017
 * Time: 10:35
 */

require('lib/pdf/PDF.php');

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
    $pdf->Cell(0,10,'Printing line number '.$i,0,1);
$pdf->Output();