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

require_once('classCommitments.php');


$commitments = new Commitments();






$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);


$pdf->Cell(0,10,'',0,1);


$pdf->Output();

