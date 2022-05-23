<?php

namespace app\models;
require 'vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Yii;

class PrintForm
{
    const BorangDaftarMasuk = 0;
   //const printerStringForBorangDaftarMasuk = "smb://DESKTOP-7044BNO/Epson"; //"smb://DESKTOP-7044BNO/Epson" Yii::$app->params['borangdafter']

    const BorangCajSheet= 0;
    const printerStringForBorangCajSheet = "smb://DESKTOP-7044BNO/Epson"; //smb://JOSH2-LAPTOP/Epson

    const BorangCaseNote= 0;
    const printerStringForBorangCaseNote = "smb://DESKTOP-7044BNO/Epson";

    const BorangSticker= 0;
    const printerSticker = "smb://DESKTOP-7044BNO/Epson";
    const Receipt= 0;
    const printerStringForReceipt= "smb://DESKTOP-7044BNO/Epson";

    const Bill= 0;
    const printerStringForBill= "smb://DESKTOP-7044BNO/Epson";

    public $formtype = null;

    private $connector = null;
    private $printer = null;

    public function __construct($formtype)
    {
        $printerStringForBorangDaftarMasuk = Yii::$app->params['borangdafter'];

        $this->connector = null;
        $this->formtype = $formtype;

        if($formtype == PrintForm::BorangDaftarMasuk){
            $this->connector = new WindowsPrintConnector($printerStringForBorangDaftarMasuk);
            
        }
        $this->printer = new Printer($this->connector);


        if($formtype == PrintForm::BorangCajSheet){
            $this->connector = new WindowsPrintConnector(PrintForm::printerStringForBorangCajSheet);
        }
        $this->printer = new Printer($this->connector);


        if($formtype == PrintForm::BorangCaseNote){
            $this->connector = new WindowsPrintConnector(PrintForm::printerStringForBorangCaseNote);
           
        }
        $this->printer = new Printer($this->connector);

        if($formtype == PrintForm::BorangSticker){
            $this->connector = new WindowsPrintConnector(PrintForm::printerSticker);
          
        }
        $this->printer = new Printer($this->connector);
        if($formtype == PrintForm::Receipt){
            $this->connector = new WindowsPrintConnector(PrintForm::printerStringForReceipt);
            // $this->printer = new Printer($this->connector);
        }
        $this->printer = new Printer($this->connector);
        if($formtype == PrintForm::Bill){
            $this->connector = new WindowsPrintConnector(PrintForm::printerStringForBill);
            // $this->printer = new Printer($this->connector);
        }
        $this->printer = new Printer($this->connector);


    }

    public function printElement($len, $value, $uppercase=false)
    {
        if($uppercase)
            $value = strtoupper($value);

        if($value == "\n"){
            $this->printNewLine($len);
        }
        
        $this->printer -> text(mb_strimwidth($value,0,$len));

        $blanklen = $len - strlen($value);

        if($blanklen < 0)
            $blanklen = 0;

        $this->printer -> text(str_repeat("\x20", $blanklen)); // space for r/n value
    }

    public function printElementArray($array)
    {
        for($i = 0; $i < count($array); $i++){
            if(count($array[$i]) < 3)
                $this->printElement($array[$i][0], $array[$i][1]);
            else
                $this->printElement($array[$i][0], $array[$i][1], $array[$i][2]);
        }
        // if(count($array) < 3)
        //     $this->printElement($array[0], $array[0]);
        // else
        //     $this->printElement($array[0], $array[0], $array[0]);

    }

    public function printNewLine($len){
        $newLine = "";
        for($i = 0; $i < $len; $i++){
            $newLine .= "\n";
        }
        $this->printer -> text($newLine);
    }

    public function close()
    {
        
        $this->printer->close();
    }

    // public function printTotalTreamentUnitCost($totalCostTreatment, $totalUnitCost){
    //     $totalCostTreatment += $totalUnitCost;
        
    //     return $totalCostTreatment;
    // }

    // public function printTotalDeposit($totalCostReceipt, $receipt_content_sum){
    //     $totalCostReceipt += $receipt_content_sum;

    //     return $totalCostReceipt;
    // }

    public function printMore($totalCost){
        $this->printElementArray(
            [
                [6, "\x20"],
                [4, "...."],
                [6, "\x20"],
                [9, number_format((float)$totalCost, 2, '.', '')],
            ]
     
        );
    }

    // public function printMoreForDepositRefund($totalCost){
    //     $this->printElementArray(
    //         [
    //             [6, "\x20"],
    //             [4, "...."],
    //             [6, "\x20"],
    //             [9, number_format((float)"-".$totalCost, 2, '.', '')],
    //         ]
    //     );
    // }

    public function printBillTreatment($bill_uid, $treatment_code, $treatment_name, $item_count, $item_per_unit_cost, $item_total_unit_cost){
        $this->printElementArray(
            [
                [6, "\x20"],
                [5, $treatment_code, true],
                [1,"\x20"],
                [30, $treatment_name,true],
                [2,"\x20"],
                [1,"x"],
                [2,"\x20"],
                [5,$item_count],
                [7,"\x20"],
                [8, $item_per_unit_cost],
                [8, $item_total_unit_cost],

            ]
        );
        $this->printNewLine(1);
    }

    // public function printMoreDeposit($totalCostReceipt){
    //     $this->printElementArray(
    //         [
    //             [6, "\x20"],
    //             [4, "...."],
    //             [6, "\x20"],
    //             [9, number_format((float)$totalCostReceipt, 2, '.', '')],
    //         ]
    //     );
    //     $this->printNewLine(1);
    // }

