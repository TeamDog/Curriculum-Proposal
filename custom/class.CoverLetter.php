<?php

require("../custom/fpdf/class.FPDF.php");
require_once("class.Bootstrap.php");

class SeedDMS_View_CoverLetter extends SeedDMS_Bootstrap_Style {

    function show() {
        
        $testDoc = $this->params['Document'];
        if(is_null($testDoc)) {
            $title = "IT'S NULL";
        } else {
            $title = "GOOD JOB";
        }
        
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,$title);
        $pdf->Output();
    }

}


?>