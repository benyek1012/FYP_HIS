<?php

namespace app\controllers;
require 'vendor/autoload.php';

use Yii;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use app\models\PrintForm;
/**
 * Patient_admissionController implements the CRUD actions for Patient_admission model.
 */
class Patient_admissionController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Patient_admission models.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays a single Patient_admission model.
     * @param string $rn Rn
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($rn)
    {
        return $this->render('view', [
            'model' => $this->findModel($rn),
        ]);
    }

    /**
     * Creates a new Patient_admission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public static function actionCreate()
    {
        if(Yii::$app->request->get('confirm') == 't')
        {
            $rows = (new \yii\db\Query())
            ->select(['rn'])
            ->from('patient_admission')
            ->where(['type' => Yii::$app->request->get('type')])
            ->all();
            $SID = "1" + count($rows);
    
            if(Yii::$app->request->get('type') == 'Normal')
                $rn = date('Y')."/".sprintf('%06d', $SID);
            else $rn = date('Y')."/9".sprintf('%05d', $SID);
            
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('+0800')); //GMT

            $model = new Patient_admission();

            $model->rn = $rn;
            $model->patient_uid = Yii::$app->request->get('id');
            $model->entry_datetime = $date->format('Y-m-d H:i');
            $model->type = Yii::$app->request->get('type');
            $model->save();

            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));          
        }
        else 
            echo '<script type="text/javascript">',
                    'setTimeout(function(){',
                        'confirmAction();',
                        '},200);',
                '</script>';
        
    }


    /**
     * Updates an existing Patient_admission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $rn Rn
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($rn)
    {
        $model = $this->findModel($rn);
        $modelpatient = Patient_information::findOne(['patient_uid' => $model->patient_uid]);
        $modelnok = Patient_next_of_kin::findOne(['patient_uid' => $modelpatient->patient_uid]);

        $getrn = arrayHelper::toArray($model->rn);
        $getname = ArrayHelper::toArray($modelpatient->name);
        $getaddress1 = ArrayHelper::toArray($modelpatient->address1);
        $getaddress2 = ArrayHelper::toArray($modelpatient->address2);
        $getaddress3 = ArrayHelper::toArray($modelpatient->address3);
        $getic = ArrayHelper::toArray($modelpatient->nric);
        $getphone = ArrayHelper::toArray($modelpatient->phone_number);
        $getgender = ArrayHelper::toArray($modelpatient->sex);
        $getjob = arrayHelper::toArray($modelpatient->job);
        //$getdateofbirth = ArrayHelper::toArray($modelpatient->dob);
        //$getget = ArrayHelper::toArray($modelpatient->age); or mayb function from dob
        $getrace = ArrayHelper::toArray($modelpatient->race);
        $getnationality = ArrayHelper::toArray($modelpatient->nationality);
        //getreligion = ArrayHelper::toArray($modelpatient->religion);
        $getentrydate = ArrayHelper::toArray($model->entry_datetime);
        $getwardcode = ArrayHelper::toArray($model->initial_ward_code);
       $getwardclass = ArrayHelper::toArray($model->initial_ward_class);
        $getemployer = ArrayHelper::toArray($model->guarantor_name);
        try {
            $noknull = 0;
                $getnok = ArrayHelper::toArray($modelnok->nok_name);
                $getnokadd1 = ArrayHelper::toArray($modelnok->nok_address1);
                $getnokadd2 = ArrayHelper::toArray($modelnok->nok_address2);
                $getnokadd3 = ArrayHelper::toArray($modelnok->nok_address3);
                $printnokname = implode($getnok);
                $printnokadd1 = implode($getnokadd1);
                $printnokadd2 = implode($getnokadd2);
                $printnokadd3 = implode($getnokadd3);
        }
        catch (Exception $e ){
            $noknull = 1;
    
    
        }
        if ($noknull == 0)
            {
               //print nok put here
                
            }

        $printrn = implode($getrn);
        $printentry = implode($getentrydate);

        // $nric = $modelpatient->nric;
        // $dob = mb_strimwidth($nric,0,6);
        // $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
        // $patientdob = date("d/m/Y" , strtotime($dateofbirth));
        // $today = date("y-m-d");
        // $diff = date_diff(date_create($dateofbirth),date_create($today));
        // $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');
         
       
        if($modelpatient->nric == ""){
            $nric = $modelpatient->nric;
            $dob = mb_strimwidth($nric,0,6);
            $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
            $patientdob = date("d/m/Y" , strtotime($dateofbirth));
            $today = date("y-m-d");
            $diff = date_diff(date_create($dateofbirth),date_create($today));
            $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');
            $agesticker = $diff->format('%Y')."yrs".$diff->format('%m')."mth".$diff->format('%d')."day";// 15 character +
        }
        else{
            $age = "";
            $patientdob = "";
        }


        $blankfront = str_repeat("\x20", 11); // adds 11 spaces
        $blankfront1 = str_repeat("\x20", 10);
        $fixbackblank = str_repeat("\x20", 43);
        $fixbackblank1 = str_repeat("\x20", 30);
        $fixbackblank2 = str_repeat("\x20", 60);
        $blank1over2 = str_repeat("\x20", 6); 
        $blank2over2 = str_repeat("\x20", 8);
        $blank2over3 = str_repeat("\x20", 4);
        $fixbackblank3 = str_repeat("\x20", 35);
        $blanktop = str_repeat("\n", 5);
        $sixbreakline = str_repeat("\n", 6);
        $cajblanktop = str_repeat("\n", 7);
        $cajblankfront2 = str_repeat("\x20", 23);
        $cajblankfront3 = str_repeat("\x20", 28);
        $sixbreakline = str_repeat("\n", 6);
        $stickerblankaftername = str_repeat("\x20", 17);
    $stickerblankaftername = str_repeat("\x20", 19);
    $stickerblankafterage = str_repeat("\x20", 11);
    $stickerblankafterage1 = str_repeat("\x20", 12);
    $stickerblankafterage2 = str_repeat("\x20", 12);
    $stickerblankafterRN = str_repeat("\x20", 12);
    $stickerblankafterRN1 = str_repeat("\x20", 15 );
    $stickerblankafterRN2 = str_repeat("\x20", 13 );
    $stickerblankaftergender = str_repeat("\x20", 13 );
    $stickerblankaftergender1 = str_repeat("\x20", 14 );
    $caseblanktop = str_repeat("\n", 4);
$casefront = str_repeat("\x20", 46); //the 13.9cm extra. it wasnt counted in, therefore requires to set in again in order to test
$caseblankfront = str_repeat("\x20", 22);
$caseblankfront1 = str_repeat("\x20", 40);
$caseblankfront2 = str_repeat("\x20", 16);
$blanktwkd = str_repeat("\x20", 18); 
        
        If (\Yii::$app->request->isPost) {
            //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
               // $printer = new Printer($connector);
            switch(\yii::$app->request->post('actionPrint'))
            {
                case 'submit1':
                
                   // $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                   $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints borang daftar". "\n\n\n");
                       
                     $printer -> text("\n\x20\n\x20\n\x20\n\x20\n"); // \n = 0.4cm
                     $printer -> text($blankfront); // space= 0.3cm， receipt column 1
                     $printer -> text(strtoupper($modelpatient->name));
                     //$blankback = ); 
                    // $printer -> text("aaabbbccc"); // patientname
 $printer -> text(str_repeat("\x20", 52- strlen($modelpatient->name))); // space for r/n value
 $printer -> text($model->rn."\n\n\n"); // R/n
 $printer -> text($blankfront);
 $printer -> text(strtoupper($modelpatient->address1)); // alamat line 1
 $printer -> text(str_repeat("\x20", 43- strlen($modelpatient->address1)));
 $printer -> text($modelpatient->nric."\n"); //no.kp
 $printer -> text($blankfront);
 $printer -> text(strtoupper($modelpatient->address2) ."\n"); // alamat line 2
 $printer -> text($blankfront);
 $printer -> text(strtoupper($modelpatient->address3));
 $printer -> text(str_repeat("\x20", 43 - strlen($modelpatient->address3)));
 $printer -> text($modelpatient->phone_number."\n\n"); //no.telephone
 $printer -> text($fixbackblank2);
 $printer -> text(" "."\n\n"); //kes polis
 $printer -> text($blank1over2);
 $printer -> text(strtoupper($modelpatient->sex)); // jantina
 $printer -> text(str_repeat("\x20", 15- strlen($modelpatient->sex)));
 $printer -> text($patientdob); // tarikh lahir
 $printer -> text($blank2over2);
 $printer -> text($age); //umur
 $printer -> text(str_repeat("\x20", 12- strlen($age)));
 $printer -> text(strtoupper($modelpatient->race)); // race
 $printer -> text(str_repeat("\x20", 17 - strlen($modelpatient->race)));
 $printer -> text(strtoupper($modelpatient->nationality)."\n\n"); // warganegara
 $printer -> text($blank1over2);
 $printer -> text("     "); // agama
 if ($noknull == 0)
             {
                 $printer -> text((str_repeat("\x20" , 38).strtoupper($modelnok->nok_name)."\n\x20\n")); // nama penuh waris "\x20" , 43 - strlen($agama)
                 //$printer -> text($blankfront);
                 //$printer -> text("taraf perkahwinan"); // taraf perkahwinan
                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address1)."\n")); // alamat terkini waris str_repeat("\x20" , 43 - strlen($tarafperkahwinan)
                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address2)."\n"));
                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address3)."\n"));
                //print nok here
             }
             else{
                 $printer -> text("\n\x20\n\n\n\n");
             }
 
 $printer -> text("\x20\n\n"); 
 $printer -> text($blankfront);
 $printer -> text(strtoupper($modelpatient->job)); //perkejaan
 $printer -> text(str_repeat("\x20" , 42 - strlen($modelpatient->job)));
 $printer -> text("       " . "\n\n"); // kategori pesakit atm
 $printer -> text($blankfront);
 $printer -> text(date("d/m/Y H:i" , strtotime($model->entry_datetime)) . "\n");
 $printer -> text(str_repeat("\x20" , 41 - strlen($model->entry_datetime)));
 $printer -> text($model->reference);//print puncarujukan
 $printer -> text(str_repeat("\n" , 3));
 $printer -> text($blanktwkd);
 $printer -> text(date("d/m/Y" , strtotime($model->entry_datetime)) . "\n\x20\n");
 $printer -> text($blanktwkd);
 $printer -> text(strtoupper($model->initial_ward_code)."\n\x20\n");
 $printer -> text($blanktwkd);
 $printer -> text(strtoupper($model->initial_ward_class)."\n\x20\n");
 $printer -> text($blanktwkd);
 $printer -> text("   "."\n");
 //have yet add in spaces for 2nd page
                 
                     
                    
                     $printer -> close();  
                 
    
                case 'submit2':
                    //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints borang daftar". "\n\n\n");
                       
                     $printer -> text($cajblanktop); // \n = 0.4cm
                     $printer -> text($blankfront1); // space= 0.3cm， receipt column 1
                     //$blankback = str_repeat("\x20", 55 - 12 - strlen($patientname)); get patient name from database
                     $printer -> text($model->rn); // r/n
                     $printer -> text(str_repeat("\x20", 34- strlen($model->rn))); // space for r/n value
                     $printer -> text(date("d/m/Y" , strtotime($model->entry_datetime)));
                     $printer -> text(str_repeat("\x20", 15));
                     $printer -> text(date("H:i" , strtotime($model->entry_datetime))."\n\n"); // masa
                     $printer -> text($blankfront1);
                     $printer -> text(strtoupper($modelpatient->name).str_repeat("\x20", 39 - strlen($modelpatient->name))); //nama 39 - 14/15 where 14 = "5.No k/p      "
                    // $blankback = str_repeat("\x20", 38 - 8 - strlen($patientname));
                     //$printer -> text("\x20", 38 - 8 - strlen($patientname));
                     $printer -> text($modelpatient->nric ."\n"); // ic number
                     $printer -> text(str_repeat("\x20", 49));
                     $printer -> text(strtoupper($modelpatient->nationality)."\n\n"); //warganegara
                     $printer -> text($blankfront1);
                     $printer -> text(strtoupper($modelpatient->sex)); //gender
                     $printer -> text(str_repeat("\x20", 35 - strlen($modelpatient->sex)));
                     $printer -> text($age); //umur need to add in strlen like like 525 in phase 2
                     $printer -> text(str_repeat("\x20", 21-strlen($age)));
                     $printer -> text(" " ."\n\n"); //status
                     $printer -> text(str_repeat("\x20", 18));
                     $printer -> text(strtoupper($modelpatient->address1) . "\n"); //address
                     $printer -> text(str_repeat("\x20", 18));
                     $printer -> text(strtoupper($modelpatient->address2)."\n");
                     $printer -> text(str_repeat("\x20", 18));
                     $printer -> text(strtoupper($modelpatient->address3));
                     $printer -> text("\n\n\n");
                     $printer -> text($blankfront1); // race
                     $printer -> text(strtoupper($model->initial_ward_code));
                     $printer -> text(str_repeat("\x20", 49-strlen($model->initial_ward_code))); // may nt be accurate
                     $printer -> text(strtoupper($model->initial_ward_class)); // warganegara
                       
                        $printer -> close(); 

                case 'submit3':
                    //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints casehistory". "\n\n\n");
                       
                     $printer -> text("\n\n\x20\n\x20\n"); // \n = 0.4cm
                     $printer -> text($model->entry_datetime);
                     $printer -> close();
                     
                     $printer -> text($caseblankfront); // space= 0.3cm， receipt column 1
                     //$blankback = str_repeat("\x20", 59 - 22 - strlen($patientname)); get patient name from database
                     $printer -> text(strtoupper($modelpatient->name)); // name
                     $printer -> text(str_repeat("\x20", 44 - strlen($modelpatient->name))); // space for r/n value
                     $printer -> text($model->rn."\n"); // r/n
                     $printer -> text($caseblankfront);
                     $printer -> text(strtoupper($modelpatient->address1)."\n"); // adress
                     $printer -> text($caseblankfront);
                     //$blankback = str_repeat("\x20", 59 - 22 - strlen($addl2)); get patient name from database
                     $printer -> text(strtoupper($modelpatient->address2)); // adress l2
                     $printer -> text(str_repeat("\x20", 44 - strlen($modelpatient->address2))); //need 12 more space after pa2
                     $printer -> text($modelpatient->nric."\n");//ic
                     $printer -> text($caseblankfront);
                     $printer -> text(strtoupper($modelpatient->address3) ."\n\n"); // adress l3
                     $printer -> text($caseblankfront);
                     $printer -> text($age); //age
                     $printer -> text(str_repeat("\x20", 10-strlen($age)));
                     $printer -> text(strtoupper($modelpatient->sex)); // gender
                     $printer -> text(str_repeat("\x20", 13- strlen($modelpatient->sex)));
                     $printer -> text(strtoupper(mb_strimwidth($modelpatient->race, 0,2))); //race
                     $printer -> text(str_repeat("\x20", 22 - strlen($modelpatient->race)));
                     $printer -> text(" " . "\n\n"); //religion
                     $printer -> text($caseblankfront);
                     $printer -> text(strtoupper($modelpatient->job). "\n\n"); //occupation
                     $printer -> text($caseblankfront);
                     $printer -> text(strtoupper($model->guarantor_name ."\n\n")); //gurantor be default
                     
                     if ($noknull == 0)
                {
                    $printer -> text($caseblankfront);
                    $printer -> text(strtoupper($modelnok->nok_name)."\n"); // nama penuh waris
                    $printer -> text($caseblankfront2);
                    $printer -> text(strtoupper(mb_strimwidth($modelnok->nok_address1,0,17))."\n");
                    $printer -> text($caseblankfront2);
                     $printer -> text(strtoupper(mb_strimwidth($modelnok->nok_address2,0,17)) ."\n"); //address l2
                     $printer -> text($caseblankfront2);
                     $printer -> text(strtoupper(mb_strimwidth($modelnok->nok_address3,0,17))); // address line3
                   //print nok here
                }
                if ($noknull == 0)
                {
                    $printer -> text(str_repeat("\x20",  30-strlen($printnokadd3)));
                }
                else
                {
                    $printer -> text(str_repeat("\n", 4 ));
                    $printer -> text(str_repeat("\x20", 45 ));
                }
                     //$blankback = str_repeat("\x20", 41 - 16 - strlen($addl3)); get patient name from database
                     //$printer -> text(str_repeat("\x20", 33 - 1-strlen($printnokadd3)));
                     $printer -> text(date("d/m/Y" , strtotime($printentry))." ". date("H:i" , strtotime($printentry)));
                     $printer -> text("\n\n");
                     $printer -> text($caseblankfront2);
                     if ($noknull ==0)
                     {
                        $printer -> text($modelnok->nok_phone_number."\n\n");
                     }
                        $printer -> text("   "."\n\n");//phone
                     
                     
                
                     $printer -> close();

                case 'submit4':
                   // $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                   $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints borang daftar". "\n\n\n");
                       
                     $n=6;
                     for($i=1; $i<=1; $i++)  //$i<=6 
                     {
                     //for($j=1; $j<=3; $j++)
                     //{
                     //}
                     for($k=1; $k<=6; $k++)
                     {
                        $printer -> text(strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 30-10 - strlen($modelpatient->name))  .$agesticker.str_repeat("\x20", 26 - strlen($agesticker)).strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 31-10 - strlen($modelpatient->name)) .$agesticker.str_repeat("\x20", 27 - strlen($agesticker)).strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 30 -10- strlen($modelpatient->name)) .$agesticker);
                        $printer -> text("\n"."KP:".$modelpatient->nric . str_repeat("\x20", 17 - strlen($modelpatient->nric)) ."NP:".$printrn.$stickerblankafterRN."KP:".$modelpatient->nric . str_repeat("\x20", 18 - strlen($modelpatient->nric)) ."NP:".$printrn.$stickerblankafterRN2."KP:".$modelpatient->nric. str_repeat("\x20", 17 - strlen($modelpatient->nric)) ."NP:".$printrn."   ");
                        $printer -> text("\n"."wardNo"." Katil: " .strtoupper($model->initial_ward_code). str_repeat("\x20", 8 -2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)). $stickerblankaftergender."wardNo"." Katil:" .strtoupper($model->initial_ward_code).str_repeat("\x20", 10-2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)).$stickerblankaftergender1."wardNo"." Katil:" .strtoupper($model->initial_ward_code).str_repeat("\x20", 9-2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)) ."   ");
                        $printer ->text("\n");
                        //$printer -> text("\n"."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."   ");
                     }
                     $printer ->text ("\n\n\n");
                     }
                     
                     
                 
                     $printer -> close();

                case 'submit5':    
                    //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                    $printer = new Printer($connector);
                    $printer -> text("\n\x20\n\x20\n\x20\n\x20\n"); // \n = 0.4cm
                    $printer -> text($blankfront); // space= 0.3cm， receipt column 1
                    $printer -> text(strtoupper($modelpatient->name));
                    //$blankback = ); 
                   // $printer -> text("aaabbbccc"); // patientname
$printer -> text(str_repeat("\x20", 52- strlen($modelpatient->name))); // space for r/n value
$printer -> text($model->rn."\n\n\n"); // R/n
$printer -> text($blankfront);
$printer -> text(strtoupper($modelpatient->address1)); // alamat line 1
$printer -> text(str_repeat("\x20", 43- strlen($modelpatient->address1)));
$printer -> text($modelpatient->nric."\n"); //no.kp
$printer -> text($blankfront);
$printer -> text(strtoupper($modelpatient->address2) ."\n"); // alamat line 2
$printer -> text($blankfront);
$printer -> text(strtoupper($modelpatient->address3));
$printer -> text(str_repeat("\x20", 43 - strlen($modelpatient->address3)));
$printer -> text($modelpatient->phone_number."\n\n"); //no.telephone
$printer -> text($fixbackblank2);
$printer -> text(" "."\n\n"); //kes polis
$printer -> text($blank1over2);
$printer -> text(strtoupper($modelpatient->sex)); // jantina
$printer -> text(str_repeat("\x20", 15- strlen($modelpatient->sex)));
$printer -> text($patientdob); // tarikh lahir
$printer -> text($blank2over2);
$printer -> text($age); //umur
$printer -> text(str_repeat("\x20", 12- strlen($age)));
$printer -> text(strtoupper($modelpatient->race)); // race
$printer -> text(str_repeat("\x20", 17 - strlen($modelpatient->race)));
$printer -> text(strtoupper($modelpatient->nationality)."\n\n"); // warganegara
$printer -> text($blank1over2);
$printer -> text("     "); // agama
if ($noknull == 0)
            {
                $printer -> text((str_repeat("\x20" , 38).strtoupper($modelnok->nok_name)."\n\x20\n")); // nama penuh waris "\x20" , 43 - strlen($agama)
                //$printer -> text($blankfront);
                //$printer -> text("taraf perkahwinan"); // taraf perkahwinan
                $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address1)."\n")); // alamat terkini waris str_repeat("\x20" , 43 - strlen($tarafperkahwinan)
                $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address2)."\n"));
                $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address3)."\n"));
               //print nok here
            }
            else{
                $printer -> text("\n\x20\n\n\n\n");
            }

$printer -> text("\x20\n\n"); 
$printer -> text($blankfront);
$printer -> text(strtoupper($modelpatient->job)); //perkejaan
$printer -> text(str_repeat("\x20" , 42 - strlen($modelpatient->job)));
$printer -> text("       " . "\n\n"); // kategori pesakit atm
$printer -> text($blankfront);
$printer -> text(date("d/m/Y H:i" , strtotime($model->entry_datetime)) . "\n");
$printer -> text(str_repeat("\x20" , 41 - strlen($model->entry_datetime)));
$printer -> text($model->reference);//print puncarujukan
$printer -> text(str_repeat("\n" , 3));
$printer -> text($blanktwkd);
$printer -> text(date("d/m/Y" , strtotime($model->entry_datetime)) . "\n\x20\n");
$printer -> text($blanktwkd);
$printer -> text(strtoupper($model->initial_ward_code)."\n\x20\n");
$printer -> text($blanktwkd);
$printer -> text(strtoupper($model->initial_ward_class)."\n\x20\n");
$printer -> text($blanktwkd);
$printer -> text("   "."\n");
//have yet add in spaces for 2nd page
                
                    
                   
                     
                    
                     $printer -> close();  
                 
                    
            }
           
         }


        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
                'rn' => $model->rn));      
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionPrint1($rn)
    {
      //  if(Yii::$app->request->get('confirmprint') == 't')
        {
            $model = $this->findModel($rn);     
            $modelpatient = Patient_information::findOne(['patient_uid' => $model->patient_uid]);
            $modelnok = Patient_next_of_kin::findOne(['patient_uid' => $modelpatient->patient_uid]);
    
            $getrn = arrayHelper::toArray($model->rn);
            $getname = ArrayHelper::toArray($modelpatient->name);
            $getaddress1 = ArrayHelper::toArray($modelpatient->address1);
            $getaddress2 = ArrayHelper::toArray($modelpatient->address2);
            $getaddress3 = ArrayHelper::toArray($modelpatient->address3);
            $getic = ArrayHelper::toArray($modelpatient->nric);
            $getphone = ArrayHelper::toArray($modelpatient->phone_number);
            $getgender = ArrayHelper::toArray($modelpatient->sex);
            $getjob = arrayHelper::toArray($modelpatient->job);
            //$getdateofbirth = ArrayHelper::toArray($modelpatient->dob);
            //$getget = ArrayHelper::toArray($modelpatient->age); or mayb function from dob
            $getrace = ArrayHelper::toArray($modelpatient->race);
            $getnationality = ArrayHelper::toArray($modelpatient->nationality);
            //getreligion = ArrayHelper::toArray($modelpatient->religion);
            $getentrydate = ArrayHelper::toArray($model->entry_datetime);
            $getwardcode = ArrayHelper::toArray($model->initial_ward_code);
           $getwardclass = ArrayHelper::toArray($model->initial_ward_class);
            $getemployer = ArrayHelper::toArray($model->guarantor_name);
            $getreference = ArrayHelper::toArray($model->reference);
            
    try {
        $noknull = 0;
            $getnok = ArrayHelper::toArray($modelnok->nok_name);
            $getnokadd1 = ArrayHelper::toArray($modelnok->nok_address1);
            $getnokadd2 = ArrayHelper::toArray($modelnok->nok_address2);
            $getnokadd3 = ArrayHelper::toArray($modelnok->nok_address3);
            $printnokname = implode($getnok);
            $printnokadd1 = implode($getnokadd1);
            $printnokadd2 = implode($getnokadd2);
            $printnokadd3 = implode($getnokadd3);
    }
    catch (Exception $e ){
        $noknull = 1;


    }

            $patientname = implode($getname);
            $printrn = implode($getrn);
            $patientaddress1 = implode($getaddress1);
            $patientaddress2 = implode($getaddress2);
            $patientaddress3 = implode($getaddress3);
            $printic = implode($getic);
            $printphone = implode($getphone);
            $printgender = implode($getgender);
                    //no age and date of birth
            $printrace = implode($getrace);
            $printnationality = implode($getnationality);
            $printjob = implode($getjob);
            $printwardcode = implode($getwardcode);
            $printwardclass = implode($getwardclass);
            $printentry = implode($getentrydate);
            $printreference =implode($getreference);
            
            
            
            
            if($modelpatient->nric != ""){
                $nric = $modelpatient->nric;
                if(strlen($nric) == 12){
                    $printic = $nric[0].$nric[1].$nric[2].$nric[3].$nric[4].$nric[5]."-".$nric[6].$nric[7]."-".$nric[8].$nric[9].$nric[10].$nric[11];
                    $dob = mb_strimwidth($nric,0,6);
                    $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
                    $patientdob = date("d/m/Y" , strtotime($dateofbirth));
                    $today = date("y-m-d");
                    $diff = date_diff(date_create($dateofbirth),date_create($today));
                    $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');
                }
                
            }
            else{
                $age = "";
                $patientdob = "";
                $printic = $modelpatient->nric;
            }
            
        //    print_r($age);

        //    exit();
            
            

            
            

    
        $blankfront = (int)str_repeat("\x20", 11); // adds 11 spaces
    $blankfront1 = str_repeat("\x20", 10);
    $fixbackblank = str_repeat("\x20", 43);
    $fixbackblank1 = str_repeat("\x20", 30);
    $fixbackblank2 = str_repeat("\x20", 60);
    $blank1over2 = str_repeat("\x20", 6); 
    $blank2over2 = str_repeat("\x20", 8);
    $blank2over3 = str_repeat("\x20", 4);
    $fixbackblank3 = str_repeat("\x20", 35);
    $blanktop = str_repeat("\n", 5);
    $sixbreakline = str_repeat("\n", 6);
    $blanktwkd = str_repeat("\x20", 18); 
    $entrydatein = date("d/m/Y H:i" , strtotime($model->entry_datetime));
    //$entrytime = date("d/m/Y H:i" , strtotime($model->entry_datetime));
    $entrydate = date("d/m/Y" , strtotime($model->entry_datetime));

    //if($this->request->isPost && $model->load($this->request->post()))
    //{

            // $form->$this->println(
            //     [
            //         [...blank for x],
            //         [namestuff],
            //         [...blank for y],
            //         [rn],
            //     ]
            // );
            if($model->validate()) {
                if (Yii::$app->params['printerstatus'] == "true")
                {
                    $form = new PrintForm(PrintForm::BorangDaftarMasuk);
                    $form->printNewLine(5);
                    // $array = [{($blankfront, "\x20"), (52, "james", true)}];
        
                    // (name and rn)
                    if(strlen($modelpatient->name) > 32){
                        $first = substr($modelpatient->name, 0, 32);
                        $second = substr($modelpatient->name, 33, 65);
        
                        $form->printElementArray(
                            [
                                [9, "\x20"],
                                [32, $first, true],
                                [20,"\x20"],
                                [11, $model->rn]
                            ]
                        );
                        $form->printNewLine(1);
        
                        $form->printElementArray(
                            [
                                [9, "\x20"],
                                [32, $second, true],
                            ]
                        );
                        $form->printNewLine(2);
                    }
                    else{
                        $form->printElementArray(
                            [
                            //[5, "\n"],
                                [9, "\x20"],
                                [32, $modelpatient->name, true],
                                [20,"\x20"],
                                [11, $model->rn]
                            ]
                        );
                        $form->printNewLine(3);
                    }
                    
        
                    // (address and ic)
                    $form->printElementArray(
                        [
                            [5, "\x20"],
                            [38, $modelpatient->address1,true],
                            [6,"\x20"],
                            [14, $printic],
                        ]
                    );
                    $form->printNewLine(1);
        
                    // (address 2)
                    $form->printElementArray(
                        [
                            [5,"\x20"],
                            [38, $modelpatient->address2,true],
                        ]
                    );
                    $form->printNewLine(1);
        
                    // (address 3 and phone number)
                    $form->printElementArray( 
                        [  
                            [5,"\x20"],  
                            [38, $modelpatient->address3,true],
                            [10,"\x20"], 
                            [15, $modelpatient->phone_number],  
                        ]
                    );
                    $form->printNewLine(2);
        
                    $form->printElementArray( 
                        [  
                           //[60, "\x20"],
                            //[notsurelength,casepolis]
                        ]
                    );
                    $form->printNewLine(2);
        
                   $form->printElementArray( 
                        [  
                            [6,"\x20"],
                            [9,$modelpatient->sex,true],
                            [6,"\x20"],
                            [10,$patientdob],
                            [4,"\x20"],
                            [8,$age],
                            [4, "\x20"],
                            [10,$modelpatient->race,true],
                            [4, "\x20"],
                            [9,$modelpatient->nationality,true],
                        ]
                    );
                    $form->printNewLine(2);
                    // if ($noknull == 0)
        //             {
        //                 $printer -> text((str_repeat("\x20" , 38).strtoupper($modelnok->nok_name)."\n\x20\n")); // nama penuh waris "\x20" , 43 - strlen($agama)
        //                 //$printer -> text($blankfront);
        //                 //$printer -> text("taraf perkahwinan"); // taraf perkahwinan
        //                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address1)."\n")); // alamat terkini waris str_repeat("\x20" , 43 - strlen($tarafperkahwinan)
        //                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address2)."\n"));
        //                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address3)."\n"));
        //                //print nok here
        //             }
        //             else{
        //                 $printer -> text("\n\x20\n\n\n\n");
        //             }
                        if ($noknull == 0)
                        {
                            $form->printElementArray( 
                                [  
                                      //[6, "\x20"],
                                    //[ntsure, $agama],
                                    [49, "\x20"],
                                    [30,$modelnok->nok_name,true],
                                ]
                            );
                            $form->printNewLine(2);
                            $form->printElementArray( 
                                [  
                                    [41, "\x20"],  //52-13
                                    [35,$modelnok->nok_address1,true],
                                ]
                            );
                            $form->printNewLine(1);
                            $form->printElementArray( 
                                [  
                                    [41, "\x20"],
                                    [35,$modelnok->nok_address2,true],
                                ]
                            );
                            $form->printNewLine(1);
                            $form->printElementArray( 
                                [  
                                    [41, "\x20"],
                                    [35,$modelnok->nok_address3,true],
                                ]
                            );
                            $form->printNewLine(1);
                        }
                        else
                        {
                            $form->printNewLine(5);
                        }
                   
                   
                        $form->printNewLine(2);
                    $form->printElementArray( 
                        [  
                            [11,"\x20"],
                            [20,$modelpatient->job,true],
                            [22,"\x20"],
                           // [20,"to be fill"],
                        ]
                    );
                    $form->printNewLine(2);
                    $form->printElementArray( 
                        [  
                            [11,"\x20"],    
                            [15,$entrydatein],
                            [23,"\x20"],
                            [30, $model->reference,true ],
                        ]
                    );
                    $form->printNewLine(4);
                    $form->printElementArray( 
                        [  
                            [18,"\x20"],
                            [11,$entrydate],
                        ]
                    );
                    $form->printNewLine(2);
                    $form->printElementArray( 
                        [  
                            [18,"\x20"],
                            [10,$model->initial_ward_code,true],
                        ]
                    );
                    $form->printNewLine(2);
                    $form->printElementArray( 
                        [  
                             [18,"\x20"],
                            [10,$model->initial_ward_class,true],
                        ]
                    );
                    $form->printNewLine(2);
                    $form->printElementArray( 
                        [  
                            [18,"\x20"],
                            // [10,"Disiplin",true],
                        ]
                    );
                    $form->printElementArray(
                        [
                           // [5, "\n"],
                           // [11, "\x20"],
                           // [32, $modelpatient->name, true],
                          //  [20,"\x20"],
                           // [11, $model->rn],
                           // [3, "\n"],
                            //[1, "\n"],
                            
                          // [1, "\n"],
                            
                            //[2,"\n"],
                            
                           // [2,"\n"],
                           
                           // [2,"\n"],
                            //[6, "\x20"],
                            //[ntsure, $agama],
                          //  [49,$modelnok->nok_name,true],
                          // [2,"\n"],
                            //[11, "\x20"],
                            //[idk,tarafperkahwinan],
                           // [52, "\x20"],
                            //[43,$modelnok->nok_address1,true],
                           // [1,"\n"],
                           // [52, "\x20"],
                            //[43,$modelnok->nok_address2,true],
                           // [1,"\n"],
                           // [52, "\x20"],
                           // [43,$modelnok->nok_address3,true],
                           // [2,"\n"],
                           // [11,"\x20"],
                           // [20,$modelpatient->job,true],
                           // [22,"\x20"],
                           // [20,"to be fill"],
                           // [2,"\n"],
                            //[11,"\x20"],    
                           // [15,$entrydatein],
                           // [26,"\x20"],
                           // [43, $model->reference,true ],
                            //[3,"\n"],
                            // [18,"\x20"],
                            // [11,$entrydate],
                            // [2, "\n"],
                            // [18,"\x20"],
                            // [10,$model->initial_ward_code,true],
                        //     [2, "\n"],
                        //     [18,"\x20"],
                        //     [10,$model->initial_ward_class,true],
                        //     [2, "\n"],
                        //     [18,"\x20"],
                        //     [10,"Disiplin",true],
        
        
        
        
                        ]
                    );
                    $form->close();
                }
    else{
        return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
        'rn' => $model->rn));  
    }
            }
 

            

             // $form->printElement(5, "\n");
            // $form->printElement(11, "\x20");
            // $form->printElement(32, $modelpatient->name, true);
            // $form->printElement(20,"\x20");
            // $form->printElement(11,$model->rn); 
        
            //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
            // $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
            //     $printer = new Printer($connector);
            
                   
//                     $printer -> text("\n\x20\n\x20\n\x20\n\x20\n"); // \n = 0.4cm
//                     $printer -> text($blankfront); // space= 0.3cm， receipt column 1
//                     $printer -> text(strtoupper(mb_strimwidth($modelpatient->name,0,50)));
// $printer -> text(str_repeat("\x20", 52- strlen($modelpatient->name))); // space for r/n value
// $printer -> text($model->rn."\n\n\n"); // R/n
// $printer -> text($blankfront);
// $printer -> text(strtoupper($modelpatient->address1)); // alamat line 1
// $printer -> text(str_repeat("\x20", 43- strlen($modelpatient->address1)));
// $printer -> text($printic."\n"); //no.kp
// $printer -> text($blankfront);
// $printer -> text(strtoupper($modelpatient->address2) ."\n"); // alamat line 2
// $printer -> text($blankfront);
// $printer -> text(strtoupper($modelpatient->address3));
// $printer -> text(str_repeat("\x20", 43 - strlen($modelpatient->address3)));
// $printer -> text($printphone."\n\n"); //no.telephone
// $printer -> text($fixbackblank2);
// $printer -> text(" "."\n\n"); //kes polis
// $printer -> text($blank1over2);
// $printer -> text(strtoupper($modelpatient->sex)); // jantina
// $printer -> text(str_repeat("\x20", 15- strlen($modelpatient->sex)));
// $printer -> text($patientdob); // tarikh lahir
// $printer -> text($blank2over3);
// $printer -> text($age); //umur
// $printer -> text(str_repeat("\x20", 12- strlen($age)));
// $printer -> text(strtoupper($modelpatient->race)); // race
// $printer -> text(str_repeat("\x20", 19 - strlen($modelpatient->race)));
// $printer -> text(strtoupper($modelpatient->nationality)."\n\n"); // warganegara
// $printer -> text($blank1over2);
// $printer -> text("     "); // agama
// if ($noknull == 0)
//             {
//                 $printer -> text((str_repeat("\x20" , 38).strtoupper($modelnok->nok_name)."\n\x20\n")); // nama penuh waris "\x20" , 43 - strlen($agama)
//                 //$printer -> text($blankfront);
//                 //$printer -> text("taraf perkahwinan"); // taraf perkahwinan
//                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address1)."\n")); // alamat terkini waris str_repeat("\x20" , 43 - strlen($tarafperkahwinan)
//                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address2)."\n"));
//                 $printer -> text((str_repeat("\x20" , 52).strtoupper($modelnok->nok_address3)."\n"));
//                //print nok here
//             }
//             else{
//                 $printer -> text("\n\x20\n\n\n\n");
//             }

// $printer -> text("\x20\n\n"); 
// $printer -> text($blankfront);
// $printer -> text(strtoupper($modelpatient->job)); //perkejaan
// $printer -> text(str_repeat("\x20" , 42 - strlen($modelpatient->job)));
// $printer -> text("       " . "\n\n"); // kategori pesakit atm
// $printer -> text($blankfront);
// $printer -> text(date("d/m/Y H:i" , strtotime($model->entry_datetime)) );
// $printer -> text(str_repeat("\x20" , 41 - strlen($model->entry_datetime)));
// $printer -> text(strtoupper($model->reference)."\n");//print puncarujukan
// $printer -> text(str_repeat("\n" , 3));
// $printer -> text($blanktwkd);
// $printer -> text(date("d/m/Y" , strtotime($model->entry_datetime)) . "\n\x20\n");
// $printer -> text($blanktwkd);
// $printer -> text(strtoupper($model->initial_ward_code)."\n\x20\n");
// $printer -> text($blanktwkd);
// $printer -> text(strtoupper($model->initial_ward_class)."\n\x20\n");
// $printer -> text($blanktwkd);
// $printer -> text("   "."\n");
// //have yet add in spaces for 2nd page
                
                    
                   
//                     $printer -> close(); 
                  
                   // $printer -> close(); 
                    
            // }
            
             
   }
    return $this->render('update', [
        'model' => $model,
    ]);
/*
        else 
            echo '<script type="text/javascript">',
                    'setTimeout(function(){',
                        'confirmPrintAction();',
                        '},200);',
                '</script>'; 

        */

    }

    public function actionPrint2($rn)
    {
      //  if(Yii::$app->request->get('confirmprint') == 't')
        {
            $model = $this->findModel($rn);     
            $modelpatient = Patient_information::findOne(['patient_uid' => $model->patient_uid]);
            $modelnok = Patient_next_of_kin::findOne(['patient_uid' => $modelpatient->patient_uid]);
    
            $getrn = arrayHelper::toArray($model->rn);
            $getname = ArrayHelper::toArray($modelpatient->name);
            $getaddress1 = ArrayHelper::toArray($modelpatient->address1);
            $getaddress2 = ArrayHelper::toArray($modelpatient->address2);
            $getaddress3 = ArrayHelper::toArray($modelpatient->address3);
            $getic = ArrayHelper::toArray($modelpatient->nric);
            $getphone = ArrayHelper::toArray($modelpatient->phone_number);
            $getgender = ArrayHelper::toArray($modelpatient->sex);
            $getjob = arrayHelper::toArray($modelpatient->job);
            //$getdateofbirth = ArrayHelper::toArray($modelpatient->dob);
            //$getget = ArrayHelper::toArray($modelpatient->age); or mayb function from dob
            $getrace = ArrayHelper::toArray($modelpatient->race);
            $getnationality = ArrayHelper::toArray($modelpatient->nationality);
            //getreligion = ArrayHelper::toArray($modelpatient->religion);
            $getentrydate = ArrayHelper::toArray($model->entry_datetime);
            $getwardcode = ArrayHelper::toArray($model->initial_ward_code);
           $getwardclass = ArrayHelper::toArray($model->initial_ward_class);
            $getemployer = ArrayHelper::toArray($model->guarantor_name);
            
    try {
        $noknull = 0;
            $getnok = ArrayHelper::toArray($modelnok->nok_name);
            $getnokadd1 = ArrayHelper::toArray($modelnok->nok_address1);
            $getnokadd2 = ArrayHelper::toArray($modelnok->nok_address2);
            $getnokadd3 = ArrayHelper::toArray($modelnok->nok_address3);
            $printnokname = implode($getnok);
            $printnokadd1 = implode($getnokadd1);
            $printnokadd2 = implode($getnokadd2);
            $printnokadd3 = implode($getnokadd3);
    }
    catch (Exception $e ){
        $noknull = 1;


    }

            $patientname = implode($getname);
            $printrn = implode($getrn);
            $patientaddress1 = implode($getaddress1);
            $patientaddress2 = implode($getaddress2);
            $patientaddress3 = implode($getaddress3);
            $printic = implode($getic);
            $printphone = implode($getphone);
            $printgender = implode($getgender);
                    //no age and date of birth
            $printrace = implode($getrace);
            $printnationality = implode($getnationality);
            $printjob = implode($getjob);
            $printwardcode = implode($getwardcode);
            $printwardclass = implode($getwardclass);
            $printentry = implode($getentrydate);
            
            
             
            if($modelpatient->nric != ""){
                $nric = $modelpatient->nric;
                if(strlen($nric) == 12){
                    $printic = $nric[0].$nric[1].$nric[2].$nric[3].$nric[4].$nric[5]."-".$nric[6].$nric[7]."-".$nric[8].$nric[9].$nric[10].$nric[11];
                    $dob = mb_strimwidth($nric,0,6);
                    $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
                    $patientdob = date("d/m/Y" , strtotime($dateofbirth));
                    $today = date("y-m-d");
                    $diff = date_diff(date_create($dateofbirth),date_create($today));
                    $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');
                }
                
            }
            else{
                $age = "";
                $patientdob = "";
                $printic = $modelpatient->nric;
            }
            
           
            $entrydate = date("d/m/Y" , strtotime($model->entry_datetime));
            $entrytime =date("H:i" , strtotime($model->entry_datetime));
            // $nric = $modelpatient->nric;
            // $dob = mb_strimwidth($nric,0,6);
            // $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
            // $today = date("y-m-d");
            // $diff = date_diff(date_create($dateofbirth),date_create($today));
            // $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');

            
            

    
        $blankfront = str_repeat("\x20", 11); // adds 11 spaces
    $blankfront1 = str_repeat("\x20", 10);
    $fixbackblank = str_repeat("\x20", 43);
    $fixbackblank1 = str_repeat("\x20", 30);
    $fixbackblank2 = str_repeat("\x20", 60);
    $blank1over2 = str_repeat("\x20", 6); 
    $blank2over2 = str_repeat("\x20", 8);
    $blank2over3 = str_repeat("\x20", 4);
    $fixbackblank3 = str_repeat("\x20", 35);
    $blanktop = str_repeat("\n", 5);
    $sixbreakline = str_repeat("\n", 6);
    $cajblanktop = str_repeat("\n", 7);
    $cajblankfront2 = str_repeat("\x20", 23);
    $cajblankfront3 = str_repeat("\x20", 28);

    //if($this->request->isPost && $model->load($this->request->post()))
    //{
        if($model->validate()) 
        {
            if (Yii::$app->params['printerstatus'] == "true"){
                $form = new PrintForm(PrintForm::BorangCajSheet);
    
                $form->printNewLine(7);
                $form->printElementArray(
                            [
                                //caj line 1, rn, entrydate, entrytime
                                [8, "\x20"],
                                [11, $model->rn],
                                [24,"\x20"],
                                [10, $entrydate],
                                [15,"\x20"],
                                [5, $entrytime],
                            ]
                        );
                        $form->printNewLine(2);
                        if(strlen($modelpatient->name) > 25){
                        $first = substr($modelpatient->name, 0, 25);
                         $second = substr($modelpatient->name, 26, 51);
        
                         $form->printElementArray(
                            [
                                //line 2, name and nric
                                [5, "\x20"],
                                [30, $first,true],
                                [15,"\x20"],
                                [10, $printic],
                            ]
                        );
                        $form->printNewLine(1);
                        $form->printElementArray(
                            [
                                //line 2, name and nric
                                [5, "\x20"],
                                [30, $second,true],
                                [15,"\x20"],
                                [25, $modelpatient->nationality,true],
                            ]
                            );
                            $form->printNewLine(2);
                        }
                    else
                    {
                        $form->printElementArray(
                            [
                                //line 2, name and nric
                                [5, "\x20"],
                                [30, $modelpatient->name,true],
                                [15,"\x20"],
                                [10, $modelpatient->nric],
                            ]
                        );
                        $form->printNewLine(1);
                        $form->printElementArray(
                            [
                                //line for nationality 
                                [50, "\x20"],
                                [25, $modelpatient->nationality,true],
                            ]
                        );
                        $form->printNewLine(2);
        
                    }
                 $form->printElementArray(
                            [
                                //line for gender, age
                                [6, "\x20"],
                                [9, $modelpatient->sex,true],
                                [27, "\x20"],
                                [8, $age],
                                [13, "\x20"],
                               // [13, "\x20"], for status in the future
                            ]
                        );
                        $form->printNewLine(2);
        
                        $form->printElementArray(
                            [
                                [12, "\x20"],
                                [50, $modelpatient->address1,true],
                            ]
                        );
                        $form->printNewLine(1);
        
                        $form->printElementArray(
                            [
                                [12, "\x20"],
                                [50, $modelpatient->address2,true],
                            ]
                        );
                        $form->printNewLine(1);
                        $form->printElementArray(
                            [
                                [12, "\x20"],
                                [50, $modelpatient->address3,true],
                            ]
                        );
                        $form->printNewLine(2);
                        $form->printElementArray(
                            [
                                [10, "\x20"],
                                [5, $model->initial_ward_code,true],
                                [42, "\x20"],
                                [3, $model->initial_ward_class,true],
                            ]
                        );
        
                $form->close();
                
        }
        else{
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));
        }
        }
        
       
        
            //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");

            // $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
            //     $printer = new Printer($connector);
            
                   
                  //$printer -> text($cajblanktop); // \n = 0.4cm
                 // $printer -> text($blankfront1); // space= 0.3cm， receipt column 1
            //      $printer -> text($model->rn); // r/n
            //      $printer -> text(str_repeat("\x20", 34- strlen($model->rn))); // space for r/n value
            //      $printer -> text(date("d/m/Y" , strtotime($model->entry_datetime)));
            //      $printer -> text(str_repeat("\x20", 15));
            //      $printer -> text(date("H:i" , strtotime($model->entry_datetime))."\n\n"); // masa
            //      $printer -> text($blankfront1);
            //      $printer -> text(strtoupper(mb_strimwidth($modelpatient->name,0,25)).str_repeat("\x20", 25 - strlen($modelpatient->name)).str_repeat("\x20", 14)); //nama 39 - 14/15 where 14 = "5.No k/p      "
            //      $printer -> text($modelpatient->nric ."\n"); // ic number
            //      $printer -> text(str_repeat("\x20", 49));
            //      $printer -> text(strtoupper($modelpatient->nationality)."\n\n"); //warganegara
            //      $printer -> text($blankfront1);
               //  $printer -> text(strtoupper($modelpatient->sex)); //gender
            //      $printer -> text(str_repeat("\x20", 35 - strlen($modelpatient->sex)));
            //      $printer -> text($age); //umur need to add in strlen like like 525 in phase 2
            //      $printer -> text(str_repeat("\x20", 21-strlen($age)));
            //      $printer -> text(" " ."\n\n"); //status
            //      $printer -> text(str_repeat("\x20", 18));
            //      $printer -> text($modelpatient->address1 . "\n"); //address
            //      $printer -> text(str_repeat("\x20", 18));
            //      $printer -> text($modelpatient->address2."\n");
            //      $printer -> text(str_repeat("\x20", 18));
            //      $printer -> text($modelpatient->address2);
            //      $printer -> text("\n\n");
                  //$printer -> text($blankfront1); 
            //      $printer -> text($model->initial_ward_code);
            //      $printer -> text(str_repeat("\x20", 47-strlen($model->initial_ward_code))); // may nt be accurate
            //      $printer -> text(strtoupper($model->initial_ward_class)); // warganegara
                   
            //         $printer -> close(); 
                  
                   // $printer -> close(); 
                    
            }
            
               
   // }
    return $this->render('update', [
        'model' => $model,
    ]);