    public function printBillDeposit($rn, $receipt_serial_number, $receipt_content_sum){
        $this->printElementArray(
            [
                [8, "\x20"],
                [14, "Tolak Cagaran "],
                [8,$receipt_serial_number],
                [38,"\x20"],
                [9, $receipt_content_sum],
            
            ]
        );
        $this->printNewLine(1);  
    }

    public function printBillDailyTreatment($billAble){
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [8, "\x20"],
                [18, "Caj Rawatan Harian"],
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [8, "\x20"],
                [18, "------------------"],
                [45, "\x20"],
                [9, "0.00"], // need ask what price is this for
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [67, "\x20"],
                [10, "----------"],
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [68, "\x20"],
                [9, $billAble],
            ]
        );
        $this->printNewLine(1);
    }

    public function printCajRawatenHarian(){
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [8, "\x20"],
                [18, "Caj Rawatan Harian"],
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [8, "\x20"],
                [18, "------------------"],
                [45, "\x20"],
                [9, "0.00"], // need ask what price is this for
            ]
        );
        $this->printNewLine(3);
    }

    public function printBillRefund($rn, $receipt_serial_number, $receipt_content_sum){
        $this->printElementArray(
            [
                [8, "\x20"],
                [14, "Refund "],
                [8,$receipt_serial_number],
                [38,"\x20"],
                [9, "-".$receipt_content_sum],
            
            ]
        );
        $this->printNewLine(1);  
    }

    public function print($formtype)
    {
        // if($formtype == "")
        // $formtype = ...
            // printThisForm();
    }

    // public static function printRegisterationForm()
    // {

    //     $box1 = "";
    //     $box2 = "";
    //     $box3 = "";
    //     $box4 = "";
    //     $box5 = "";
    //     $box6 = "";
    //     $box7 = "";
    //     $box8 = "";
    //     $box9 = "";
    //     $box10 = "";
    //     $box11 = "";
    //     $box12 = "";
    //     $box13 = "";
    //     $box14 = "";
    //     $box15 = "";
    //     $box16 = "";
    //     $box17 = "";
    //     $box18 = "";
    //     $box19 = "";
    //     $box20 = "";
    //     $box21 = "";
    //     $box22 = "";
    //     $box23 = "";
    //     $box24 = "";
    //     $box25 = "";
    //     $box26 = "";
    //     $box27 = "";
    //     $box28 = "";


    // }

    // public static function printChargeSheet()
    // {
    //     $box1 = "";
    //     $box2 = "";
    //     $box3 = "";
    //     $box4 = "";
    //     $box5 = "";
    //     $box6 = "";
    //     $box7 = "";
    //     $box8 = "";
    //     $box9 = "";
    //     $box10 = "";
    //     $box11 = "";
    //     $box12 = "";
    //     $box13 = "";
    //     $box14 = "";
    //     $box15 = "";
    //     $box16 = "";
    //     $box17 = "";
    //     $box18 = "";
    //     $box19 = "";
    //     $box20 = "";
    //     $box21 = "";
    //     $box22 = "";
    //     $box23 = "";
    //     $box24 = "";
    //     $box25 = "";
    //     $box26 = "";
    //     $box27 = "";
    //     $box28 = "";

        
    // }

    // public static function printCaseHistorySheet()
    // {
    //     $box1 = "";
    //     $box2 = "";
    //     $box3 = "";
    //     $box4 = "";
    //     $box5 = "";
    //     $box6 = "";
    //     $box7 = "";
    //     $box8 = "";
    //     $box9 = "";
    //     $box10 = "";
    //     $box11 = "";
    //     $box12 = "";
    //     $box13 = "";
    //     $box14 = "";
    //     $box15 = "";
    //     $box16 = "";
    //     $box17 = "";
    //     $box18 = "";
    //     $box19 = "";
    //     $box20 = "";
    //     $box21 = "";
    //     $box22 = "";
    //     $box23 = "";
    //     $box24 = "";

        
    // }

    // public static function printSticker()
    // {
    //     $box1 = "";
    //     $box2 = "";
    //     $box3 = "";
    //     $box4 = "";
    //     $box5 = "";
    //     $box6 = "";
    //     $box7 = "";
    //     $box8 = "";
    //     $box9 = "";
    //     $box10 = "";
    //     $box11 = "";
    //     $box12 = "";
    //     $box13 = "";
    //     $box14 = "";
    //     $box15 = "";
    //     $box16 = "";

        
    // }

    // public static function printBill()
    // {
    //     $box1 = "";
    //     $box2 = "";
    //     $box3 = "";
    //     $box4 = "";
    //     $box5 = "";
    //     $box6 = "";
    //     $box7 = "";
    //     $box8 = "";
    //     $box9 = "";
    //     $box10 = "";
    //     $box11 = "";
    //     $box12 = "";
    //     $box13 = "";
    //     $box14 = "";
    //     $box15 = "";
    //     $box16 = "";
    //     $box17 = "";

        
    // }


    // public static function printReceipt()
    // {
    //     $box1 = "";
    //     $box2 = "";
    //     $box3 = "";
    //     $box4 = "";
    //     $box5 = "";
    //     $box6 = "";
    //     $box7 = "";
    //     $box8 = "";
    //     $box9 = "";
    //     $box10 = "";
    //     $box11 = "";
  
        
    // }

} 
