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
    const BorangDaftarMasuk = 0;
   //const printerStringForBorangDaftarMasuk = "smb://DESKTOP-7044BNO/Epson"; //"smb://DESKTOP-7044BNO/Epson" Yii::$app->params['borangdafter']

    const BorangCajSheet= 0;
   // const printerStringForBorangCajSheet = "smb://DESKTOP-7044BNO/Epson"; //smb://JOSH2-LAPTOP/Epson

    const BorangCaseNote= 0;
    //const printerStringForBorangCaseNote = "smb://DESKTOP-7044BNO/Epson";

    const BorangSticker= 0;
    //const printerSticker = "smb://DESKTOP-7044BNO/Epson";
    const Receipt= 0;
    //const printerStringForReceipt= "smb://DESKTOP-7044BNO/Epson";

    const Bill= 0;
   // const printerStringForBill= "smb://DESKTOP-7044BNO/Epson";

    public $formtype = null;

    private $connector = null;
    private $printer = null;

    public function __construct($formtype)
    {
        $printerStringForBorangDaftarMasuk = Yii::$app->params['borangdafter'];
        $printerStringForBorangCajSheet = Yii::$app->params['chargesheet'];
        $printerStringForBorangCaseNote = Yii::$app->params['casehistory'];
        $printerSticker = Yii::$app->params['sticker'];
        $printerStringForReceipt = Yii::$app->params['receipt'];
        $printerStringForBill = Yii::$app->params['bill'];

        $this->connector = null;
        $this->formtype = $formtype;

        if($formtype == PrintForm::BorangDaftarMasuk){
            $this->connector = new WindowsPrintConnector($printerStringForBorangDaftarMasuk);
            
        }
        $this->printer = new Printer($this->connector);


        if($formtype == PrintForm::BorangCajSheet){
            $this->connector = new WindowsPrintConnector($printerStringForBorangCajSheet);
        }
        $this->printer = new Printer($this->connector);


        if($formtype == PrintForm::BorangCaseNote){
            $this->connector = new WindowsPrintConnector($printerStringForBorangCaseNote);
           
        }
        $this->printer = new Printer($this->connector);

        if($formtype == PrintForm::BorangSticker){
            $this->connector = new WindowsPrintConnector($printerSticker);
          
        }
        $this->printer = new Printer($this->connector);
        if($formtype == PrintForm::Receipt){
            $this->connector = new WindowsPrintConnector($printerStringForReceipt);
            // $this->printer = new Printer($this->connector);
        }
        $this->printer = new Printer($this->connector);
        if($formtype == PrintForm::Bill){
            $this->connector = new WindowsPrintConnector($printerStringForBill);
            // $this->printer = new Printer($this->connector);
        }
        $this->printer = new Printer($this->connector);


    }

    public function printElement($len, $value, $uppercase=false, $rightalign=false)
    {
		if($value != "")
		{
			if($uppercase)
				$value = strtoupper($value);

			if($value == "\n"){
				$this->printNewLine($len);
			}
			
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
		
		
		// Begin Print
		$form->escp2ResetPaper();
		$form->escp2SetTypeface(0);
		$form->printNewLine(5);

		// (name and rn)
		if(strlen($patientInformation->name) > 32){
			$first = substr($patientInformation->name, 0, 32);
			$second = substr($patientInformation->name, 33, 65);

			$form->printElementArray(
				[
					[11, "\x20"],
					[32, $first, true],
					[20,"\x20"],
					[11, $rn]
				]
			);
			$form->printNewLine(1);

			$form->printElementArray(
				[
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
					[9, "\x20"],
					[32, $patientInformation->name, true],
					[20,"\x20"],
					[11, $rn]
				]
			);
			$form->printNewLine(2);
		}
		

		// (address and ic)
		$form->printElementArray(
			[
				[56,"\x20"],
				[14, $patientInformation->nric],
			]
		);
		$form->printNewLine(1);
		
		$form->printElementArray(
			[
				[5, "\x20"],
				[38, $patientInformation->address1,true],
			]
		);
		$form->printNewLine(1);

		// (address 2)
		$form->printElementArray(
			[
				[5,"\x20"],
				[38, $patientInformation->address2,true],
				[13,"\x20"], 
				[15, $patientInformation->phone_number],  
			]
		);
		$form->printNewLine(1);

		// (address 3 and phone number)
		$form->printElementArray( 
			[  
				[5,"\x20"],  
				[38, $patientInformation->address3,true],
			]
		);
		
		$form->printNewLine(4);

		$race = $patientInformation->race;
		$raceResult = Lookup_general::findOne(["code"=>$race, "category"=>"Race"]);
		if(!is_null($raceResult))
			$race = $raceResult->name;
		$form->printElementArray( 
			[  
				[10,"\x20"],
				[9,$patientInformation->sex,true],
				[6,"\x20"],
				[10,$patientInformation->getDOB()],
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
					[53, "\x20"],
					[30,$firstNOK->nok_name,true],
				]
			);
			$form->printNewLine(2);
			$form->printElementArray( 
				[  
					[43, "\x20"],  //52-13
					[35,$firstNOK->nok_address1,true],
				]
			);
			$form->printNewLine(1);
			$form->printElementArray( 
				[  
					[43, "\x20"],
					[35,$firstNOK->nok_address2,true],
				]
			);
			$form->printNewLine(1);
			$form->printElementArray( 
				[  
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
				[11,"\x20"],
				[20,$patientInformation->job,true],
				[22,"\x20"],
			   // [20,"to be fill"],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray( 
			[  
				[11,"\x20"],    
				[16,date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y H:i")], 
				[23,"\x20"],
				[30,$patientAdmission->reference,true ],
			]
		);
		$form->printNewLine(4);
		$form->printElementArray( 
			[  
				[19,"\x20"],
				[11,date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y")],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray( 
			[  
				[19,"\x20"],
				[10,$patientAdmission->initial_ward_code,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray( 
			[  
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
		$form->escp2SetTypeface(0);
		
		
		$form->printNewLine(7);
		$form->printElementArray(
				[
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
					[51, "\x20"],
					[25, $patientInformation->nationality,true],
				]
			);
			$form->printNewLine(2);

		}
		$form->printElementArray(
			[
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
				[13, "\x20"],
				[50, $patientInformation->address1,true],
			]
		);
		$form->printNewLine(1);

		$form->printElementArray(
			[
				[13, "\x20"],
				[50, $patientInformation->address2,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[13, "\x20"],
				[50, $patientInformation->address3,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
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
			
		$form = new PrintForm(PrintForm::BorangCajSheet);
		
    
		
		// Begin Print
		$form->escp2ResetPaper();
		$form->escp2SetTypefaceDraft();
		
		
		$form->printNewLine(4);
		$form->printElementArray(
			[
				//case history note line 1, name and rn
				[21, "\x20"], // from 22->20
				[36, $patientInformation->name,true],
				[10,"\x20"],
				[11, $patientAdmission->rn],
			]
		);
		$form->printNewLine(1);

		$form->printElementArray(
			[
				//case history note line 2,address1
				[21, "\x20"], // from 22->20
				[36, $patientInformation->address1,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				//case history note line 3,address2 , ic
				[21, "\x20"], // from 22->20
				[36, $patientInformation->address2,true],
				[9,"\x20"],
				[14, $patientInformation->nric],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				//case history note line 4,address3
				[21, "\x20"], // from 22->20
				[36, $patientInformation->address3,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				//case history note line 5,age,gender,race,religion
				[19, "\x20"], // from 22->20
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
				//case history note line 6 occupation/job
				[21, "\x20"], // from 22->20
				[36, $patientInformation->job,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
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
				$first = substr($firstNOK->nok_name, 0, 23);
				$second = substr($firstNOK->nok_name, 24, 47);
			}
			else{
				$first = $firstNOK->nok_name;
				$second = " ";
			}
		}
			
		$form->printElementArray(
			[
				//case history note line 8, nok name
				[21, "\x20"], // from 22->20
				[24, $availableNOK?$first:" ",true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				//case history note line 8, nok name
				[21, "\x20"], // from 22->20
				[24, $availableNOK?$second:" ",true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				//case history note line 9, nok add 1
				[16, "\x20"], // from 22->20
				[29, $availableNOK?$firstNOK->nok_address1:" ",true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				//case history note line 10, nok add2
				[16, "\x20"], // from 22->20
				[29, $availableNOK?$firstNOK->nok_address2:" ",true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				//case history note line 11, nok add3 , entry datetime
				[16, "\x20"], // from 22->20
				[29, $availableNOK?$firstNOK->nok_address3:" ",true],
				[1, "\x20"],
				[10, date_format(new \datetime($patientAdmission->entry_datetime), "d/m/Y")],
				[1, "\x20"],
				[5, date_format(new \datetime($patientAdmission->entry_datetime), "H:i")],
			]
		);
		$form->printNewLine(1);



		$form->printElementArray(
			[
				//case history note line 12, nok phone
				[15, "\x20"], 
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
	

		$n=6;
		
		$ageString = $patientInformation->getAge("%yyrs%mmth%dday");
		$wardDesc = Lookup_ward::findOne(["ward_code"=>$patientAdmission->initial_ward_code])->ward_name; 
		
		for($i=1; $i<=1; $i++)  
		{
			
			for($k=1; $k<=6; $k++)  //$k<=6 
			{
				$form->printElementArray(
					[
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
						[16, $ageString]
					]
				);
				$form->printNewLine(1);
				$form->printElementArray(
					[
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
						[1, $patientInformation->sex, true]
					]
				);
				$form->printNewLine(1);
				$form->printElementArray(
					[

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
						[2,$patientInformation->race,true]

						
					]
				);
				$form->printNewLine(1);
				$form->printElementArray(
					[
						//sticker line 4 , hospital address
						[41, "Sarawak General Hospital, 93586, Kuching"],
						[6, "\x20"],
						[41, "Sarawak General Hospital, 93586, Kuching"],
						[6, "\x20"],
						[41, "Sarawak General Hospital, 93586, Kuching"]
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
			
		$form = new PrintForm(PrintForm::Receipt);
		
		
		// Begin Print
		$form->escp2ResetPaper();
		$form->escp2SetTypefaceDraft(0);
		
		$form->printNewLine(6);
		
		$form->printElementArray(
				[
					//receiptline 1, receipt serial number, ic
					[16, "\x20"],
					[8, $receipt->receipt_serial_number,true],
					[33,"\x20"],
					[14, $patientInformation->nric],
				]
			);
		$form->printNewLine(1);
		$form->printElementArray(
				[
					//line2 , pay date, rn
					[16, "\x20"],
					[10, date_format(new \datetime($receipt->receipt_content_datetime_paid), "d/m/Y")],
					[33, "\x20"],
					[11, $receipt->rn],
					
				   // [13, "\x20"], for status in the future
				]
		);
		$form->printNewLine(1);

		$form->printElementArray(
			[
				//line 3, pay time, bil number
				[16, "\x20"],
				[5, date_format(new \datetime($receipt->receipt_content_datetime_paid), "H:i:s")],
				[36, "\x20"],
				[8, $receipt->receipt_content_bill_id, true],

			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				//line 4 akaun, total 
				[16, "\x20"],
				[1, " "], // akaun, maybe phase 2?
				[40, "\x20"],
				[9, number_format(($receipt->receipt_content_sum),2, '.', '')],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				// line 5, OP mayb phase 2?
				[16, "\x20"],
				[1, " "],
			]
		);
		$form->printNewLine(1);
			$form->printElementArray(
				[
					//line 6, cagaran and nama pembayar
					[16, "\x20"],
					[8, "",true],
					[33, "\x20"],
					[20, $receipt->receipt_content_payer_name,true],
				]
			);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				//line 7, patient name, payment method 25,15 42-8
				[16, "\x20"],
				[25, $patientInformation->name,true],
				[16, "\x20"],
				[17, $receipt->receipt_content_payment_method,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				//line 8, penjelasan, descripton
				[8, "\x20"],
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
		//	throw new Exception("Printing was not enabled");

		if (!is_null($bill->bill_print_id))
			return Yii::t('app','Bill has been printed for receipt').$bill->bill_print_id.Yii::t('app',', only 1 bill allowed');
		
		if ($bill->deleted)
			return Yii::t('app','Bill is no longer valid');
		
		$patientAdmission = Patient_admission::findOne(["rn"=>$bill->rn]);
		$patientInformation = $patientAdmission->getPatient_information()->one();
			
		$form = new PrintForm(PrintForm::Bill);
		
		// Begin Print
		$form->escp2ResetPaper();
		$form->escp2SetTypeface(0);
		$form->printNewLine(9); // mayb 8
		
		$form->printElementArray(
			[
				[62, "\x20"],
				[10, date_format(new \datetime($bill->bill_generation_datetime), "d/m/Y")],
			]
		);           
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[7, "\x20"],
				[11, $bill->rn],
			]
		);     
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[7, "\x20"],
				[35, $patientInformation->name,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[7, "\x20"],
				[35, $patientInformation->address1,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[7, "\x20"],
				[35, $patientInformation->address2,true],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[7, "\x20"],
				[35, $patientInformation->address3,true],
			]
		);
		$form->printNewLine(10);
		
		$hasWards = Ward::find()->where(["bill_uid"=>$bill_uid])->exists();
		
	
		$form->printElementArray(
			[
				[7, "\x20"],
				[32, "Caj Duduk Wad  (Tarikh Masuk  : "],
				[10, $hasWards?date_format(new \datetime(Ward::find()->where(["bill_uid"=>$bill_uid])->min("ward_start_datetime")), "d/m/Y"):"N/A"],
				[2," )"],

			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[22, "\x20"],
				[17, "(Tarikh Keluar : "],
				[10, $hasWards?date_format(new \datetime(Ward::find()->where(["bill_uid"=>$bill_uid])->max("ward_end_datetime")), "d/m/Y"):"N/A"],
				[2," )"],

			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				[7, "\x20"],
				[7, "Kelas  "],
				[2, $bill->class],
				[4," :  "],
				[5, $wardNumOfDays = ($hasWards?Ward::find()->where(["bill_uid"=>$bill_uid])->sum("ward_number_of_days"):"0")],
				[1," "],
				[4, "hari"],
				[26, "\x20"],
				[9, $bill->daily_ward_cost,false,true],
				[2, "\x20"],
				[9, number_format(($wardNumOfDays*$bill->daily_ward_cost),2, '.', ''),false,true],
			]
		);
		$form->printNewLine(2);
		$form->printElementArray(
			[
				[7, "\x20"],
				[28, "Caj Pemeriksaan/Ujian Makmal"],
			]
		);
		$form->printNewLine(1);
		$form->printElementArray(
			[
				[7, "\x20"],
				[28, "-----------------------------"],
			]
		);
		
		$form->printNewLine(1);
		
		//Printing treatment details section
		#if exists no previous payment, can fit 11 (after check, it's probably 14)
		#if exists previous payment, can fit 8 + 3 (1 payment minimum takes 3 lines)
		
		#$existsPreviousPayment = ($bill->bill_generation_billable_sum_rm == $bill->bill_generation_final_fee_rm);
		$previousPayments = Receipt::find()
			->where(["rn"=>$bill->rn])
			->andWhere(["<", "receipt_content_datetime_paid", $bill->bill_generation_datetime])
			->all();
		$countPreviousPayments = count($previousPayments);
		$treatmentDetails = Treatment_details::find()->where(["bill_uid"=>$bill->bill_uid])->all();
		$countTreatmentDetails = count($treatmentDetails);
		$treatmentDetailsOverflow = false;
		$paymentOverflow = false;
		
		$maxRowThatCanFit = 14;
		$treatmentDetailRowsRequired = $maxRowThatCanFit;
		$paymentRowsRequired = 0;
		
		if($countPreviousPayments > 0) 
			$treatmentDetailRowsRequired = $maxRowThatCanFit - 3;
			
		if($countTreatmentDetails <= $treatmentDetailRowsRequired)
			$treatmentDetailRowsRequired = $countTreatmentDetails;
		else
			$treatmentDetailsOverflow = true;			
		
		if($maxRowThatCanFit <= ($treatmentDetailRowsRequired + 2 + $countPreviousPayments)) //2 because previousPayments take up x+2
		{
			$paymentOverflow = true;
			$paymentRowsRequired = $maxRowThatCanFit - 2 - $treatmentDetailRowsRequired;
		}	
		else
		{
			$paymentRowsRequired = $countPreviousPayments;			
		}
		
		//Begin Printing Treatment Details
		$rowCounter = 0;
		if($treatmentDetailsOverflow)
			$treatmentDetailRowsRequired = $treatmentDetailRowsRequired - 1; //reduce number of automated loop
		$overflowSum = 0;
		
		foreach($treatmentDetails as $treatmentDetail)
		{
			$rowCounter++;
			if($rowCounter <= $treatmentDetailRowsRequired)
				$form->printBillTreatmentRow($treatmentDetail);
			else
				$overflowSum = $overflowSum + $treatmentDetail->item_total_unit_cost_rm;
		}
		
		if($treatmentDetailsOverflow)
			$form->printBillTreatmentRowOverflow($overflowSum);//print overflow line
		
		
		if($countPreviousPayments == 0)
			$form->printCajRawatenHarian();
		else
			$form->printBillDailyTreatment($bill->bill_generation_billable_sum_rm);
		
		
		//Begin Printing Past Payments
		$rowCounter = 0;
		if($paymentOverflow)
			$paymentRowsRequired = $paymentRowsRequired - 1; //reduce number of automated loop
		$overflowSum = 0;
		
		foreach($previousPayments as $previousPayment)//previous payment is actually 'Receipt' model
		{
			$rowCounter++;
  
			if($rowCounter <= $paymentRowsRequired)
				$form->printBillPreviousPaymentRow($previousPayment);//print payment row
			else if($previousPayment->receipt_type == "refund")
				$overflowSum = $overflowSum - $previousPayment->receipt_content_sum;
			else
				$overflowSum = $overflowSum + $previousPayment->receipt_content_sum;
		}
		
		if($paymentOverflow)
			$form->printBillPreviousPaymentRowOverflow($overflowSum);//print overflow line		
		
		//fill in spaces
		//calculation
		$newlinesRequired = $maxRowThatCanFit - $treatmentDetailRowsRequired - $paymentRowsRequired;
		$newlinesRequired = $newlinesRequired - ($treatmentDetailsOverflow?1:0) - ($paymentOverflow?1:0);
		$newlinesRequired = $newlinesRequired - (($countPreviousPayments==0)?0:2);
        $form->printNewLine($newlinesRequired);
		
		
		$form->printElementArray(
			[
				[28, "\x20"],
				[29, "JUMLAH YANG PERLU DIBAYAR ==>"], //$model->bill_generation_final_fee_rm
				[10,"\x20"],
				[9,$bill->bill_generation_final_fee_rm,false,true],
			]
		);
		
		$form->escp2EjectPaper();
		$form->close();
		/*
        else{
            if(!(new Bill()) -> isPrinted(Yii::$app->request->get('rn')))
            {
                $model->bill_print_id = SerialNumber::getSerialNumber("bill");
            }
        }

        return $this->render('print', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
        ]);*/
    }
	
    public function printBillTreatmentRow($treatmentDetails){
        $this->printElementArray(
            [
                [7, "\x20"],
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

            ]
        );
        $this->printNewLine(1);
    }
	
	public function printBillTreatmentRowOverflow($sum)
	{
		$this->printElementArray(
            [
                [5, "\x20"],
                [5, "", true],
                [1,"\x20"],
                [30, "...",true],
                [2,"\x20"],
                [1,""],
                [2,"\x20"],
                [5, ""],
                [6,"\x20"],
                [8, "",false,true],
                [2,"\x20"],
                [9, $sum,false,true],

            ]
        );
        $this->printNewLine(1);
	}
	
	public function printBillPreviousPaymentRow($receipt){
		
        $this->printElementArray(
            [
                [7, "\x20"],
                [23, (($receipt->receipt_type=="refund")?"Tambah Cagaran":"Tolak Cagaran")." ". $receipt->receipt_serial_number],
                [37,"\x20"],
                [9, $receipt->receipt_content_sum,false,true],
            ]
        );
        $this->printNewLine(1);  
    }

	public function printBillPreviousPaymentRowOverflow($sum){
		
        $this->printElementArray(
            [
                [7, "\x20"],
                [23, "Tambah Cagaran ..."],
                [37,"\x20"],
                [9, $sum,false,true],
            ]
        );
        $this->printNewLine(1);  
    }
	
    public function printBillTreatment($bill_uid, $treatment_code, $treatment_name, $item_count, $item_per_unit_cost, $item_total_unit_cost){
        $this->printElementArray(
            [
                [6, "\x20"],
                [5, $treatment_code, true],
                [1,"\x20"],
                [30, $treatment_name,true],
                [2,"\x20"],
                [1,"X"],
                [2,"\x20"],
                [5,$item_count],
                [5,"\x20"],
                [8, $item_per_unit_cost,false,true],
                [2,"\x20"],
                [9, $item_total_unit_cost,false,true],

            ]
        );
        $this->printNewLine(1);
    }


    public function printBillDeposit($rn, $receipt_serial_number, $receipt_content_sum){
        $this->printElementArray(
            [
                [8, "\x20"],
                [14, "Tolak Cagaran "],
                [8,$receipt_serial_number],
                [37,"\x20"],
                [9, $receipt_content_sum,false,true],
            
            ]
        );
        $this->printNewLine(1);  
    }

    public function printBillDailyTreatment($billAble){
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [7, "\x20"],
                [18, "Caj Rawatan Harian"],
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [7, "\x20"],
                [18, "------------------"],
                [47, "\x20"],
                [4, "0.00"], // need ask what price is this for
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [69, "\x20"],
                [10, "----------"],
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [67, "\x20"],
                [9, $billAble,false,true],
            ]
        );
        $this->printNewLine(1);
    }

    public function printCajRawatenHarian(){
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [7, "\x20"],
                [18, "Caj Rawatan Harian"],
            ]
        );
        $this->printNewLine(1);
        $this->printElementArray(
            [
                [7, "\x20"],
                [18, "------------------"],
                [47, "\x20"],
                [4, "0.00"], // need ask what price is this for
            ]
        );
        $this->printNewLine(1);
    }

    public function printBillRefund($rn, $receipt_serial_number, $receipt_content_sum){
        $this->printElementArray(
            [
                [8, "\x20"],
                [14, "Refund "],
                [8,$receipt_serial_number],
                [36,"\x20"],
                [10, "-".$receipt_content_sum,false,true],
            
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
} 