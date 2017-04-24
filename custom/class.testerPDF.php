<?php
require("../custom/fpdf/class.FPDF.php");
session_start();
$documentID = $_POST['formVar'];
if(is_null($documentID)) {
    $title = "IT'S NULL";
} else {
    $title = "GOOD JOB";
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,$title);
$pdf->Output();

?>