<?php
namespace app\models;

use FPDF;

class Pdf extends FPDF
{
    // Page header
    function Header()
    {        
        // Set font-family and font-size
        $this->SetFont('Times','B',10);
        
        // Move to the right
        $this->Cell(80);
        
        // Set the title of pages.
        $this->Cell(30, 25, 'SURAT PERINGATAN 1', 0, 2, 'C');
        
        // Break line with given space
        $this->Ln(5);
    }
    
    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        
        // Set font-family and font-size of footer.
        $this->SetFont('Arial', 'I', 8);
        
        // set page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() .
            '/{nb}', 0, 0, 'C');
    }

    function content(){
        // Move to the right
        $this->Cell(20);
        // Loop to display line number content
        for($i = 0; $i < 4; $i++)
            $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->content_bill();
    }

    function content_bill(){
        // Move to the right
        //$this->Cell(20);
        // Set font-family and font-size
        $this->SetFont('Times','B',10);
          // Set the title of pages.
        $this->Cell(30, 15, 'Tuan / Puan', 0, 2, 'L');
        $this->Cell(30, 10, 'BAYARAN TUNGGAKAN BIL RAWATAN', 0, 2, 'L');
        $this->Cell(30, 0, 'NAMA PESAKIT     :', 0, 2, 'L');
        

        

    }
}

