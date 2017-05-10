<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 12/03/2017
 * Time: 10:38
 */

require_once('fpdf/fpdf.php');

class PDF extends FPDF {

    // Page header
    function Header(){
        // Logo
        $this->Image("../img/gic.png",10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'GIC',1,0,'C');
        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer(){
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        $convertedTxt = utf8_decode($txt);
        parent::Cell($w, $h, $convertedTxt, $border, $ln, $align, $fill, $link);
    }

//    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false){
//        $convertedTxt = utf8_decode($txt);
//        parent::MultiCell($w, $h, $convertedTxt, $border, $align, $fill);
//    }
}