/*
        else 
            echo '<script type="text/javascript">',
                    'setTimeout(function(){',
                        'confirmPrintAction();',
                        '},200);',
                '</script>'; 

        */

    }
    public function actionPrint3($rn)
    {
      //  if(Yii::$app->request->get('confirmprint') == 't')
        {
            $model = $this->findModel($rn);     
            $modelpatient = Patient_information::findOne(['patient_uid' => $model->patient_uid]);
            $modelnok = Patient_next_of_kin::findOne(['patient_uid' => $modelpatient->patient_uid]);
    /*
            $getrn = arrayHelper::toArray($model->rn);
            $getname = ArrayHelper::toArray($modelpatient->name);
            $getaddress1 = ArrayHelper::toArray($modelpatient->address1);
            $getaddress2 = ArrayHelper::toArray($modelpatient->address2);
            $getaddress3 = ArrayHelper::toArray($modelpatient->address3);
            $getic = ArrayHelper::toArray($modelpatient->nric);
            $getphone = ArrayHelper::toArray($modelpatient->phone_number);
            $getgender = ArrayHelper::toArray($modelpatient->sex);
            $getjob = arrayHelper::toArray($modelpatient->job);
            //$getdateofbirth = ArrayHelper::toArray($modelpatient->dob);
            //$getget = ArrayHelper::toArray($modelpatient->age); or mayb function from dob
            $getrace = ArrayHelper::toArray($modelpatient->race);
            $getnationality = ArrayHelper::toArray($modelpatient->nationality);
            //getreligion = ArrayHelper::toArray($modelpatient->religion);
            $getentrydate = ArrayHelper::toArray($model->entry_datetime);
            $getwardcode = ArrayHelper::toArray($model->initial_ward_code);
           $getwardclass = ArrayHelper::toArray($model->initial_ward_class);
            $getemployer = ArrayHelper::toArray($model->guarantor_name);
           */
            
    try {
        $noknull = 0;
            $getnok =$modelnok->nok_name;
            $getnokadd1 = $modelnok->nok_address1;
            $getnokadd2 = $modelnok->nok_address2;
            $getnokadd3 = $modelnok->nok_address3;
            $getnokphone = $modelnok->nok_phone_number;
            /*
            $printnokname = implode($getnok);
            $printnokadd1 = implode($getnokadd1);
            $printnokadd2 = implode($getnokadd2);
            $printnokadd3 = implode($getnokadd3);
            $printnokphone = implode($getnokphone);
            */
    }
    catch (Exception $e ){
        $noknull = 1;


    }
/*
            $patientname = implode($getname);
            $printrn = implode($getrn);
            $patientaddress1 = implode($getaddress1);
            $patientaddress2 = implode($getaddress2);
            $patientaddress3 = implode($getaddress3);
            $printic = implode($getic);
            $printphone = implode($getphone);
            $printgender = implode($getgender);
                    //no age and date of birth
            $printrace = implode($getrace);
            $printnationality = implode($getnationality);
            $printjob = implode($getjob);
            $printwardcode = implode($getwardcode);
            $printwardclass = implode($getwardclass);
            $printentry = implode($getentrydate);
            $printemployername = implode($getemployer);
            */
            
            // $nric = $modelpatient->nric;
            // $dob = mb_strimwidth($nric,0,6);
            // $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
            // $today = date("y-m-d");
            // $diff = date_diff(date_create($dateofbirth),date_create($today));
            // $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');
            // //print_r($age);

         
            if($modelpatient->nric != ""){
                $nric = $modelpatient->nric;
                if(strlen($nric) == 12){
                    $printic = $nric[0].$nric[1].$nric[2].$nric[3].$nric[4].$nric[5]."-".$nric[6].$nric[7]."-".$nric[8].$nric[9].$nric[10].$nric[11];
                    $dob = mb_strimwidth($nric,0,6);
                    $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
                    $patientdob = date("d/m/Y" , strtotime($dateofbirth));
                    $today = date("y-m-d");
                    $diff = date_diff(date_create($dateofbirth),date_create($today));
                    $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');
                }
                
            }
            else{
                $age = "";
                $patientdob = "";
                $printic = $modelpatient->nric;
            }
            $entrydate = date("d/m/Y" , strtotime($model->entry_datetime));
            $entrytime =date("H:i" , strtotime($model->entry_datetime));
            $entrydatetime =date("d/m/Y H:i" , strtotime($model->entry_datetime));
            

            
            

    
        $blankfront = str_repeat("\x20", 11); // adds 11 spaces
    $blankfront1 = str_repeat("\x20", 10);
    $fixbackblank = str_repeat("\x20", 43);
    $fixbackblank1 = str_repeat("\x20", 30);
    $fixbackblank2 = str_repeat("\x20", 60);
    $blank1over2 = str_repeat("\x20", 6); 
    $blank2over2 = str_repeat("\x20", 8);
    $blank2over3 = str_repeat("\x20", 4);
    $fixbackblank3 = str_repeat("\x20", 35);
    $blanktop = str_repeat("\n", 5);
    $sixbreakline = str_repeat("\n", 6);
    $caseblanktop = str_repeat("\n", 4);
$casefront = str_repeat("\x20", 46); //the 13.9cm extra. it wasnt counted in, therefore requires to set in again in order to test
$caseblankfront = str_repeat("\x20", 22);
$caseblankfront1 = str_repeat("\x20", 40);
$caseblankfront2 = str_repeat("\x20", 16);


    //if($this->request->isPost && $model->load($this->request->post()))
    //{

        if($model->validate())
        {
            if (Yii::$app->params['printerstatus'] == "true"){
                $form = new PrintForm(PrintForm::BorangCaseNote);
    
                $form->printNewLine(4);
                $form->printElementArray(
                    [
                        //case history note line 1, name and rn
                        [20, "\x20"], // from 22->20
                        [36, $modelpatient->name,true],
                        [10,"\x20"],
                        [11, $model->rn],
                    ]
                );
                $form->printNewLine(1);
        
                $form->printElementArray(
                    [
                        //case history note line 2,address1
                        [20, "\x20"], // from 22->20
                        [36, $modelpatient->address1,true],
                    ]
                );
                $form->printNewLine(1);
                $form->printElementArray(
                    [
                        //case history note line 3,address2 , ic
                        [20, "\x20"], // from 22->20
                        [36, $modelpatient->address2,true],
                        [10,"\x20"],
                        [14, $printic],
                    ]
                );
                $form->printNewLine(1);
                $form->printElementArray(
                    [
                        //case history note line 4,address3
                        [20, "\x20"], // from 22->20
                        [36, $modelpatient->address3,true],
                    ]
                );
                $form->printNewLine(2);
                $form->printElementArray(
                    [
                        //case history note line 5,age,gender,race,religion
                        [18, "\x20"], // from 22->20
                        [8, $age],
                        [8 , "\x20"],
                        [1, $modelpatient->sex,true],
                        [12, "\x20"],
                        [2, $modelpatient->race,true],
                        [18, "\x20"],
                       // [notsure, $religion],
                    ]
                );
                $form->printNewLine(2);
                $form->printElementArray(
                    [
                        //case history note line 6 occupation/job
                        [20, "\x20"], // from 22->20
                        [36, $modelpatient->job,true],
                    ]
                );
                $form->printNewLine(2);
                $form->printElementArray(
                    [
                        //case history note line 7, employername
                        [20, "\x20"], // from 22->20
                        [13, $model->guarantor_name,true],// phase2 change to be selective
                    ]
                );
                $form->printNewLine(2);
                if ($noknull == 0)
                 {
                    $form->printElementArray(
                        [
                            //case history note line 8, nok name
                            [20, "\x20"], // from 22->20
                            [13, $modelnok->nok_name,true],
                        ]
                    );
                    $form->printNewLine(1);
                    $form->printElementArray(
                        [
                            //case history note line 9, nok add 1
                            [17, "\x20"], // from 22->20
                            [17, $modelnok->nok_address1,true],
                        ]
                    );
                    $form->printNewLine(1);
                    $form->printElementArray(
                        [
                            //case history note line 10, nok add2
                            [17, "\x20"], // from 22->20
                            [17, $modelnok->nok_address2,true],
                        ]
                    );
                    $form->printNewLine(1);
                    $form->printElementArray(
                        [
                            //case history note line 11, nok add3 , entry datetime
                            [17, "\x20"], // from 22->20
                            [17, $modelnok->nok_address3,true],
                            [11, "\x20"],
                            [17, $entrydatetime],
                        ]
                    );
                    $form->printNewLine(1);
                 }
                 else
                 {
                     $form->printNewLine(4);
                     $form->printElementArray(
                        [
                            //case history note line 11, entrydate and time
                            [45, "\x20"], // from 22->20
                            [10, $entrydate],
                            [1, "\x20"],
                            [5, $entrytime],
                        ]
                    );
                }
                $form->printNewLine(1);
                if ($noknull == 0)
                 {
                    $form->printElementArray(
                        [
                            //case history note line 12, nok phone
                            [16, "\x20"], 
                            [13, $modelnok->nok_phone_number],
                        ]
                    );
                }
                else{
        
                    $form->printElementArray(
                        [
                            //case history note line 12, nok phone
                            [16, "\x20"], 
                            [13, " "],
                        ]
                    );
                }
        
                $form->close();
            }
        else{
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
                'rn' => $model->rn)); 
        }
        }
      
       
        //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");

    //     $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
    //     $printer = new Printer($connector);
    
    //      //$printer -> text("this prints casehistory". "\n\n\n");
           
    //      $printer -> text("\n\n\x20\n\x20\n"); // \n = 0.4cm
    //      $printer -> text($caseblankfront); // space= 0.3cm， receipt column 1
    //      $printer -> text(strtoupper(mb_strimwidth($modelpatient->name,0,34))); // name
    //      $printer -> text(str_repeat("\x20", 34 - strlen($modelpatient->name)).str_repeat("\x20", 10)); // space for r/n value
    //      $printer -> text($model->rn."\n"); // r/n
    //      $printer -> text($caseblankfront);
    //      $printer -> text(strtoupper($modelpatient->address1)."\n"); // adress
    //      $printer -> text($caseblankfront);
    //      $printer -> text(strtoupper($modelpatient->address2)); // adress l2
    //      $printer -> text(str_repeat("\x20", 44 - strlen($modelpatient->address2))); //need 12 more space after pa2
    //      $printer -> text($modelpatient->nric."\n");//ic
    //      $printer -> text($caseblankfront);
    //      $printer -> text(strtoupper($modelpatient->address3) ."\n\n"); // adress l3
    //      $printer -> text(str_repeat("\x20", 19));
    //      $printer -> text($age); //age
    //      $printer -> text(str_repeat("\x20", 13-strlen($age)));
    //      $printer -> text(strtoupper($modelpatient->sex)); // gender
    //      $printer -> text(str_repeat("\x20", 13- strlen($modelpatient->sex)));
    //      $printer -> text(strtoupper(mb_strimwidth($modelpatient->race, 0,2))); //race
    //      $printer -> text(str_repeat("\x20", 22 - strlen($modelpatient->race)));
    //      $printer -> text(" " . "\n\n"); //religion
    //      $printer -> text($caseblankfront);
    //      $printer -> text(strtoupper($modelpatient->job). "\n\n"); //occupation
    //      $printer -> text($caseblankfront);
    //      $printer -> text(strtoupper($model->guarantor_name) ."\n\n"); //gurantor be default
         
    //      if ($noknull == 0)
    // {
    //     $printer -> text(str_repeat("\x20",20));
    //     $printer -> text(strtoupper(mb_strimwidth($modelnok->nok_name,0,13))."\n"); // nama penuh waris
        //$printer -> text($caseblankfront2);
    //     $printer -> text(strtoupper(mb_strimwidth($modelnok->nok_address1,0,17))."\n");
    //     $printer -> text($caseblankfront2);
    //      $printer -> text(strtoupper(mb_strimwidth($modelnok->nok_address2,0,17)) ."\n"); //address l2
    //      $printer -> text($caseblankfront2);
    //      $printer -> text(strtoupper(mb_strimwidth($modelnok->nok_address3,0,17))); // address line3
    //    //print nok here
    // }
    // if ($noknull == 0)
    // {
    //     $printer -> text(str_repeat("\x20",  30-strlen($modelnok->nok_address3)));
    // }
    // else
    // {
    //     $printer -> text(str_repeat("\n", 4 ));
    //     $printer -> text(str_repeat("\x20", 45 ));
    // }
    //      //$printer -> text(str_repeat("\x20", 33 - 1-strlen($printnokadd3)));
    //      $printer -> text(date("d/m/Y" , strtotime($model->entry_datetime))." ". date("H:i" , strtotime($model->entry_datetime)));
    //      $printer -> text("\n\n");
    //      $printer -> text($caseblankfront2);
    //      if ($noknull ==0)
    //      {
    //         $printer -> text($modelnok->nok_phone_number."\n\n");
    //      }
    //         $printer -> text("   "."\n\n");//phone
         
         
    
    //      $printer -> close();
                  
                   // $printer -> close(); 
                    
            }
            
             
   // }
    return $this->render('update', [
        'model' => $model,
    ]);
