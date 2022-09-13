<?php
namespace app\models;

use FPDF;

class Pdf_html extends FPDF
{
    var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';

    function WriteHTML($html)
    {
        //HTML parser
        $html=str_replace("\n",' ',$html);
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN=='center')
                    $this->Cell(0,5,$e,0,1,'C');
                else
                    $this->Write(5,$e);
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract properties
                    $a2=explode(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $prop=array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $prop[strtoupper($a3[1])]=$a3[2];
                    }
                    $this->OpenTag($tag,$prop);
                }
            }
        }
    }

    function OpenTag($tag,$prop)
    {
        //Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')
        {
            if( !empty($prop['WIDTH']) )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

    function SetStyle($tag,$enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
            if($this->$s>0)
                $style.=$s;
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

     // Page header
     function Header()
     {        
         $this->Cell(120);
         // Set font-family and font-size
         $this->SetFont('Arial','',10);
         // Set the title of pages.
         $this->Cell(160, 8, "Rujukan kami :", 0,'R',0);
         $this->Cell(160, 8, "Tarikh :", 0,'R',0);
         // Break line with given space
         $this->Ln(5);
     }
     
     // Page footer
    //  function Footer()
    //  {
    //      // Position at 1.5 cm from bottom
    //      $this->SetY(-15);
         
    //      // Set font-family and font-size of footer.
    //      $this->SetFont('Arial', 'I', 8);
         
    //      // set page number
    //      $this->Cell(0, 10, 'Page ' . $this->PageNo() .
    //          '/{nb}', 0, 0, 'C');
    //  }s
    
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

    function content1(){
        // Set font-family and font-size
        $this->SetFont('Times','B',12);
        
        $this->Cell(70);
        // Set the title of pages.
        $this->Cell(30, 20, 'SURAT PERINGATAN 1', 0, 2, 'C');
          
        // Break line with given space
        $this->Ln(5);
        // Move to the right
        //$this->Cell(20);
        // Loop to display line number content
        $this->SetFont('Times','',12);
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
    
        $this->content_bill();

        $this->SetFont('Arial','',11);
        $this->Cell(160, 15,  "Dengan hormatnya saya adalah diarah merujuk kepada perkara di atas.", 0,'L',0);
        $text = '2.          Untuk makluman tuan, bil rawatan tuan/puan berjumlah RM............. masih belum dijelaskan. Tuan / Puan dipohon untuk menjelaskan bil rawatan tersebut dalam tempoh 14 hari dari tarikh surat ini dikeluarkan. Bayaran boleh dibuat secara tunai / kad debit / kad kredit di kaunter bayaran Unit Hasil Hospital. Selain itu, bayaran juga boleh dibuat menggunakan Wang Pos / Bank Draf yang berpalang atas nama "Pengarah Hospital......". Sekiranya tuan/ puan mempunyai sebarang pertanyaan mengenai perkara ini, tuan / puan boleh berhubung dengan pegawai di Unit Hasil Hospital.....di talian............ atau melalui emel....*';
        $this->MultiCell(160, 8,  $text, 0,'FJ',0);
        $text2 = '3.          Sila abaikan surat ini jika pembayaran penuh telah dibuat dan pihak hospital mengucapkan terima kasih.';
        $this->MultiCell(160, 8,  $text2, 0,'J',0);
        $this->SetFont('Times','B',10);
        // Set the title of pages.
        $this->Cell(30, 15, '"BERKHIDMAT UNTUK NEGARA" ', 0, 2, 'L');
        $this->SetFont('Arial','',11);
        $this->Cell(30, 15, 'Saya yang menurut perintah, ', 0, 2, 'L');
        $this->Cell(30, 8, '(                             ) ', 0, 2, 'L');
        $this->Cell(30, 8, 'b.p Pengarah', 0, 2, 'L');
        $this->Cell(30, 8, 'Hospital.........', 0, 2, 'L');
  
    }

    function content_bill(){
       
       $name = "aaaaadsad";
       $bill_no = "232112321";
       $rn = "2022/000001";
       $date_bill = "2022/10/04";
       $amount = "Rm 2312321";
       
        // Set font-family and font-size
        $this->SetFont('Times','B',10);
          // Set the title of pages.
        $this->Cell(30, 15, 'Tuan / Puan', 0, 2, 'L');
        $this->Cell(30, 5, 'BAYARAN TUNGGAKAN BIL RAWATAN', 0, 2, 'L');
        $this->Cell(30, 5, 'NAMA PESAKIT     : '.$name, 0, 2, 'L');
        $this->Cell(30, 5, 'NO. BIL                     : '.$bill_no.'                                 TARIKH BIL   : '.$date_bill, 0, 2, 'L');
        $this->Cell(30, 5, 'R/N                             : '.$rn.'                                AMAUN       : '.$amount, 0, 0, 'L');

        $this->WriteHTML('<br><hr>');
    }

    function content2(){

        // Set font-family and font-size
        $this->SetFont('Times','B',12);
    
        $this->Cell(70);
        // Set the title of pages.
        $this->Cell(30, 20, 'SURAT PERINGATAN 2', 0, 2, 'C');
        
        // Break line with given space
        $this->Ln(5);
        // Move to the right
        //$this->Cell(20);
        // Loop to display line number content
        $this->SetFont('Times','',12);
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
     
        $this->content_bill();


        $this->SetFont('Arial','',11);
        $this->Cell(160, 15,  "Dengan hormatnya saya adalah diarah merujuk kepada perkara di atas.", 0,'L',0);
        $text = '2.          Untuk makluman tuan, bil rawatan tuan / puan berjumlah RM.. telah tertunggak selama 28 hari. Tuan / Puan dipohon untuk menjelaskan bil rawatan tersebut dalam tempoh 14 hari dari tarikh surat ini dikeluarkan. Bayaran boleh dibuat secara tunai / kad debit / kad kredit di kaunter bayaran Unit Hasil Hospital. Selain itu, bayaran juga boleh dibuat menggunakan Wang Pos Bank Draf yang berpalang atas nama "Pengarah Hospital......". Sekiranya tuan/ puan mempunyai sebarang pertanyaan mengenai perkara ini, tuan / puan boleh berhubung dengan pegawai di Unit Hasil Hospital.....di talian............ atau melalui emel....';
        $this->MultiCell(160, 8,  $text, 0,'FJ',0);
        $text2 = '3.          Kegagalan tuan / puan untuk menjelaskan bayaran boleh menyebabkan tindakan undang-undang dikenakan kepada tuan/puan. Sila abaikan surat ini tika pembayaran penuh telah dibuat dan pihak hospital mengucapkan terima kasih.';
        $this->MultiCell(160, 8,  $text2, 0,'J',0);
        $this->SetFont('Times','B',10);
        // Set the title of pages.
        $this->Cell(30, 15, '"BERKHIDMAT UNTUK NEGARA" ', 0, 2, 'L');
        $this->SetFont('Arial','',11);
        $this->Cell(30, 15, 'Saya yang menurut perintah, ', 0, 2, 'L');
        $this->Cell(30, 8, '(                             ) ', 0, 2, 'L');
        $this->Cell(30, 8, 'b.p Pengarah', 0, 2, 'L');
        $this->Cell(30, 8, 'Hospital.........', 0, 2, 'L');
    }

    function content3(){

        // Set font-family and font-size
        $this->SetFont('Times','B',12);
       
        $this->Cell(70);
        // Set the title of pages.
        $this->Cell(30, 20, 'SURAT PERINGATAN TERAKHIR', 0, 2, 'C');
          
        // Break line with given space
        $this->Ln(5);
        // Move to the right
        //$this->Cell(20);
        // Loop to display line number content
        $this->SetFont('Times','',12);
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
     
        $this->content_bill();


        $this->SetFont('Arial','',11);
        $this->Cell(160, 15,  "Dengan hormatnya saya adalah diarah merujuk kepada perkara di atas.", 0,'L',0);
        $text = '2.          Untuk makluman tuan, bil rawatan tuan/puan berjumlah RM..... telah tertunggak selama 42 hari. Tuan / Puan dipohon untuk menjelaskan bil rawatan tersebut dalam tempoh 14 hari dari tarikh surat ini dikeluarkan. Bayaran boleh dibuat secara tunai / kad debit / kad kredit di kaunter bavaran Unit Hasil Hospital. Selain itu, bayaran juga boleh dibuat menggunakan Wang Pos / Bank Draf yang berpalang atas nama "Pengarah Hospital......". Sekiranya tuan / puan mempunyai sebarang pertanyaan mengenai perkara ini, tuan / puan boleh berhubung dengan pegawai di Unit Hasil Hospital.....di talian.......';
        $this->MultiCell(160, 8,  $text, 0,'FJ',0);
        $text2 = '3.          Ini merupakan SURAT PERINGATAN TERAKHIR dan sekiranya tuan/ puan masih belum menjelaskan tunggakan bil rawatan ini, pihak hospital akan merujuk perkara ini kepada Pejabat Penasihat Undang-Undang Kementerian Kesihatan Malaysia untuk tindakan perundangan.';
        $this->MultiCell(160, 8,  $text2, 0,'J',0);
        $this->MultiCell(160, 8,  "Sekian, harap maklum. Terima kasih.", 0,'J',0);
        $this->SetFont('Times','B',10);
        // Set the title of pages.
        $this->Cell(30, 10, '"BERKHIDMAT UNTUK NEGARA" ', 0, 2, 'L');
        $this->SetFont('Arial','',11);
        $this->Cell(30, 15, 'Saya yang menurut perintah, ', 0, 2, 'L');
        $this->Cell(30, 8, '(                             ) ', 0, 2, 'L');
        $this->Cell(30, 8, 'b.p Pengarah', 0, 2, 'L');
        $this->Cell(30, 8, 'Hospital.........', 0, 2, 'L');
   }
}
?>