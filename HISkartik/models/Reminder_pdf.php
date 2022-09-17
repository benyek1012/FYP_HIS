<?php
namespace app\models;

use TCPDF;

// Extend the TCPDF class to create custom Header and Footer
class Reminder_pdf extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $this->Cell(120);
        // Set font-family and font-size
        $this->SetFont('freeserif','',10);
        // Set the title of pages.
        $this->Cell(160, 8, "Rujukan kami :", 0,'R',0);
        $this->Cell(160, 8, "Tarikh :", 0,'R',0);
        // Break line with given space
        $this->Ln(5);
    }

  
    function content1($rn){
        // Set font-family and font-size
        $this->SetFont('times','B',12);
        
        $this->Cell(70);
        // Set the title of pages.
        $this->Cell(30, 20, 'SURAT PERINGATAN 1', 0, 2, 'C');
          
        // Break line with given space
        $this->Ln(5);
        // Move to the right
        //$this->Cell(20);
        // Loop to display line number content
        $this->SetFont('times','',12);
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
    
        $this->content_bill($rn);

        $this->SetFont('freeserif','',11);
        $this->Cell(160, 15,  "Dengan hormatnya saya adalah diarah merujuk kepada perkara di atas.", 0,'L',0);
        $text = '2.&emsp; &emsp; &emsp;Untuk makluman tuan, bil rawatan tuan/puan berjumlah <b>RM.............</b>
        masih belum dijelaskan. Tuan / Puan dipohon untuk menjelaskan bil rawatan tersebut dalam tempoh <b>14 hari dari
        tarikh surat</b> ini dikeluarkan. Bayaran boleh dibuat secara <b>tunai / kad debit / kad kredit
        di kaunter bayaran Unit Hasil Hospital. Selain itu, bayaran juga boleh dibuat menggunakan
        Wang Pos / Bank Draf</b> yang berpalang atas nama "<b>Pengarah Hospital......</b>".
        Sekiranya tuan/ puan mempunyai sebarang pertanyaan mengenai perkara ini, tuan / puan
        boleh berhubung dengan pegawai di Unit Hasil Hospital.....di <b>talian............ atau melalui emel....</b>*<br/>';

        $this->MultiCell(160, 10,  $text, 0,'FJ', false, 1, '', '', true, 0, true, true, 0, 'T', false);
        $text2 = '3.&emsp; &emsp; &emsp;Sila abaikan surat ini jika pembayaran penuh telah dibuat dan pihak hospital mengucapkan terima kasih.';
        $this->MultiCell(160, 10,  $text2, 0,'J',false, 1, '', '', true, 0, true, true, 0, 'T', false);
        $this->SetFont('times','B',10);
        // Set the title of pages.
        $this->Cell(30, 15, '"BERKHIDMAT UNTUK NEGARA" ', 0, 2, 'L');
        $this->SetFont('freeserif','',11);
        $this->Cell(30, 15, 'Saya yang menurut perintah, ', 0, 2, 'L');
        $this->Cell(30, 8, '(                             ) ', 0, 2, 'L');
        $this->Cell(30, 8, 'b.p Pengarah', 0, 2, 'L');
        $this->Cell(30, 8, 'Hospital.........', 0, 2, 'L');
  
    }

    function content_bill($rn){
       
       $name = "aaaaadsad";
       $bill_no = "232112321";
       $rn = $rn;
       $date_bill = "2022/10/04";
       $amount = "Rm 2312321";
       
        // Set font-family and font-size
        $this->SetFont('times','B',10);
          // Set the title of pages.
        $this->Cell(30, 15, 'Tuan / Puan', 0, 2, 'L');
        $this->Cell(30, 5, 'BAYARAN TUNGGAKAN BIL RAWATAN', 0, 2, 'L');
        $this->Cell(30, 5, 'NAMA PESAKIT     : '.$name, 0, 2, 'L');
        $this->Cell(30, 5, 'NO. BIL                     : '.$bill_no.'                                 TARIKH BIL   : '.$date_bill, 0, 2, 'L');
        $this->Cell(30, 5, 'R/N                             : '.$rn.'                                AMAUN       : '.$amount, 0, 0, 'L');

        $this->WriteHTML('<br><p><hr></p>');
    }

    function content2($rn){

        // Set font-family and font-size
        $this->SetFont('times','B',12);
    
        $this->Cell(70);
        // Set the title of pages.
        $this->Cell(30, 20, 'SURAT PERINGATAN 2', 0, 2, 'C');
        
        // Break line with given space
        $this->Ln(5);
        // Move to the right
        //$this->Cell(20);
        // Loop to display line number content
        $this->SetFont('times','',12);
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
     
        $this->content_bill($rn);


        $this->SetFont('freeserif','',11);
        $this->Cell(160, 15,  "Dengan hormatnya saya adalah diarah merujuk kepada perkara di atas.", 0,'L',0);
        $text = '2.          Untuk makluman tuan, bil rawatan tuan / puan berjumlah RM.. telah tertunggak selama 28 hari. Tuan / Puan dipohon untuk menjelaskan bil rawatan tersebut dalam tempoh 14 hari dari tarikh surat ini dikeluarkan. Bayaran boleh dibuat secara tunai / kad debit / kad kredit di kaunter bayaran Unit Hasil Hospital. Selain itu, bayaran juga boleh dibuat menggunakan Wang Pos Bank Draf yang berpalang atas nama "Pengarah Hospital......". Sekiranya tuan/ puan mempunyai sebarang pertanyaan mengenai perkara ini, tuan / puan boleh berhubung dengan pegawai di Unit Hasil Hospital.....di talian............ atau melalui emel....';
        $this->MultiCell(160, 8,  $text, 0,'FJ',0);
        $text2 = '3.          Kegagalan tuan / puan untuk menjelaskan bayaran boleh menyebabkan tindakan undang-undang dikenakan kepada tuan/puan. Sila abaikan surat ini tika pembayaran penuh telah dibuat dan pihak hospital mengucapkan terima kasih.';
        $this->MultiCell(160, 8,  $text2, 0,'J',0);
        $this->SetFont('times','B',10);
        // Set the title of pages.
        $this->Cell(30, 15, '"BERKHIDMAT UNTUK NEGARA" ', 0, 2, 'L');
        $this->SetFont('freeserif','',11);
        $this->Cell(30, 15, 'Saya yang menurut perintah, ', 0, 2, 'L');
        $this->Cell(30, 8, '(                             ) ', 0, 2, 'L');
        $this->Cell(30, 8, 'b.p Pengarah', 0, 2, 'L');
        $this->Cell(30, 8, 'Hospital.........', 0, 2, 'L');
    }

    function content3($rn){

        // Set font-family and font-size
        $this->SetFont('times','B',12);
       
        $this->Cell(70);
        // Set the title of pages.
        $this->Cell(30, 20, 'SURAT PERINGATAN TERAKHIR', 0, 2, 'C');
          
        // Break line with given space
        $this->Ln(5);
        // Move to the right
        //$this->Cell(20);
        // Loop to display line number content
        $this->SetFont('times','',12);
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
        $this->Cell(30, 5, '..................................................................', 0, 2, 'L');
     
        $this->content_bill($rn);


        $this->SetFont('freeserif','',11);
        $this->Cell(160, 15,  "Dengan hormatnya saya adalah diarah merujuk kepada perkara di atas.", 0,'L',0);
        $text = '2.          Untuk makluman tuan, bil rawatan tuan/puan berjumlah RM..... telah tertunggak selama 42 hari. Tuan / Puan dipohon untuk menjelaskan bil rawatan tersebut dalam tempoh 14 hari dari tarikh surat ini dikeluarkan. Bayaran boleh dibuat secara tunai / kad debit / kad kredit di kaunter bavaran Unit Hasil Hospital. Selain itu, bayaran juga boleh dibuat menggunakan Wang Pos / Bank Draf yang berpalang atas nama "Pengarah Hospital......". Sekiranya tuan / puan mempunyai sebarang pertanyaan mengenai perkara ini, tuan / puan boleh berhubung dengan pegawai di Unit Hasil Hospital.....di talian.......';
        $this->MultiCell(160, 8,  $text, 0,'FJ',0);
        $text2 = '3.          Ini merupakan SURAT PERINGATAN TERAKHIR dan sekiranya tuan/ puan masih belum menjelaskan tunggakan bil rawatan ini, pihak hospital akan merujuk perkara ini kepada Pejabat Penasihat Undang-Undang Kementerian Kesihatan Malaysia untuk tindakan perundangan.';
        $this->MultiCell(160, 8,  $text2, 0,'J',0);
        $this->MultiCell(160, 8,  "Sekian, harap maklum. Terima kasih.", 0,'J',0);
        $this->SetFont('times','B',10);
        // Set the title of pages.
        $this->Cell(30, 10, '"BERKHIDMAT UNTUK NEGARA" ', 0, 2, 'L');
        $this->SetFont('freeserif','',11);
        $this->Cell(30, 15, 'Saya yang menurut perintah, ', 0, 2, 'L');
        $this->Cell(30, 8, '(                             ) ', 0, 2, 'L');
        $this->Cell(30, 8, 'b.p Pengarah', 0, 2, 'L');
        $this->Cell(30, 8, 'Hospital.........', 0, 2, 'L');
   }
}