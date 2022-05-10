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
    public function actionCreate()
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
        
        If (\Yii::$app->request->isPost) {
            $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                $printer = new Printer($connector);
            switch(\yii::$app->request->post('actionPrint'))
            {
                case 'submit1':
                
                    $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints borang daftar". "\n\n\n");
                       
                        $printer -> text("\n\x20\n\x20\n\x20\n\x20"); // \n = 0.4cm
                        $printer -> text($blankfront); // space= 0.3cm， receipt column 1
                        $blankback = str_repeat("\x20", 55 - 11 - strlen($patientname)); 
                       // $printer -> text("aaabbbccc"); // patientname
    $printer -> text($fixbackblank); // space for r/n value
    $printer -> text($printrn."\n\n\n"); // R/n
    $printer -> text($blankfront);
    $printer -> text($patientaddress1); // alamat line 1
    $printer -> text($fixbackblank1);
    $printer -> text($printic."\n"); //no.kp
    $printer -> text($blankfront);
    $printer -> text($patientaddress2 ."\n"); // alamat line 2
    $printer -> text($blankfront);
    $printer -> text($patientaddress3);
    $printer -> text($fixbackblank1);
    $printer -> text($printphone."\n\n"); //no.telephone
    $printer -> text($fixbackblank2);
    $printer -> text(" "."\n\n"); //kes polis
    $printer -> text($blank1over2);
    $printer -> text($printgender); // jantina
    $printer -> text($blank2over2);
    $printer -> text("          "); // tarikh lahir
    $printer -> text($blank2over2);
    $printer -> text("      "); //umur
    $printer -> text($blank2over3);
    $printer -> text($printrace); // race
    $printer -> text($blank2over2);
    $printer -> text($printnationality."\n\n"); // warganegara
    $printer -> text($blank1over2);
    $printer -> text("     "); // agama
    if ($noknull == 0)
                {
                    $printer -> text((str_repeat("\x20" , 38).$printnokname."\n\x20\n")); // nama penuh waris
                    //$printer -> text($blankfront);
                    //$printer -> text("taraf perkahwinan"); // taraf perkahwinan
                    $printer -> text((str_repeat("\x20" , 52).$printnokadd1."\n")); // alamat terkini waris
                    $printer -> text((str_repeat("\x20" , 52).$printnokadd2."\n"));
                    $printer -> text((str_repeat("\x20" , 52).$printnokadd3."\n"));
                   //print nok here
                }
    
    $printer -> text("\x20\n\n\n"); 
    $printer -> text($blankfront);
    $printer -> text($printjob); //perkejaan
    $printer -> text($fixbackblank1 . "   ");
    $printer -> text("       " . "\n\n"); // kategori pesakit atm
    $printer -> text($blankfront);
    $printer -> text(date("d/m/Y H:i" , strtotime($printentry)) . "\n");
    $printer -> text($fixbackblank1);
    $printer -> text("       ");
    $printer -> text(str_repeat("\n" , 3));
    $printer -> text($blank1over2);
    $printer -> text(date("d/m/Y" , strtotime($printentry)) . "\n\n\n");
    $printer -> text($blank1over2);
    $printer -> text($printwardcode."\n\n");
    $printer -> text($blank1over2);
    $printer -> text($printwardclass."\n\n");
    $printer -> text($blank1over2);
    $printer -> text("   "."\n");
            $printer -> close(); 
                 
    
                case 'submit2':
                    $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints borang daftar". "\n\n\n");
                       
                     $printer -> text($cajblanktop); // \n = 0.4cm
                     $printer -> text($blankfront1); // space= 0.3cm， receipt column 1
                     //$blankback = str_repeat("\x20", 55 - 12 - strlen($patientname)); get patient name from database
                     $printer -> text($printrn); // r/n
                     $printer -> text($cajblankfront2); // space for r/n value
                     $printer -> text(date("d/m/Y" , strtotime($printentry)));
                     $printer -> text("01/01/2021"); // date
                     $printer -> text(str_repeat("\x20", 15));
                     $printer -> text(date("d/m/Y" , strtotime($printentry))."\n\n"); // masa
                     $printer -> text($blankfront1);
                     $printer -> text($patientname); //nama
                     $blankback = str_repeat("\x20", 49 - 8 - strlen($patientname));
                     $printer -> text(str_repeat("\x20", 30));
                     $printer -> text($printic ."\n"); // ic number
                     $printer -> text(str_repeat("\x20", 49));
                     $printer -> text($printnationality."\n\n"); //warganegara
                     $printer -> text($blankfront1);
                     $printer -> text($printgender); //gender
                     $printer -> text($cajblankfront3);
                     $printer -> text(" "); //umur need to add in strlen like like 525 in phase 2
                     $printer -> text(str_repeat("\x20", 19));
                     $printer -> text(" " ."\n\n"); //status
                     $printer -> text(str_repeat("\x20", 18));
                     $printer -> text($patientaddress1 . "\n"); //address
                     $printer -> text(str_repeat("\x20", 18));
                     $printer -> text($patientaddress2);
                     $printer -> text(str_repeat("\x20", 18));
                     $printer -> text($patientaddress3);
                     $printer -> text("\n\n\n");
                     $printer -> text($blankfront1); // race
                     $printer -> text($printwardcode);
                     $printer -> text(str_repeat("\x20", 47));
                     $printer -> text($printwardclass); // warganegara
                       
                        $printer -> close(); 

                case 'submit3':
                    $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints casehistory". "\n\n\n");
                       
                     $printer -> text($caseblanktop); // \n = 0.4cm
                     $printer -> text($caseblankfront); // space= 0.3cm， receipt column 1
                     //$blankback = str_repeat("\x20", 59 - 22 - strlen($patientname)); get patient name from database
                     $printer -> text($patientname); // name
                     $printer -> text($caseblankfront1); // space for r/n value
                     $printer -> text($printrn."\n"); // r/n
                     $printer -> text($caseblankfront);
                     $printer -> text($patientaddress1."\n"); // adress
                     $printer -> text($caseblankfront);
                     //$blankback = str_repeat("\x20", 59 - 22 - strlen($addl2)); get patient name from database
                     $printer -> text($patientaddress2); // adress l2
                     $printer -> text(str_repeat("\x20",  22 - strlen($patientaddress2)));
                     $printer -> text($printic."\n");//ic
                     $printer -> text($caseblankfront);
                     $printer -> text($patientaddress3 ."\n\n"); // adress l3
                     $printer -> text($caseblankfront);
                     $printer -> text(" "); //age
                     $printer -> text(str_repeat("\x20", 8));
                     $printer -> text($printgender ); // gender
                     $printer -> text(str_repeat("\x20", 12));
                     $printer -> text($printrace); //race
                     $printer -> text(str_repeat("\x20", 20));
                     $printer -> text(" " . "\n\n"); //religion
                     $printer -> text($caseblankfront);
                     $printer -> text($printjob. "\n\n"); //occupation
                     $printer -> text($caseblankfront);
                     $printer -> text($printemployername ."\n\n"); //gurantor
                     
                     if ($noknull == 0)
                {
                    $printer -> text($caseblankfront);
                    $printer -> text($printnokname."\n"); // nama penuh waris
                    $printer -> text($caseblankfront2);
                    $printer -> text($printnokadd1."\n");
                    $printer -> text($caseblankfront2);
                     $printer -> text($printnokadd2 ."\n"); //address l2
                     $printer -> text($caseblankfront2);
                     $printer -> text($printnokadd3 ); // address line3
                   //print nok here
                }
                   
                     
                     //$blankback = str_repeat("\x20", 41 - 16 - strlen($addl3)); get patient name from database
                     $printer -> text(str_repeat("\x20", 17));
                     $printer -> text(date("d/m/Y" , strtotime($printentry))." ". date("H:i" , strtotime($printentry)));
                     $printer -> text("\n\n");
                     $printer -> text($caseblankfront2);
                     $printer -> text($printnokphone."\n\n"); //phone
                     
                     
                
                     $printer -> close();

                case 'submit4':
                    $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints borang daftar". "\n\n\n");
                       
                     $n=6;
                     for($i=1; $i<=1; $i++)  //$i<=6 
                     {
                     //for($j=1; $j<=3; $j++)
                     //{
                     //}
                     for($k=1; $k<=1; $k++)
                     {
                        $printer -> text($patientname . str_repeat("\x20", 20 - strlen($patientname))  ."31"."yrs"."10"."mth"."25"."day".$stickerblankafterage.$patientname . str_repeat("\x20", 19 - strlen($patientname)) ."31"."yrs"."10"."mth"."25"."day".$stickerblankafterage1. $patientname . str_repeat("\x20", 18 - strlen($patientname)) ."31"."yrs"."10"."mth"."25"."day");
                        $printer -> text("\n"."KP:".$printic . str_repeat("\x20", 17 - strlen($printic)) ."NP:".$printrn.$stickerblankafterRN."KP:".$printic . str_repeat("\x20", 16 - strlen($printic)) ."NP:".$printrn.$stickerblankafterRN2."KP:".$printic. str_repeat("\x20", 16 - strlen($printic)) ."NP:".$printrn."   ");
                        $printer -> text("\n"."wardNo"." Katil: " .$printwardcode. str_repeat("\x20", 6 - strlen($printwardcode)) ."BAN:".mb_strimwidth($printrace,0, 2)."  "."JAN:".mb_strimwidth($printgender,0, 1). $stickerblankaftergender."wardNo"." Katil:" .$printwardcode.str_repeat("\x20", 6 - strlen($printwardcode)) ."BAN:".mb_strimwidth($printrace,0, 2)."  "."JAN:".mb_strimwidth($printgender,0, 1).$stickerblankaftergender1."wardNo"." Katil:" .$printwardcode.str_repeat("\x20", 6 - strlen($printwardcode)) ."BAN:".mb_strimwidth($printrace,0, 2)."  "."JAN:".mb_strimwidth($printgender,0, 1) ."   ");
                        $printer -> text("\n");
                     //$printer -> text("\n"."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."   ");
                     }
                     $printer ->text ("\n\n\n");
                     }
                     
                     
                 
                     $printer -> close();

                case 'submit5':    
                    $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                    $printer = new Printer($connector);
                
                     //$printer -> text("this prints borang daftar". "\n\n\n");
                       
                        $printer -> text("\n\x20\n\x20\n\x20\n\x20"); // \n = 0.4cm
                        $printer -> text($blankfront); // space= 0.3cm， receipt column 1
                        $blankback = str_repeat("\x20", 55 - 11 - strlen($patientname)); 
                       // $printer -> text("aaabbbccc"); // patientname
    $printer -> text($fixbackblank); // space for r/n value
    $printer -> text($printrn."\n\n\n"); // R/n
    $printer -> text($blankfront);
    $printer -> text($patientaddress1); // alamat line 1
    $printer -> text($fixbackblank1);
    $printer -> text($printic."\n"); //no.kp
    $printer -> text($blankfront);
    $printer -> text($patientaddress2 ."\n"); // alamat line 2
    $printer -> text($blankfront);
    $printer -> text($patientaddress3);
    $printer -> text($fixbackblank1);
    $printer -> text($printphone."\n\n"); //no.telephone
    $printer -> text($fixbackblank2);
    $printer -> text(" "."\n\n"); //kes polis
    $printer -> text($blank1over2);
    $printer -> text($printgender); // jantina
    $printer -> text($blank2over2);
    $printer -> text("          "); // tarikh lahir
    $printer -> text($blank2over2);
    $printer -> text("      "); //umur
    $printer -> text($blank2over3);
    $printer -> text($printrace); // race
    $printer -> text($blank2over2);
    $printer -> text($printnationality."\n\n"); // warganegara
    $printer -> text($blank1over2);
    $printer -> text("     "); // agama
    if ($noknull == 0)
                {
                    $printer -> text((str_repeat("\x20" , 38).$printnokname."\n\x20\n")); // nama penuh waris
                    //$printer -> text($blankfront);
                    //$printer -> text("taraf perkahwinan"); // taraf perkahwinan
                    $printer -> text((str_repeat("\x20" , 52).$printnokadd1."\n")); // alamat terkini waris
                    $printer -> text((str_repeat("\x20" , 52).$printnokadd2."\n"));
                    $printer -> text((str_repeat("\x20" , 52).$printnokadd3."\n"));
                   //print nok here
                }
    
    $printer -> text("\x20\n\n\n"); 
    $printer -> text($blankfront);
    $printer -> text($printjob); //perkejaan
    $printer -> text($fixbackblank1 . "   ");
    $printer -> text("       " . "\n\n"); // kategori pesakit atm
    $printer -> text($blankfront);
    $printer -> text(date("d/m/Y H:i" , strtotime($printentry)) . "\n");
    $printer -> text($fixbackblank1);
    $printer -> text("       ");
    $printer -> text(str_repeat("\n" , 3));
    $printer -> text($blank1over2);
    $printer -> text(date("d/m/Y" , strtotime($printentry)) . "\n\n\n");
    $printer -> text($blank1over2);
    $printer -> text($printwardcode."\n\n");
    $printer -> text($blank1over2);
    $printer -> text($printwardclass."\n\n");
    $printer -> text($blank1over2);
    $printer -> text("   "."\n");
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
    $blanktwkd = str_repeat("\x20", 18); 

    //if($this->request->isPost && $model->load($this->request->post()))
    //{
        
            $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                $printer = new Printer($connector);
            
                 //$printer -> text("this prints borang daftar". "\n\n\n");
                   
                    $printer -> text("\n\x20\n\x20\n\x20\n\x20\n"); // \n = 0.4cm
                    $printer -> text($blankfront); // space= 0.3cm， receipt column 1
                    $printer -> text($patientname);
                    //$blankback = ); 
                   // $printer -> text("aaabbbccc"); // patientname
$printer -> text(str_repeat("\x20", 63 - 11 - strlen($patientname))); // space for r/n value
$printer -> text($printrn."\n\n\n"); // R/n
$printer -> text($blankfront);
$printer -> text($patientaddress1); // alamat line 1
$printer -> text($fixbackblank1);
$printer -> text($printic."\n"); //no.kp
$printer -> text($blankfront);
$printer -> text($patientaddress2 ."\n"); // alamat line 2
$printer -> text($blankfront);
$printer -> text($patientaddress3);
$printer -> text(str_repeat("\x20", 57 - 11 - strlen($patientaddress3)));
$printer -> text($printphone."\n\n"); //no.telephone
$printer -> text($fixbackblank2);
$printer -> text(" "."\n\n"); //kes polis
$printer -> text($blank1over2);
$printer -> text($printgender); // jantina
$printer -> text($blank2over2);
$printer -> text("          "); // tarikh lahir
$printer -> text($blank2over2);
$printer -> text("        "); //umur
$printer -> text($blank2over3);
$printer -> text($printrace); // race
$printer -> text(str_repeat("\x20", 28 - 8 - strlen($printrace)));
$printer -> text($printnationality."\n\n"); // warganegara
$printer -> text($blank1over2);
$printer -> text("     "); // agama
if ($noknull == 0)
            {
                $printer -> text((str_repeat("\x20" , 38).$printnokname."\n\x20\n")); // nama penuh waris
                //$printer -> text($blankfront);
                //$printer -> text("taraf perkahwinan"); // taraf perkahwinan
                $printer -> text((str_repeat("\x20" , 52).$printnokadd1."\n")); // alamat terkini waris
                $printer -> text((str_repeat("\x20" , 52).$printnokadd2."\n"));
                $printer -> text((str_repeat("\x20" , 52).$printnokadd3."\n"));
               //print nok here
            }

$printer -> text("\x20\n\n"); 
$printer -> text($blankfront);
$printer -> text($printjob); //perkejaan
$printer -> text($fixbackblank1 . "   ");
$printer -> text("       " . "\n\n"); // kategori pesakit atm
$printer -> text($blankfront);
$printer -> text(date("d/m/Y H:i" , strtotime($printentry)) . "\n");
$printer -> text($fixbackblank1);
$printer -> text("       ");//print puncarujukan
$printer -> text(str_repeat("\n" , 3));
$printer -> text($blanktwkd);
$printer -> text(date("d/m/Y" , strtotime($printentry)) . "\n\x20\n");
$printer -> text($blanktwkd);
$printer -> text($printwardcode."\n\x20\n");
$printer -> text($blanktwkd);
$printer -> text($printwardclass."\n\x20\n");
$printer -> text($blanktwkd);
$printer -> text("   "."\n");
//have yet add in spaces for 2nd page
                
                    
                   
                    $printer -> close(); 
                  
                   // $printer -> close(); 
                    
            }
            
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));   
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
        
            $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
                $printer = new Printer($connector);
            
                 //$printer -> text("this prints borang daftar". "\n\n\n");
                   
                 $printer -> text($cajblanktop); // \n = 0.4cm
                 $printer -> text($blankfront1); // space= 0.3cm， receipt column 1
                 //$blankback = str_repeat("\x20", 55 - 12 - strlen($patientname)); get patient name from database
                 $printer -> text($printrn); // r/n
                 $printer -> text($cajblankfront2); // space for r/n value
                 $printer -> text(date("d/m/Y" , strtotime($printentry)));
                 $printer -> text(str_repeat("\x20", 15));
                 $printer -> text(date("H:i" , strtotime($printentry))."\n\n"); // masa
                 $printer -> text($blankfront1);
                 $printer -> text($patientname.str_repeat("\x20", 47 - 8 - strlen($patientname))); //nama
                // $blankback = str_repeat("\x20", 38 - 8 - strlen($patientname));
                 //$printer -> text("\x20", 38 - 8 - strlen($patientname));
                 $printer -> text($printic ."\n"); // ic number
                 $printer -> text(str_repeat("\x20", 49));
                 $printer -> text($printnationality."\n\n"); //warganegara
                 $printer -> text($blankfront1);
                 $printer -> text($printgender); //gender
                 $printer -> text($cajblankfront3);
                 $printer -> text(" "); //umur need to add in strlen like like 525 in phase 2
                 $printer -> text(str_repeat("\x20", 19));
                 $printer -> text(" " ."\n\n"); //status
                 $printer -> text(str_repeat("\x20", 18));
                 $printer -> text($patientaddress1 . "\n"); //address
                 $printer -> text(str_repeat("\x20", 18));
                 $printer -> text($patientaddress2."\n");
                 $printer -> text(str_repeat("\x20", 18));
                 $printer -> text($patientaddress3);
                 $printer -> text("\n\n\n");
                 $printer -> text($blankfront1); // race
                 $printer -> text($printwardcode);
                 $printer -> text(str_repeat("\x20", 47));
                 $printer -> text($printwardclass); // warganegara
                   
                    $printer -> close(); 
                  
                   // $printer -> close(); 
                    
            }
            
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));   
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
            $getnokphone = ArrayHelper::toArray($modelnok->nok_phone_number);
            $printnokname = implode($getnok);
            $printnokadd1 = implode($getnokadd1);
            $printnokadd2 = implode($getnokadd2);
            $printnokadd3 = implode($getnokadd3);
            $printnokphone = implode($getnokphone);
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
            $printemployername = implode($getemployer);
            
            
            
            
            
            
            

            
            

    
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
        
        $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
        $printer = new Printer($connector);
    
         //$printer -> text("this prints casehistory". "\n\n\n");
           
         //$printer -> text("\n\n\x20\n\x20\n"); // \n = 0.4cm
         $printer -> text($model->entry_datetime);
         $printer -> close();
         
         $printer -> text($caseblankfront); // space= 0.3cm， receipt column 1
         //$blankback = str_repeat("\x20", 59 - 22 - strlen($patientname)); get patient name from database
         $printer -> text($patientname); // name
         $printer -> text(str_repeat("\x20", 63 - 18 - strlen($patientname))); // space for r/n value
         $printer -> text($printrn."\n"); // r/n
         $printer -> text($caseblankfront);
         $printer -> text($patientaddress1."\n"); // adress
         $printer -> text($caseblankfront);
         //$blankback = str_repeat("\x20", 59 - 22 - strlen($addl2)); get patient name from database
         $printer -> text($patientaddress2); // adress l2
         $printer -> text(str_repeat("\x20", 56 - 12 - strlen($patientaddress2))); //need 12 more space after pa2
         $printer -> text($printic."\n");//ic
         $printer -> text($caseblankfront);
         $printer -> text($patientaddress3 ."\n\n"); // adress l3
         $printer -> text($caseblankfront);
         $printer -> text(" "); //age
         $printer -> text(str_repeat("\x20", 8));
         $printer -> text($printgender); // gender
         $printer -> text(str_repeat("\x20", 12));
         $printer -> text($printrace); //race
         $printer -> text(str_repeat("\x20", 20));
         $printer -> text(" " . "\n\n"); //religion
         $printer -> text($caseblankfront);
         $printer -> text($printjob. "\n\n"); //occupation
         $printer -> text($caseblankfront);
         $printer -> text($printemployername ."\n\n"); //gurantor
         
         if ($noknull == 0)
    {
        $printer -> text($caseblankfront);
        $printer -> text($printnokname."\n"); // nama penuh waris
        $printer -> text($caseblankfront2);
        $printer -> text(mb_strimwidth($printnokadd1,0,19)."\n");
        $printer -> text($caseblankfront2);
         $printer -> text(mb_strimwidth($printnokadd2,0,19) ."\n"); //address l2
         $printer -> text($caseblankfront2);
         $printer -> text(mb_strimwidth($printnokadd3,0,19)); // address line3
       //print nok here
    }
    if ($noknull == 0)
    {
        $printer -> text(str_repeat("\x20", 33 - 1-strlen($printnokadd3)));
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
            $printer -> text($printnokphone."\n\n");
         }
            $printer -> text("   "."\n\n");//phone
         
         
    
         $printer -> close();
                  
                   // $printer -> close(); 
                    
            }
            
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));   
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
    $stickerblankaftername = str_repeat("\x20", 17);
    $stickerblankaftername = str_repeat("\x20", 19);
    $stickerblankafterage = str_repeat("\x20", 11);
    $stickerblankafterage1 = str_repeat("\x20", 12);
    $stickerblankafterage2 = str_repeat("\x20", 12);
    $stickerblankafterRN = str_repeat("\x20", 12);
    $stickerblankafterRN1 = str_repeat("\x20", 15 );
    $stickerblankafterRN2 = str_repeat("\x20", 13 );
    $stickerblankaftergender = str_repeat("\x20", 14 );
    $stickerblankaftergender1 = str_repeat("\x20", 15 );

    //if($this->request->isPost && $model->load($this->request->post()))
    //{
        
            $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/epson");
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
                    $printer -> text(mb_strimwidth($patientname,0,14) . str_repeat("\x20", 35-11 - strlen($patientname))  ."31"."yrs"."10"."mth"."25"."day".$stickerblankafterage.mb_strimwidth($patientname,0,14) . str_repeat("\x20", 35-11 - strlen($patientname)) ."31"."yrs"."10"."mth"."25"."day".$stickerblankafterage1. mb_strimwidth($patientname,0,14) . str_repeat("\x20", 35 -11- strlen($patientname)) ."31"."yrs"."10"."mth"."25"."day");
                    $printer -> text("\n"."KP:".$printic . str_repeat("\x20", 17 - strlen($printic)) ."NP:".$printrn.$stickerblankafterRN."KP:".$printic . str_repeat("\x20", 18 - strlen($printic)) ."NP:".$printrn.$stickerblankafterRN2."KP:".$printic. str_repeat("\x20", 17 - strlen($printic)) ."NP:".$printrn."   ");
                    $printer -> text("\n"."wardNo"." Katil: " .$printwardcode. str_repeat("\x20", 8 - strlen($printwardcode)) ."BAN:".mb_strimwidth($printrace,0, 2)."  "."JAN:".mb_strimwidth($printgender,0, 1). $stickerblankaftergender."wardNo"." Katil:" .$printwardcode.str_repeat("\x20", 9 - strlen($printwardcode)) ."BAN:".mb_strimwidth($printrace,0, 2)."  "."JAN:".mb_strimwidth($printgender,0, 1).$stickerblankaftergender1."wardNo"." Katil:" .$printwardcode.str_repeat("\x20", 6 - strlen($printwardcode)) ."BAN:".mb_strimwidth($printrace,0, 2)."  "."JAN:".mb_strimwidth($printgender,0, 1) ."   ");
                    $printer ->text("\n");
                    //$printer -> text("\n"."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."       "."Sarawak General Hospital,93586, Kuching"."   ");
                 }
                 $printer ->text ("\n\n\n");
                 }
                 
                 
             
                 $printer -> close();
                   
                   
                  
                   // $printer -> close(); 
                    
            }
            
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));   
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
    public function findModel($rn)
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