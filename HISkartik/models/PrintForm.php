<?php

namespace app\models;
require 'vendor/autoload.php';
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Yii;
use Exception;

class PrintForm
{
    const BorangDaftarMasuk = 1;
   //const printerStringForBorangDaftarMasuk = "smb://DESKTOP-7044BNO/Epson"; //"smb://DESKTOP-7044BNO/Epson" Yii::$app->params['borangdafter']

    const BorangCajSheet= 2;
   // const printerStringForBorangCajSheet = "smb://DESKTOP-7044BNO/Epson"; //smb://JOSH2-LAPTOP/Epson

    const BorangCaseNote= 3;
    //const printerStringForBorangCaseNote = "smb://DESKTOP-7044BNO/Epson";

    const BorangSticker= 4;
    //const printerSticker = "smb://DESKTOP-7044BNO/Epson";
    const Receipt= 5;
    //const printerStringForReceipt= "smb://DESKTOP-7044BNO/Epson";

    const Bill= 6;
	
	const Bill2= 7;
   // const printerStringForBill= "smb://DESKTOP-7044BNO/Epson";

    public $formtype = null;

    private $connector = null;
    private $printer = null;

	private $borangdafter_offset = null;
	private $chargesheet_offset = null;
	private $casehistory_offset = null;
	private $sticker_offset = null;
	private $receipt_offset = null;
	private $bill_offset = null;


    public function __construct($formtype)
    {
        //Yii::$app->params['borangdafter'] = Yii::$app->params['borangdafter'];
        $printerStringForBorangCajSheet = Yii::$app->params['chargesheet'];
        $printerStringForBorangCaseNote = Yii::$app->params['casehistory'];
        $printerSticker = Yii::$app->params['sticker'];
        $printerStringForReceipt = Yii::$app->params['receipt'];
        $printerStringForBill = Yii::$app->params['bill'];
		
		// test printer bill
		$printerStringForBill2 = Yii::$app->params['bill2'];

		$this->borangdafter_offset = Yii::$app->params['borangdafter_offset'];
		$this->chargesheet_offset = Yii::$app->params['chargesheet_offset'];
		$this->casehistory_offset = Yii::$app->params['casehistory_offset'];
		$this->sticker_offset = Yii::$app->params['sticker_offset'];
		$this->receipt_offset = Yii::$app->params['receipt_offset'];
		$this->bill_offset = Yii::$app->params['bill_offset'];

        $this->connector = null;
        $this->formtype = $formtype;
				
        if($formtype == PrintForm::BorangDaftarMasuk){
            $this->connector = new WindowsPrintConnector(Yii::$app->params['borangdafter']);
        }
       
        if($formtype == PrintForm::BorangCajSheet){
            $this->connector = new WindowsPrintConnector($printerStringForBorangCajSheet);
        }
		
        if($formtype == PrintForm::BorangCaseNote){
            $this->connector = new WindowsPrintConnector($printerStringForBorangCaseNote);
        }

        if($formtype == PrintForm::BorangSticker){
            $this->connector = new WindowsPrintConnector($printerSticker);
        }
        //$this->printer = new Printer($this->connector);
        if($formtype == PrintForm::Receipt){
            $this->connector = new WindowsPrintConnector($printerStringForReceipt);
        }
       // $this->printer = new Printer($this->connector);
        if($formtype == PrintForm::Bill){
            $this->connector = new WindowsPrintConnector($printerStringForBill);
        }
		
		if($formtype == PrintForm::Bill2){
            $this->connector = new WindowsPrintConnector($printerStringForBill2);
        }
		
		$this->printer = new Printer($this->connector);
    }

    public function printElement($len, $value, $uppercase=false, $rightalign=false)
    {
		if(is_null($value)){
			$value = " ";
		}
		if($value == ""){
			$value = " ";
		}

		if($value != "")
		{
			if($uppercase)
				$value = strtoupper($value);

			if($value == "\n"){
				$this->printNewLine($len);
			}
			else
			{
			
				if($rightalign)
				{
					$fixvalue = $len - strlen($value);
					if($fixvalue < 0){
						$fixvalue = 0;
					}
					
					$this->printer -> text(str_repeat("\x20", $fixvalue));
					$this->printer -> text(mb_strimwidth($value,0,$len));
				}
				else{        
					$this->printer -> text(mb_strimwidth($value,0,$len));

					$blanklen = $len - strlen($value);

					if($blanklen < 0)
						$blanklen = 0;

					$this->printer -> text(str_repeat("\x20", $blanklen)); 
				}
			}
		}
    }