/*
        else 
            echo '<script type="text/javascript">',
                    'setTimeout(function(){',
                        'confirmPrintAction();',
                        '},200);',
                '</script>'; 

        */

    }
    public function actionPrint4($rn)
    {
      //  if(Yii::$app->request->get('confirmprint') == 't')
        // {
            $model = $this->findModel($rn);     
            $modelpatient = Patient_information::findOne(['patient_uid' => $model->patient_uid]);
            $modelnok = Patient_next_of_kin::findOne(['patient_uid' => $modelpatient->patient_uid]);
    
            $getrn = arrayHelper::toArray($model->rn);
            $getname = ArrayHelper::toArray($modelpatient->name);
            $getaddress1 = ArrayHelper::toArray($modelpatient->address1);
            $getaddress2 = ArrayHelper::toArray($modelpatient->address2);
            $getaddress3 = ArrayHelper::toArray($modelpatient->address3);
            $getic = ArrayHelper::toArray($modelpatient->nric);
            $getphone = ArrayHelper::toArray($modelpatient->phone_number);
            $getgender = ArrayHelper::toArray($modelpatient->sex);
            $getjob = arrayHelper::toArray($modelpatient->job);
            //$getdateofbirth = ArrayHelper::toArray($modelpatient->dob);
            //$getget = ArrayHelper::toArray($modelpatient->age); or mayb function from dob
            $getrace = ArrayHelper::toArray($modelpatient->race);
            $getnationality = ArrayHelper::toArray($modelpatient->nationality);
            //getreligion = ArrayHelper::toArray($modelpatient->religion);
            $getentrydate = ArrayHelper::toArray($model->entry_datetime);
            $getwardcode = ArrayHelper::toArray($model->initial_ward_code);
           $getwardclass = ArrayHelper::toArray($model->initial_ward_class);
            $getemployer = ArrayHelper::toArray($model->guarantor_name);
            
            try {
                $noknull = 0;
                    $getnok = ArrayHelper::toArray($modelnok->nok_name);
                    $getnokadd1 = ArrayHelper::toArray($modelnok->nok_address1);
                    $getnokadd2 = ArrayHelper::toArray($modelnok->nok_address2);
                    $getnokadd3 = ArrayHelper::toArray($modelnok->nok_address3);
                    $printnokname = implode($getnok);
                    $printnokadd1 = implode($getnokadd1);
                    $printnokadd2 = implode($getnokadd2);
                    $printnokadd3 = implode($getnokadd3);
            }
            catch (Exception $e ){
                $noknull = 1;


            }

            $patientname = implode($getname);
            $printrn = implode($getrn);
            $patientaddress1 = implode($getaddress1);
            $patientaddress2 = implode($getaddress2);
            $patientaddress3 = implode($getaddress3);
            $printic = implode($getic);
            $printphone = implode($getphone);
            $printgender = implode($getgender);
                    //no age and date of birth
            $printrace = implode($getrace);
            $printnationality = implode($getnationality);
            $printjob = implode($getjob);
            $printwardcode = implode($getwardcode);
            $printwardclass = implode($getwardclass);
            $printentry = implode($getentrydate);
            
               // $nric = $modelpatient->nric;
            // $dob = mb_strimwidth($nric,0,6);
            // $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
            // $today = date("y-m-d");
            // $diff = date_diff(date_create($dateofbirth),date_create($today));
            // $agesticker = $diff->format('%Y')."yrs".$diff->format('%m')."mth".$diff->format('%d')."day"; // 15 character +
       
          
            if($modelpatient->nric != ""){
                $nric = $modelpatient->nric;
                if(strlen($nric) == 12){
                    $printic = $nric[0].$nric[1].$nric[2].$nric[3].$nric[4].$nric[5]."-".$nric[6].$nric[7]."-".$nric[8].$nric[9].$nric[10].$nric[11];
                    $dob = mb_strimwidth($nric,0,6);
                    $dateofbirth = $dob[0] . $dob[1] . "-" . $dob[2] . $dob[3] . "-".$dob[4] . $dob[5];
                    $patientdob = date("d/m/Y" , strtotime($dateofbirth));
                    $today = date("y-m-d");
                    $diff = date_diff(date_create($dateofbirth),date_create($today));
                    $age = $diff->format('%Y').",".$diff->format('%m').",".$diff->format('%d');
                    $agesticker = $diff->format('%Y')."yrs".$diff->format('%m')."mth".$diff->format('%d')."day";// 15 character +
                }
                
            }
            else{
                $age = "";
                $patientdob = "";
                $printic = $modelpatient->nric;
                $agesticker = "  yrs  mth  day";
            }
            
        
             

    
           
            $stickerblankaftername = str_repeat("\x20", 17);
            $stickerblankaftername = str_repeat("\x20", 19);
            $stickerblankafterage = str_repeat("\x20", 11);
            $stickerblankafterage1 = str_repeat("\x20", 12);
            $stickerblankafterage2 = str_repeat("\x20", 12);
            $stickerblankafterRN = str_repeat("\x20", 12);
            $stickerblankafterRN1 = str_repeat("\x20", 15 );
            $stickerblankafterRN2 = str_repeat("\x20", 12 );
            $stickerblankaftergender = str_repeat("\x20", 14 );
            $stickerblankaftergender1 = str_repeat("\x20", 14 );

     
    if($model->validate())
    {
        if (Yii::$app->params['printerstatus'] == "true"){
            $form = new PrintForm(PrintForm::BorangSticker);
            $n=6;
            for($i=1; $i<=1; $i++)  
            {
                
                for($k=1; $k<=1; $k++)  //$k<=6 
                {
                    $form->printElementArray(
                        [
                            //sticker line 1 , name, age
                            [15, $modelpatient->name,true],
                            [3,"\x20"],
                            [15, $agesticker],
                            [11,"\x20"],//17
                            [15, $modelpatient->name,true],
                            [3,"\x20"],
                            [15, $agesticker],
                            [11,"\x20"],
                            [15, $modelpatient->name,true],
                            [3,"\x20"],
                            [15, $agesticker],
                        ]
                    );
                    $form->printNewLine(1);
                    $form->printElementArray(
                        [
                            //sticker line 2 , ic rn
                            [4, "KP: "],
                            [14,$modelpatient->nric],
                            [3, "\x20"],
                            [3,"NP:"],
                            [11, $model->rn],
                            [13,"\x20"], // 47
                            [4, "KP: "],
                            [14,$modelpatient->nric],
                            [3, "\x20"],
                            [3,"NP:"],
                            [11, $model->rn],
                            [12, "\x20"],
                            [4, "KP: "],
                            [14,$modelpatient->nric],
                            [3, "\x20"],
                            [3,"NP:"],
                            [11, $model->rn],
                        ]
                    );
                    $form->printNewLine(1);
                    $form->printElementArray(
                        [
                            //sticker line 1 , name, age
                            [6, $model->initial_ward_code,true],
                            [1, "\x20"],
                            [7,"Katil: "],
                            [17,"\x20"],
                            [4, "BAN:"],
                            [2,$modelpatient->race,true],
                            [2, "\x20"],
                            [4, "Jan:"], 
                            [1,$modelpatient->sex,true], //44
                            [13, "\x20"],
                            [6, $model->initial_ward_code,true],
                            [1, "\x20"],
                            [7,"Katil: "],
                            [17,"\x20"],
                            [4, "BAN:"],
                            [2,$modelpatient->race,true],
                            [2, "\x20"],
                            [4, "Jan:"],
                            [1,$modelpatient->sex,true],
                            [12, "\x20"],
                            [6, $model->initial_ward_code,true],
                            [1, "\x20"],
                            [7,"Katil: "],
                            [17,"\x20"],
                            [4, "BAN:"],
                            [2,$modelpatient->race,true],
                            [2, "\x20"],
                            [4, "Jan:"],
                            [1,$modelpatient->sex,true]
                            
                        ]
                    );
                    $form->printNewLine(1);
                    $form->printElementArray(
                        [
                            //sticker line 4 , hospital address
                            [39, "Sarawak General Hospital,93586, Kuching"],
                            [7, "\x20"],
                            [39, "Sarawak General Hospital,93586, Kuching"],
                            [7, "\x20"],
                            [39, "Sarawak General Hospital,93586, Kuching"],
                            [3, "\x20"],
                        ]
                    );
                    $form->printNewLine(1);
                    // $form->printNewLine(2);
                    
                        // $printer -> text(strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 30-10 - strlen($modelpatient->name))  .$agesticker.str_repeat("\x20", 28 - strlen($agesticker)).strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 31-10 - strlen($modelpatient->name)) .$agesticker.str_repeat("\x20", 25 - strlen($agesticker)).strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 30 -10- strlen($modelpatient->name)) .$agesticker);
                        // $printer -> text("\n"."KP:".$modelpatient->nric . str_repeat("\x20", 17 - strlen($modelpatient->nric)) ."NP:".$printrn.$stickerblankafterRN." KP:".$modelpatient->nric . str_repeat("\x20", 18 - strlen($modelpatient->nric)) ."NP:".$printrn.$stickerblankafterRN2."KP:".$modelpatient->nric. str_repeat("\x20", 17 - strlen($modelpatient->nric)) ."NP:".$printrn."   ");
                        // $printer -> text("\n"."wardNo"." Katil: " .strtoupper($model->initial_ward_code). str_repeat("\x20", 8 -2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)). $stickerblankaftergender." wardNo"." Katil:" .strtoupper($model->initial_ward_code).str_repeat("\x20", 10-2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)).$stickerblankaftergender1."wardNo"." Katil:" .strtoupper($model->initial_ward_code).str_repeat("\x20", 9-2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)) ."   ");
                    // $printer ->text("\n");
                    // $printer -> text("\n"."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."   "."\n");
                }
                //$printer ->text ("\n\n\n");
            }
            $form->close();
        }
        else{
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));   
        }
    }
    return $this->render('update', [
        'model' => $model,
     ]);


           



            //if($this->request->isPost && $model->load($this->request->post()))
            //{
                
                    //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    /*
                    $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                        $printer = new Printer($connector);
                    
                        //$printer -> text("this prints borang daftar". "\n\n\n");
                        
                        $n=6;
                        for($i=1; $i<=1; $i++)  //$i<=6 
                        {
                            //for($j=1; $j<=3; $j++)
                            //{
                            //}
                            for($k=1; $k<=6; $k++)
                            {
                                
                                /*
                                    $printer -> text(strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 30-10 - strlen($modelpatient->name))  .$agesticker.str_repeat("\x20", 28 - strlen($agesticker)).strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 31-10 - strlen($modelpatient->name)) .$agesticker.str_repeat("\x20", 25 - strlen($agesticker)).strtoupper(mb_strimwidth($modelpatient->name,0,14)) . str_repeat("\x20", 30 -10- strlen($modelpatient->name)) .$agesticker);
                                    $printer -> text("\n"."KP:".$modelpatient->nric . str_repeat("\x20", 17 - strlen($modelpatient->nric)) ."NP:".$printrn.$stickerblankafterRN." KP:".$modelpatient->nric . str_repeat("\x20", 18 - strlen($modelpatient->nric)) ."NP:".$printrn.$stickerblankafterRN2."KP:".$modelpatient->nric. str_repeat("\x20", 17 - strlen($modelpatient->nric)) ."NP:".$printrn."   ");
                                    $printer -> text("\n"."wardNo"." Katil: " .strtoupper($model->initial_ward_code). str_repeat("\x20", 8 -2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)). $stickerblankaftergender." wardNo"." Katil:" .strtoupper($model->initial_ward_code).str_repeat("\x20", 10-2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)).$stickerblankaftergender1."wardNo"." Katil:" .strtoupper($model->initial_ward_code).str_repeat("\x20", 9-2 - strlen($model->initial_ward_code)) ."BAN:".strtoupper(mb_strimwidth($modelpatient->race,0, 2))."  "."JAN:".strtoupper(mb_strimwidth($modelpatient->sex,0, 1)) ."   ");
                                // $printer ->text("\n");
                                $printer -> text("\n"."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."   "."\n");
                            }
                            $printer ->text ("\n\n\n");
                        }
                        
                        
                    
                        $printer -> close();
                        */
                        
                        
                        // $printer -> close();                 
            // }
            
                    
        // }
             
        /*
                else 
                    echo '<script type="text/javascript">',
                            'setTimeout(function(){',
                                'confirmPrintAction();',
                                '},200);',
                        '</script>'; 

                */

    // }
}
    // public function println($array)
    // {
    //     if(!empty($array))
    //         foreach($array as $element)
    //             PrintForm::printElement($element->value[0], $element->value[1]);

    //     // $form->printNewLine();
    // }


    /**
     * Deletes an existing Patient_admission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $rn Rn
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($rn)
    {
        $this->findModel($rn)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Patient_admission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $rn Rn
     * @return Patient_admission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findModel($rn)
    {
        if (($model = Patient_admission::findOne(['rn' => $rn])) !== null) {
            return $model;
        }
        else return 0;
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

?>

<script>
// The function below will start the confirmation dialog
function confirmAction() {
    var answer = confirm("Are you sure to create patient admission?");
    if (answer) {
        window.location.href = window.location + '&confirm=t';
    } else {
        window.location.href = history.back();
    }
}

</script>