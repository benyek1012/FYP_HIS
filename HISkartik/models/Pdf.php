<?php
namespace app\models;

use FPDF;

class Pdf extends FPDF
{
    // Page header
    function Header()
    {        
        // Set font-family and font-size
        $this->SetFont('Times','B',12);
        
        $this->Cell(70);
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
    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $k=$this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
        {
            $x=$this->x;
            $ws=$this->ws;
            if($ws>0)
            {
                $this->ws=0;
                $this->_out('0 Tw');
            }
            $this->AddPage($this->CurOrientation);
            $this->x=$x;
            if($ws>0)
            {
                $this->ws=$ws;
                $this->_out(sprintf('%.3F Tw', $ws*$k));
            }
        }
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $s='';
        if($fill || $border==1)
        {
            if($fill)
                $op=($border==1) ? 'B' : 'f';
            else
                $op='S';
            $s=sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x*$k, ($this->h-$this->y)*$k, $w*$k, -$h*$k, $op);
        }
        if(is_string($border))
        {
            $x=$this->x;
            $y=$this->y;
            if(is_int(strpos($border, 'L')))
                $s.=sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, $x*$k, ($this->h-($y+$h))*$k);
            if(is_int(strpos($border, 'T')))
                $s.=sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-$y)*$k);
            if(is_int(strpos($border, 'R')))
                $s.=sprintf('%.2F %.2F m %.2F %.2F l S ', ($x+$w)*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
            if(is_int(strpos($border, 'B')))
                $s.=sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-($y+$h))*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
        }
        if($txt!='')
        {
            if($align=='R')
                $dx=$w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C')
                $dx=($w-$this->GetStringWidth($txt))/2;
            elseif($align=='FJ')
            {
                //Set word spacing
                $wmax=($w-2*$this->cMargin);
                $nb=substr_count($txt, ' ');
                if($nb>0)
                    $this->ws=($wmax-$this->GetStringWidth($txt))/$nb;
                else
                    $this->ws=0;
                $this->_out(sprintf('%.3F Tw', $this->ws*$this->k));
                $dx=$this->cMargin;
            }
            else
                $dx=$this->cMargin;
            $txt=str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));
            if($this->ColorFlag)
                $s.='q '.$this->TextColor.' ';
            $s.=sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $txt);
            if($this->underline)
                $s.=' '.$this->_dounderline($this->x+$dx, $this->y+.5*$h+.3*$this->FontSize, $txt);
            if($this->ColorFlag)
                $s.=' Q';
            if($link)
            {
                if($align=='FJ')
                    $wlink=$wmax;
                else
                    $wlink=$this->GetStringWidth($txt);
                $this->Link($this->x+$dx, $this->y+.5*$h-.5*$this->FontSize, $wlink, $this->FontSize, $link);
            }
        }
        if($s)
            $this->_out($s);
        if($align=='FJ')
        {
            //Remove word spacing
            $this->_out('0 Tw');
            $this->ws=0;
        }
        $this->lasth=$h;
        if($ln>0)
        {
            $this->y+=$h;
            if($ln==1)
                $this->x=$this->lMargin;
        }
        else
            $this->x+=$w;
    }
    function content(){
        // Move to the right
        //$this->Cell(20);
        // Loop to display line number content
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->content_bill();
        $this->SetFont('Arial','',11);
        $text = '2.          Untuk makluman tuan, bil rawatan tuan/puan berjumlah RM. masih belum dijelaskan. Tuan / Puan dipohon untuk menjelaskan bil rawatan tersebut dalam tempoh 14 hari dari tarikh surat ini dikeluarkan. Bayaran boleh dibuat secara tunai / kad debit / kad kredit di kaunter bayaran Unit Hasil Hospital. Selain itu, bayaran juga boleh dibuat menggunakan Wang Pos / Bank Draf yang berpalang atas nama "Pengarah Hospital......". Sekiranya tuan/ puan mempunyai sebarang pertanyaan mengenai perkara ini, tuan / puan boleh berhubung dengan pegawai di Unit Hasil Hospital.....di talian............ atau melalui emel....*';
        $this->MultiCell(160, 8,  $text, 0,'FJ',0);
        $text2 = '3.          Sila abaikan surat ini jika pembayaran penuh telah dibuat dan pihak hospital mengucapkan terima kasih.';
        $this->MultiCell(160, 8,  $text2, 0,'J',0);
        $this->SetFont('Times','B',10);
        // Set the title of pages.
        $this->Cell(30, 15, '"BERKHIDMAT UNTUK NEGARA" ', 0, 2, 'L');
        $this->SetFont('Arial','',11);
        $this->Cell(30, 15, 'Saya yang menurut perintah, ', 0, 2, 'L');
        $this->Cell(30, 8, '(                             ), ', 0, 2, 'L');
        $this->Cell(30, 8, 'b.p Pengarah', 0, 2, 'L');
        $this->Cell(30, 8, 'Hospital.........', 0, 2, 'L');
  
    }

    function content_bill(){
        // Move to the right
        //$this->Cell(20); 
        // Set font-family and font-size
        $this->SetFont('Times','B',11);
          // Set the title of pages.
        $this->Cell(30, 15, 'Tuan / Puan', 0, 2, 'L');
        $this->SetFont('Times','B',10);
        $this->Cell(30, 8, 'BAYARAN TUNGGAKAN BIL RAWATAN', 0, 2, 'L');
    
    }
}