    public function printElementArray($array)
    {
        for($i = 0; $i < count($array); $i++){
            if(count($array[$i]) < 3)
                $this->printElement($array[$i][0], $array[$i][1]);
            else if(count($array[$i]) == 3)
                $this->printElement($array[$i][0], $array[$i][1], $array[$i][2]);
            else{
                $this->printElement($array[$i][0], $array[$i][1], $array[$i][2], $array[$i][3]);
            }
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
    
	public function escp2ResetPaper()
	{
		$this->printer->connector->write( "\x1b" . "\x19" . "B"); //or F
		
	}
	public function escp2EjectPaper()
	{
		$this->printer->connector->write( "\x0C"); //command FF
		//$this->printer->connector->write( "\x1b" . "\x19" . "R"); //command Eject but doesnt do anything. FF is better
	}
	public function escp2SetTypeface($int)
	{
		$this->printer->connector->write( "\x1b" . "x" . 1);
		$this->printer->connector->write( "\x1b" . "k" . $int);
	}
	public function escp2SetTypefaceDraft()
	{
		$this->printer->connector->write( "\x1b" . "x" . 0);
	}
	public function escp2SetTiny()
	{
		$this->printer->connector->write( "\x0f");
	}
	public function escp2UnsetTiny()
	{
		$this->printer->connector->write( "\x13");
	}
	
    public function close()
    {
        
        $this->printer->close();
    }


    public function printMore($totalCost){
        $this->printElementArray(
            [
                [8, "\x20"],
                [4, "...."],
                [55, "\x20"],
                [9, number_format((float)$totalCost, 2, '.', ''),false,true],
            ]
     
        );
    }

	public static function printAdmissionForm($rn)
    {
		$patientAdmission = Patient_admission::findOne(["rn"=>$rn]);
		if (is_null($patientAdmission))
			return Yii::t('app',"RN can't be found in database");
		
		if (Yii::$app->params['printerstatus'] != "true")
			return Yii::t('app','Printing was not enabled');

		$patientInformation = $patientAdmission->getPatient_information()->one();
			
		$form = new PrintForm(PrintForm::BorangDaftarMasuk);
		
		//var_dump(Yii::$app->params['borangdafter']);
		//exit;
		
		// Begin Print
		$form->escp2ResetPaper();
		if(Yii::$app->params['printeroverwritefont'] == "true")
			$form->escp2SetTypeface(0);
		$form->printNewLine(2);

		// (name and rn)
		if(strlen($patientInformation->name) > 32){
			$first = substr($patientInformation->name, 0, 32);
			$second = substr($patientInformation->name, 33, 65);

			$form->printElementArray(
				[
					[$form->borangdafter_offset, "\x20"],
					[11, "\x20"],
					[32, $first, true],
					[20,"\x20"],
					[11, $rn]
				]
			);
			$form->printNewLine(1);

			$form->printElementArray(
				[
					[$form->borangdafter_offset, "\x20"],
					[9, "\x20"],
					[32, $second, true],
				]
			);
			$form->printNewLine(1);
		}
		else{
			$form->printElementArray(
				[
				//[5, "\n"],
					[$form->borangdafter_offset, "\x20"],
					[9, "\x20"],
					[32, $patientInformation->name, true],
					[20,"\x20"],
					[11, $rn]
				]
			);
			$form->printNewLine(2);
		}
		
		// // (address and ic)
		// $form->printElementArray(
		// 	[
		// 		[56,"\x20"],
		// 		[14, $patientInformation->nric],
		// 	]
		// );
		$form->printNewLine(1);
		
		$form->printElementArray(
			[
				[$form->borangdafter_offset, "\x20"],
				[5, "\x20"],
				[38, $patientInformation->address1,true],
				[13,"\x20"],
				[14, $patientInformation->nric],
			]
		);
		$form->printNewLine(1);

		// (address 2)
		$form->printElementArray(
			[
				[$form->borangdafter_offset, "\x20"],
				[5,"\x20"],
				[38, $patientInformation->address2,true],
			]
		);
		$form->printNewLine(1);

		// (address 3 and phone number)
		$form->printElementArray( 
			[  
				[$form->borangdafter_offset, "\x20"],
				[5,"\x20"],  
				[38, $patientInformation->address3,true],
				[13,"\x20"], 
				[15, $patientInformation->phone_number],  
			]
		);
		
		$form->printNewLine(4);

		$race = $patientInformation->race;
		$raceResult = Lookup_general::findOne(["code"=>$race, "category"=>"Race"]);
		if(!is_null($raceResult))
			$race = $raceResult->name;
		$form->printElementArray( 
			[  
				[$form->borangdafter_offset, "\x20"],
				[10,"\x20"],
				[9,$patientInformation->sex,true],
				[4,"\x20"],
				[10,$patientInformation->getDOB_format()],
				[3,"\x20"],
				[11,$patientInformation->getAge("%y, %m, %d")],
				[2, "\x20"],
				[10,$race,true],
				[8, "\x20"],
				[9,$patientInformation->nationality,true],
			]
		);
		$form->printNewLine(2);
		
		$firstNOK = $patientInformation->getLatestNOK();
		if (!is_null($firstNOK))
		{
			$form->printElementArray( 
				[  
					  //[6, "\x20"],
					//[ntsure, $agama],
					[$form->borangdafter_offset, "\x20"],
					[43, "\x20"],
					[35,$firstNOK->nok_name,true],
				]
			);
			$form->printNewLine(2);
			$form->printElementArray( 
				[  
					[$form->borangdafter_offset, "\x20"],
					[43, "\x20"],  //52-13
					[35,$firstNOK->nok_address1,true],
				]
			);
			$form->printNewLine(1);
			$form->printElementArray( 
				[  
					[$form->borangdafter_offset, "\x20"],
					[43, "\x20"],
					[35,$firstNOK->nok_address2,true],
				]
			);
			$form->printNewLine(1);
			$form->printElementArray( 
				[  
					[$form->borangdafter_offset, "\x20"],
					[43, "\x20"],
					[35,$firstNOK->nok_address3,true],
				]
			);
			$form->printNewLine(2);
		}
		else
		{
			$form->printNewLine(6);
		}
		
   
		$form->printElementArray( 
			[  
				[$form->borangdafter_offset, "\x20"],
				[11,"\x20"],
				[20,$patientInformation->job,true],
				[22,"\x20"],
			   // [20,"to be fill"],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray( 
			[  
				[$form->borangdafter_offset, "\x20"],
				[11,"\x20"],    
				[16,date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y H:i")], 
				[23,"\x20"],
				[30,$patientAdmission->reference,true ],
			]
		);
		$form->printNewLine(4);
		$form->printElementArray( 
			[  
				[$form->borangdafter_offset, "\x20"],
				[19,"\x20"],
				[11,date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y")],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray( 
			[  
				[$form->borangdafter_offset, "\x20"],
				[19,"\x20"],
				[10,$patientAdmission->initial_ward_code,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray( 
			[  
				[$form->borangdafter_offset, "\x20"],
				[19,"\x20"],
				[10,$patientAdmission->initial_ward_class,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray( 
			[  
				[19,"\x20"],
				// [10,"Disiplin",true],
			]
		);

		$form->escp2EjectPaper();
		$form->close();

    }

	public static function printChargeSheet($rn)
	{	
		$patientAdmission = Patient_admission::findOne(["rn"=>$rn]);
		if (is_null($patientAdmission))
			return Yii::t('app',"RN can't be found in database");
		
		if (Yii::$app->params['printerstatus'] != "true")
			return Yii::t('app','Printing was not enabled');

		$patientInformation = $patientAdmission->getPatient_information()->one();
			
		$form = new PrintForm(PrintForm::BorangCajSheet);
		
		// Begin Print
		$form->escp2ResetPaper();

		if(Yii::$app->params['printeroverwritefont'] == "true")
			$form->escp2SetTypeface(0);
		
		$form->printNewLine(4);
		$form->printElementArray(
				[
					[$form->chargesheet_offset, "\x20"],
					//caj line 1, rn, entrydate, entrytime
					[9, "\x20"],
					[11, $patientAdmission->rn],
					[24,"\x20"],
					[10, date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y")],
					[15,"\x20"],
					[5, date_format(new \datetime($patientAdmission->entry_datetime), "H:i")],
				]
			);
		$form->printNewLine(2);
		if(strlen($patientInformation->name) > 25){
			$first = substr($patientInformation->name, 0, 25);
			$second = substr($patientInformation->name, 26, 51);

			$form->printElementArray(
				[
					//line 2, name and nric
					[$form->chargesheet_offset, "\x20"],
					[6, "\x20"],
					[30, $first,true],
					[15,"\x20"],
					[14, $patientInformation->nric],
				]
			);
			$form->printNewLine(1);
			$form->printElementArray(
			[
				//line 2, name and nric
				[$form->chargesheet_offset, "\x20"],
				[6, "\x20"],
				[30, $second,true],
				[15,"\x20"],
				[25, $patientInformation->nationality,true],
			]
			);
			$form->printNewLine(2);
		}
		else
		{
			$form->printElementArray(
				[
					//line 2, name and nric
					[$form->chargesheet_offset, "\x20"],
					[6, "\x20"],
					[30, $patientInformation->name,true],
					[15,"\x20"],
					[14, $patientInformation->nric],
				]
			);
			$form->printNewLine(1);
			$form->printElementArray(
				[
					//line for nationality 
					[$form->chargesheet_offset, "\x20"],
					[51, "\x20"],
					[25, $patientInformation->nationality,true],
				]
			);
			$form->printNewLine(2);

		}
		$form->printElementArray(
			[
				[$form->chargesheet_offset, "\x20"],
				//line for gender, age
				[7, "\x20"],
				[9, $patientInformation->sex,true],
				[27, "\x20"],
				[11, $patientInformation->getAge("%y, %m, %d")],
				[10, "\x20"],
			   // [13, "\x20"], for status in the future
			]
		);
		$form->printNewLine(2);

		$form->printElementArray(
			[
				[$form->chargesheet_offset, "\x20"],
				[13, "\x20"],
				[50, $patientInformation->address1,true],
			]
		);
		$form->printNewLine(1);

		$form->printElementArray(
			[
				[$form->chargesheet_offset, "\x20"],
				[13, "\x20"],
				[50, $patientInformation->address2,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->chargesheet_offset, "\x20"],
				[13, "\x20"],
				[50, $patientInformation->address3,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				[$form->chargesheet_offset, "\x20"],
				[11, "\x20"],
				[5, $patientAdmission->initial_ward_code,true],
				[42, "\x20"],
				[3, $patientAdmission->initial_ward_class,true],
			]
		);

		$form->escp2EjectPaper();
		$form->close();	
	}
	
	public static function printCaseHistorySheet($rn)
	{
		$patientAdmission = Patient_admission::findOne(["rn"=>$rn]);
		if (is_null($patientAdmission))
			return Yii::t('app',"RN can't be found in database");
		
		if (Yii::$app->params['printerstatus'] != "true")
			return Yii::t('app','Printing was not enabled');

		$patientInformation = $patientAdmission->getPatient_information()->one();
			
		$form = new PrintForm(PrintForm::BorangCaseNote);
		
		// Begin Print
		$form->escp2ResetPaper();
		if(Yii::$app->params['printeroverwritefont'] == "true")
			$form->escp2SetTypefaceDraft();
		
		
		//$form->printNewLine(4);
		$form->printElementArray(
			[
				//case history note line 1, name and rn
				[$form->casehistory_offset, "\x20"],
				[21, "\x20"], // from 22->20
				[36, $patientInformation->name,true],
				[9,"\x20"],
				[12, $patientAdmission->rn],
			]
		);
		$form->printNewLine(1);

		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 2,address1
				[21, "\x20"], // from 22->20
				[36, $patientInformation->address1,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 3,address2 , ic
				[21, "\x20"], // from 22->20
				[36, $patientInformation->address2,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 4,address3
				[21, "\x20"], // from 22->20
				[36, $patientInformation->address3,true],
				[5,"\x20"],
				[14, $patientInformation->nric],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 5,age,gender,race,religion
				[21, "\x20"], // from 22->20
				[11, $patientInformation->getAge("%y, %m, %d")],
				[5 , "\x20"],
				[1, $patientInformation->sex,true],
				[12, "\x20"],
				[2, $patientInformation->race,true],
				[18, "\x20"],
			   // [notsure, $religion],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 6 occupation/job
				[21, "\x20"], // from 22->20
				[36, $patientInformation->job,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 7, employername
				[21, "\x20"], // from 22->20
				[13, $patientAdmission->guarantor_name,true],// phase2 change to be selective
			]
		);
		$form->printNewLine(2);
		$firstNOK = $patientInformation->getLatestNOK();
		$availableNOK = !is_null($firstNOK);

		
		if($availableNOK)
		{
			if(strlen($firstNOK->nok_name) > 24){
				$first = substr($firstNOK->nok_name, 0, 13);
				$second = substr($firstNOK->nok_name, 14, 47);
			}
			else{
				$first = $firstNOK->nok_name;
				$second = " ";
			}
		}
		
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 8, nok name
				[21, "\x20"], // from 22->20
				[14, $availableNOK?$first:" ",true],
				[15 , "\x20"],
				[4, $patientAdmission->initial_ward_code,true],
				[4 , "\x20"],
				[4, $patientAdmission->initial_ward_class,true],

			]
		);
		//$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 8, nok name
				[21, "\x20"], // from 22->20
				[24, $availableNOK?$second:" ",true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 9, nok add 1
				[16, "\x20"], // from 22->20
				[29, $availableNOK?$firstNOK->nok_address1:" ",true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 10, nok add2
				[16, "\x20"], // from 22->20
				[29, $availableNOK?$firstNOK->nok_address2:" ",true],
			]
		);
		$form->printNewLine(1);
		
		if($availableNOK && $firstNOK->nok_address3 == ""){
			$form->printElementArray(
				[
					[$form->casehistory_offset, "\x20"],
					//case history note line 11, nok add3 , entry datetime
					[16, "\x20"], // from 22->20
					[29, " "],
					[1, "\x20"],
					[10, date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y")],
					[1, "\x20"],
					[5, date_format(new \datetime($patientAdmission->entry_datetime), "H:i")],
				]
			);
		}
		else{
			$form->printElementArray(
				[
					[$form->casehistory_offset, "\x20"],
					//case history note line 11, nok add3 , entry datetime
					[16, "\x20"], // from 22->20
					[29, $availableNOK?$firstNOK->nok_address3:" ",true],
					[1, "\x20"],
					[10, date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y")],
					[1, "\x20"],
					[5, date_format(new \datetime($patientAdmission->entry_datetime), "H:i")],
				]
			);
		}
		$form->printNewLine(1);

		$form->printElementArray(
			[
				[$form->casehistory_offset, "\x20"],
				//case history note line 12, nok phone
				[16, "\x20"], 
				[13, $availableNOK?$firstNOK->nok_phone_number:" "],
			]
		);

		$form->escp2EjectPaper();
		$form->close();
	}
	
	public static function printStickerLabels($rn)
	{
		$patientAdmission = Patient_admission::findOne(["rn"=>$rn]);
		if (is_null($patientAdmission))
			return Yii::t('app',"RN can't be found in database");
		
		if (Yii::$app->params['printerstatus'] != "true")
			return Yii::t('app','Printing was not enabled');

		$patientInformation = $patientAdmission->getPatient_information()->one();
			
		$form = new PrintForm(PrintForm::BorangSticker);
		
		// Begin Print
		$form->escp2ResetPaper();
		
		$form->escp2SetTypefaceDraft();
		$form->escp2SetTiny();
		
		
		$ageString = $patientInformation->getAge("%yyrs %mmth%dday");
		$wardDesc = Lookup_ward::findOne(["ward_code"=>$patientAdmission->initial_ward_code])->ward_name; 
		
		$rows = 3;
		
		for($i=1; $i<=1; $i++)  
		{
			for($k=1; $k<= $rows; $k++)  //$k<=6 
			{
				$form->printElementArray(
					[
						[$form->sticker_offset, "\x20"],
						//sticker line 1 , name, age
						[24, $patientInformation->name,true],
						[1,"\x20"],
						[16, $ageString],
						[6,"\x20"],//47
						
						[24, $patientInformation->name,true],
						[1,"\x20"],
						[16, $ageString],
						[6,"\x20"],//47
						
						[24, $patientInformation->name,true],
						[1,"\x20"],
						[17, $ageString]
					]
				);
				$form->printNewLine(1);
				$form->printElementArray(
					[
						[$form->sticker_offset, "\x20"],
						//sticker line 2 , ic rn
						[4, "KP:"],
						[14,$patientInformation->nric],
						[2, "\x20"],
						[4,"NP:"],
						[11, $patientAdmission->rn],
						[1, "\x20"],
						[4,"JAN:"],
						[1, $patientInformation->sex, true],
						[6, "\x20"],// 47
						
						[4, "KP:"],
						[14,$patientInformation->nric],
						[2, "\x20"],
						[4,"NP:"],
						[11, $patientAdmission->rn],
						[1, "\x20"],
						[4,"JAN:"],
						[1, $patientInformation->sex, true],
						[6, "\x20"],// 47	
						
						[4, "KP:"],
						[14,$patientInformation->nric],
						[2, "\x20"],
						[4,"NP:"],
						[11, $patientAdmission->rn],
						[1, "\x20"],
						[4,"JAN:"],
						[2, $patientInformation->sex, true]
					]
				);
				$form->printNewLine(1);
				$form->printElementArray(
					[
						[$form->sticker_offset, "\x20"],
						[3, $patientAdmission->initial_ward_code,true],
						[1, "\x20"],
						[18, $wardDesc],
						[7,"Katil: "],
						[6,"\x20"],
						[4, "BAN:"],
						[2,$patientInformation->race,true],
						[6,"\x20"],//47
						
						[3, $patientAdmission->initial_ward_code,true],
						[1, "\x20"],
						[18, $wardDesc],
						[7,"Katil: "],
						[6,"\x20"],
						[4, "BAN:"],
						[2,$patientInformation->race,true],
						[6,"\x20"],//47
						
						[3, $patientAdmission->initial_ward_code,true],
						[1, "\x20"],
						[18, $wardDesc],
						[7,"Katil: "],
						[6,"\x20"],
						[4, "BAN:"],
						[3,$patientInformation->race,true]

						
					]
				);
				$form->printNewLine(1);
				$form->printElementArray(
					[
						[$form->sticker_offset, "\x20"],
						//sticker line 4 , hospital address
						[41, "Sarawak General Hospital, 93586, Kuching"],
						[6, "\x20"],
						[41, "Sarawak General Hospital, 93586, Kuching"],
						[6, "\x20"],
						[42, "Sarawak General Hospital, 93586, Kuching"]
					]
				);
				$form->printNewLine(3);
				// $form->printNewLine(2);
			}
		}
			
		$form->escp2EjectPaper();	

		$form->escp2UnsetTiny();

		$form->close();
	}
	
	public static function printReceipt($receipt, $patientInformation)
	{
		#$patientAdmission = Patient_admission::findOne(["rn"=>$rn]);
		if (is_null($receipt))
			return Yii::t('app',"Receipt can't be found in database");
		
		if (Yii::$app->params['printerstatus'] != "true")
			return Yii::t('app','Printing was not enabled');


		#$patientInformation = $patientAdmission->getPatient_information()->one();
			
		//$form = new PrintForm(PrintForm::Receipt);
		
		$form = new PrintForm(PrintForm::Bill2);
		
		// Begin Print
		$form->escp2ResetPaper();
		if(Yii::$app->params['printeroverwritefont'] == "true")
			$form->escp2SetTypefaceDraft(0);
		
		$form->printNewLine(6);
		
		$form->printElementArray(
				[
					[$form->receipt_offset, "\x20"],
					//receiptline 1, receipt serial number, ic
					[15, "\x20"],
					[10, $receipt->receipt_serial_number,true],
					[33,"\x20"],
					[14, $patientInformation->nric],
				]
			);
		$form->printNewLine(1);
		$form->printElementArray(
				[
					[$form->receipt_offset, "\x20"],
					//line2 , pay date, rn
					[15, "\x20"],
					[10, date_format(new \datetime($receipt->receipt_content_datetime_paid), "d/m/Y")],
					[33, "\x20"],
					[11, $receipt->rn],
					
				   // [13, "\x20"], for status in the future
				]
		);
		$form->printNewLine(1);

		$form->printElementArray(
			[
				[$form->receipt_offset, "\x20"],
				//line 3, pay time, bil number
				[15, "\x20"],
				[5, date_format(new \datetime($receipt->receipt_content_datetime_paid), "H:i:s")],
				[36, "\x20"],
				[8, $receipt->receipt_content_bill_id, true],

			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->receipt_offset, "\x20"],
				//line 4 akaun, total 
				[15, "\x20"],
				[10, $receipt->kod_akaun,true],
				[31, "\x20"],
				[9, number_format(($receipt->receipt_content_sum),2, '.', '')],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->receipt_offset, "\x20"],
				// line 5, OP mayb phase 2?
				[15, "\x20"],
				[1, " "],
			]
		);
		$form->printNewLine(1);
			$form->printElementArray(
				[
					[$form->receipt_offset, "\x20"],
					//line 6, cagaran and nama pembayar
					[23, "\x20"],
					[8, "",true],
					[25, "\x20"],
					[23, $receipt->receipt_content_payer_name,true],
				]
			);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[$form->receipt_offset, "\x20"],
				//line 7, patient name, payment method 25,15 42-8
				[15, "\x20"],
				[25, $patientInformation->name,true],
				[16, "\x20"],
				[17, $receipt->receipt_content_payment_method,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				[$form->receipt_offset, "\x20"],
				//line 8, penjelasan, descripton
				[7, "\x20"],
				[13, "Penjelasan : "],
				[50, $receipt->receipt_content_description,true],
				
			]
		);
				
		$form->escp2EjectPaper();
		$form->close();
	} 

	public static function printBill($bill_uid)
	{
		$bill = Bill::findOne(["bill_uid"=>$bill_uid]);
		if (is_null($bill))
			return Yii::t('app',"Bill can't be found in database");
		
		if (Yii::$app->params['printerstatus'] != "true")
		{
			return Yii::t('app','Printing was not enabled');
		}

		if (!is_null($bill->bill_print_id))
			return Yii::t('app','Bill has been printed for receipt').$bill->bill_print_id.Yii::t('app',', only 1 bill allowed');
		
		if ($bill->deleted)
			return Yii::t('app','Bill is no longer valid');
		
		$patientAdmission = Patient_admission::findOne(["rn"=>$bill->rn]);
		$patientInformation = $patientAdmission->getPatient_information()->one();
		$previousPayments = Receipt::find()
			->where(["rn"=>$bill->rn])
			->andWhere(["<", "receipt_content_datetime_paid", $bill->bill_generation_datetime])
			->all();
		
		
		$treatmentDetails = Treatment_details::find()->where(["bill_uid"=>$bill->bill_uid])->all();
		$fppDetails = Fpp::find()->where(["bill_uid"=>$bill->bill_uid])->all();

		//print header
		//print ward details (3 rows)
		//print treatment header
		//print treatment details
		//print fpp details
		//print inpatient treatment details (size 3)
		//print payment details
		//nextpage (includes printheader)
		//print footer

		//load queue buffer
		// each item in buffer = 1 line

		//-- print function --
		//print
		// frame size = x, linecount = 0
		// while buffer is not empty
			// while linecount < frame size
			// print 1 line from buffer
			// otherwise, nextpage(linecount = 0, print header)
		// print footer(linecount)
		
		$session = Yii::$app->session;
		$form_type = null;
		$printer_choice = $session->get('bill_printer_session');
		
		/*
		if ($session->has('bill_printer_session')) {
			$printer_choice = $session->get('bill_printer_session');
			if($printer_choice == 'Printer 1')
				$form_type = PrintForm::Bill;
			else if($printer_choice == 'Printer 2')
				$form_type = PrintForm::Bill2;
			
		}
		else $form_type = PrintForm::Bill;
		*/
		
					
		$form_type = PrintForm::Bill2;
		
		$form = new PrintForm($form_type);

		// adding queue
		$MyQueue = new Queue();

		$form->printCajDudukWad($bill, $MyQueue);
		foreach ($treatmentDetails as $treatmentDetail) {
			$form->printBillTreatmentRow($treatmentDetail, $MyQueue);
		}
		foreach ($fppDetails as $fppDetail) {
			$form->printBillFppRow($fppDetail, $MyQueue);
		}
		$form->printBillDailyTreatment($bill, $MyQueue, $previousPayments);
		foreach ($previousPayments as $payment) {
			$form->printBillPreviousPaymentRow($payment, $MyQueue);
		}

		$windowLimit = 23; 
		$lineCounter = 0;

		// print bill content in here
		$form->header_bill(8, $bill, $patientInformation, $form);
		while (!$MyQueue->isEmpty())
		{
			if($lineCounter < $windowLimit)
			{
			//	var_dump($MyQueue->frontElement());
				$form->printElementArray($MyQueue->frontElement());
				$MyQueue->DeQueue();
				$lineCounter++;
			}else
			{
				$form->escp2EjectPaper();
				$form->header_bill(8, $bill, $patientInformation, $form);
				$lineCounter = 0;
			}
		}
		$newlinesRequired = $windowLimit - $lineCounter;
		$form->printNewLine($newlinesRequired);
			
		$form->printElementArray(
			[
				[$form->bill_offset, "\x20"],
				[27, "\x20"],
				[29, "JUMLAH YANG PERLU DIBAYAR ==>"], //$model->bill_generation_final_fee_rm
				[10,"\x20"],
				[9,  $bill->bill_generation_final_fee_rm,false,true],
			]
		);

		$form->escp2EjectPaper();
	 	$form->close();
	
	}

	public function header_bill($line, $bill, $patientInformation, $form)
	{
		$this->escp2ResetPaper();
		if(Yii::$app->params['printeroverwritefont'] == "true")
			$this->escp2SetTypeface(0);
		$this->printNewLine($line); 
		$this->printBillAddress($bill, $patientInformation, $form);
		$this->printNewLine(1); 
	}

	public function printBillAddress($bill, $patientInformation, $form)
	{
		$this->printElementArray(
			[
				[$form->bill_offset, "\x20"],
				[61, "\x20"],
				[10, date_format(new \datetime($bill->bill_generation_datetime), "d/m/Y")],
			]
		);           
		$this->printNewLine(2);
		$this->printElementArray(
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[11, $bill->rn],
			]
		);     
		$this->printNewLine(1);
		$this->printElementArray(
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[35, $patientInformation->name,true],
			]
		);
		$this->printNewLine(1);
		$this->printElementArray(
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[35, $patientInformation->address1,true],
			]
		);
		$this->printNewLine(1);
		$this->printElementArray(
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[35, $patientInformation->address2,true],
			]
		);
		$this->printNewLine(1);
		$this->printElementArray(
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[35, $patientInformation->address3,true],
			]
		);
		$this->printNewLine(10);
	}

	public function printCajDudukWad($bill, $queue)
	{
		$form = new PrintForm(PrintForm::Bill);
		$hasWards = Ward::find()->where(["bill_uid"=>$bill->bill_uid])->exists();
		$queue->EnQueue(			
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[32, "Caj Duduk Wad  (Tarikh Masuk  : "],
				[10, $hasWards?date_format(new \datetime(Ward::find()->where(["bill_uid"=>$bill->bill_uid])->min("ward_start_datetime")), "d/m/Y"):"N/A"],
				[2," )"],
				[1,"\n"]
			]);
		$queue->EnQueue(			
			[
				[$form->bill_offset, "\x20"],
				[21, "\x20"],
				[17, "(Tarikh Keluar : "],
				[10, $hasWards?date_format(new \datetime(Ward::find()->where(["bill_uid"=>$bill->bill_uid])->max("ward_end_datetime")), "d/m/Y"):"N/A"],
				[2," )"],
				[1,"\n"]
			]);
		$queue->EnQueue([[1,"\n"]]);
		$queue->EnQueue([[1,"\n"]]);
		$queue->EnQueue(			
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[7, "Kelas  "],
				[2, $bill->class],
				[3,":  "],
				[3, $wardNumOfDays = ($hasWards?Ward::find()->where(["bill_uid"=>$bill->bill_uid])->sum("ward_number_of_days"):"0")],
				// [1," "],
				[4, "hari"],
				[30, "\x20"],
				[9, $bill->daily_ward_cost,false,true],
				[2, "\x20"],
				[9, number_format(($wardNumOfDays*$bill->daily_ward_cost),2, '.', ''),false,true],
				[1,"\n"]
			]);
		$queue->EnQueue([[1,"\n"]]);
		$queue->EnQueue(			
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[28, "Caj Pemeriksaan/Ujian Makmal"],
				[1,"\n"]
			]);
		$queue->EnQueue(			
			[
				[$form->bill_offset, "\x20"],
				[6, "\x20"],
				[28, "-----------------------------"],
				[1,"\n"]
			]);
	}

    public function printBillTreatmentRow($treatmentDetails, $queue){
		$form = new PrintForm(PrintForm::Bill);
		$queue->EnQueue(
            [
				[$form->bill_offset, "\x20"],
                [6, "\x20"],
                [5, $treatmentDetails->treatment_code, true],
                [1,"\x20"],
                [30, $treatmentDetails->treatment_name,true],
                [2,"\x20"],
                [1,"X"],
                [2,"\x20"],
                [5, $treatmentDetails->item_count],
                [4,"\x20"],
                [8, $treatmentDetails->item_per_unit_cost_rm,false,true],
                [2,"\x20"],
                [9, $treatmentDetails->item_total_unit_cost_rm,false,true],
				[1,"\n"]
            ]
        );
    }

	public function printBillFppRow($fppDetails, $queue){
		$form = new PrintForm(PrintForm::Bill);
		$queue->EnQueue(
            [
				[$form->bill_offset, "\x20"],
                [6, "\x20"],
                [5, $fppDetails->kod, true],
                [1,"\x20"],
                [30, $fppDetails->name,true],
                [2,"\x20"],
                [1,"X"],
                [2,"\x20"],
                [5, $fppDetails->number_of_units],
                [4,"\x20"],
                [8, $fppDetails->cost_per_unit,false,true],
                [2,"\x20"],
                [9, $fppDetails->total_cost,false,true],
				[1,"\n"]
            ]
        );
    }
	
	public function printBillDailyTreatment($bill, $queue, $previousPayments){
		$form = new PrintForm(PrintForm::Bill);
		$modelInpatient = Inpatient_treatment::findOne(['bill_uid' => $bill->bill_uid]);

		$queue->EnQueue([[1,"\n"]]);
    	$queue->EnQueue(
            [
				[$form->bill_offset, "\x20"],
                [6, "\x20"],
                [24, "Caj Rawatan Harian"],
				[1,"\n"]
            ]
        );
		$queue->EnQueue(
            [
				[$form->bill_offset, "\x20"],
                [6, "\x20"],
                [18, "---------------------"],
                [45, "\x20"],
                [6, (!empty($modelInpatient) ? $modelInpatient->inpatient_treatment_cost_rm : 
					number_format(0,2, '.', '')), false, true], 
				[1,"\n"]
            ]
        );
		
       if(!empty($previousPayments))
	   {
		   $queue->EnQueue(
            [
				[$form->bill_offset, "\x20"],
                [68, "\x20"],
                [10, "--------"],
				[1,"\n"]
            ]
			);
		  
			$queue->EnQueue(
				[
					[$form->bill_offset, "\x20"],
					[66, "\x20"],
					[9, $bill->bill_generation_billable_sum_rm,false,true],
					[1,"\n"]
				]
			);
	   }
	   
		
    }
	
	public function printBillPreviousPaymentRow($receipt, $queue){
		$form = new PrintForm(PrintForm::Bill);
		$queue->EnQueue(
            [
				[$form->bill_offset, "\x20"],
                [6, "\x20"],
                [23, (($receipt->receipt_type=="refund")?"Tambah Cagaran":"Tolak Cagaran")." ". $receipt->receipt_serial_number],
                [37,"\x20"],
                [9, $receipt->receipt_content_sum,false,true],
				[1,"\n"]
            ]
        );
    }

	// comment functions below, old codes, no used

	// public function printBillTreatmentRowOverflow($sum)
	// {
	// 	$this->printElementArray(
    //         [
    //             [5, "\x20"],
    //             [5, "", true],
    //             [1,"\x20"],
    //             [30, "...",true],
    //             [2,"\x20"],
    //             [1,""],
    //             [2,"\x20"],
    //             [5, ""],
    //             [6,"\x20"],
    //             [8, "",false,true],
    //             [2,"\x20"],
    //             [9, $sum,false,true],

    //         ]
    //     );
    //     $this->printNewLine(1);
	// }

	
    // public function printBillTreatment($bill_uid, $treatment_code, $treatment_name, $item_count, $item_per_unit_cost, $item_total_unit_cost){
    //     $this->printElementArray(
    //         [
    //             [6, "\x20"],
    //             [5, $treatment_code, true],
    //             [1,"\x20"],
    //             [30, $treatment_name,true],
    //             [2,"\x20"],
    //             [1,"X"],
    //             [2,"\x20"],
    //             [5,$item_count],
    //             [5,"\x20"],
    //             [8, $item_per_unit_cost,false,true],
    //             [2,"\x20"],
    //             [9, $item_total_unit_cost,false,true],

    //         ]
    //     );
    //     $this->printNewLine(1);
    // }


    // public function printBillDeposit($rn, $receipt_serial_number, $receipt_content_sum){
    //     $this->printElementArray(
    //         [
    //             [8, "\x20"],
    //             [14, "Tolak Cagaran "],
    //             [8,$receipt_serial_number],
    //             [37,"\x20"],
    //             [9, $receipt_content_sum,false,true],
            
    //         ]
    //     );
    //     $this->printNewLine(1);  
    // }



    // public function printCajRawatenHarian($bill){
    //     $this->printNewLine(1);
    //     $this->printElementArray(
    //         [
    //             [7, "\x20"],
    //             [24, "Rawatan Pesakit Dalam"],
    //         ]
    //     );
    //     $this->printNewLine(1);
	// 	$modelInpatient = Inpatient_treatment::findOne(['bill_uid' => $bill->bill_uid]);
    //     $this->printElementArray(
    //         [
    //             [7, "\x20"],
    //             [21, "------------------------"],
    //             [42, "\x20"],
    //             [6, $modelInpatient->inpatient_treatment_cost_rm], // need ask what price is this for
    //         ]
    //     );
    //     $this->printNewLine(1);
    // }

    // public function printBillRefund($rn, $receipt_serial_number, $receipt_content_sum){
    //     $this->printElementArray(
    //         [
    //             [8, "\x20"],
    //             [14, "Refund "],
    //             [8,$receipt_serial_number],
    //             [36,"\x20"],
    //             [10, "-".$receipt_content_sum,false,true],
            
    //         ]
    //     );
    //     $this->printNewLine(1);  
    // }
} 