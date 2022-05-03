<?php

namespace app\controllers;

require 'vendor/autoload.php';
use Yii;
use app\models\Bill;
use app\models\BillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Ward;
use yii\base\Exception;
use app\models\Model;
use app\models\Treatment_details;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Patient_information;
use app\models\Patient_admission;
use app\models\Patient_next_of_kin;
use yii\helpers\ArrayHelper;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class clearText {

    public function clearVar($text)
    {
        $updateTxt = ltrim($text,"Array ( [0] => ");
        $updateTxt = rtrim($updateTxt," )");

        return $updateTxt;
        
    }
}
/**
 * BillController implements the CRUD actions for Bill model.
 */
class BillController extends Controller
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
     * Lists all Bill models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    

    /**
     * Displays a single Bill model.
     * @param string $bill_uid Bill Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($bill_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($bill_uid),
        ]);
    }

    /**
     * Creates a new Bill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Bill();

        //Find out how many ward have been submitted by the form
        // $count = count(Yii::$app->request->post('Ward', []));
        $count = 1;

        //Send at least one model to the form
        $modelWard = [new Ward];

        //Create an array of the wards submitted
        for($i = 1; $i < $count; $i++) {
            $modelWard[] = new Ward();
        }

        $modelTreatment = [new Treatment_details];

        for($i = 1; $i < $count; $i++) {
            $modelTreatment[] = new Treatment_details();
        }

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $model->save()) {
                $modelWard = Model::createMultiple(Ward::classname());
                $modelTreatment = Model::createMultiple(Treatment_details::className());
                Model::loadMultiple($modelWard, Yii::$app->request->post());
                Model::loadMultiple($modelTreatment, Yii::$app->request->post());

                // validate all models
                $valid = $model->validate();
                $valid = Model::validateMultiple($modelWard) && $valid;
                $valid = Model::validateMultiple($modelTreatment) && $valid;
                
                if ($valid) {
                    
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            foreach ($modelWard as $modelWard) {
                                $modelWard->bill_uid = $model->bill_uid;
                                $modelWard->ward_uid = Base64UID::generate(32);
                                if (! ($flag = $modelWard->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }

                            foreach ($modelTreatment as $modelTreatment) {
                                $modelTreatment->bill_uid = $model->bill_uid;
                                $modelTreatment->treatment_details_uid = Base64UID::generate(32);
                                if (! ($flag = $modelTreatment->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            // var_dump($modelWard);
                            // exit();
                            // return $this->redirect(['view', 'bill_uid' => $model->bill_uid, 'rn' => $model->rn]);
                            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'b'));    
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
            'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details()] : $modelTreatment,
        ]);
    }


    /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($bill_uid)
    {
        $model = $this->findModel($bill_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'bill_uid' => $model->bill_uid]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
        ]);
    }

      /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGenerate($bill_uid)
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
            
        $model = $this->findModel($bill_uid);
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);

<<<<<<< Updated upstream
=======
        if ($this->request->isPost && $model->load($this->request->post())) {
            if(empty($model->bill_generation_datetime))
            {
                $model->bill_generation_datetime =  $date->format('Y-m-d H:i');
            }
            $cookies = Yii::$app->request->cookies;
            $model->generation_responsible_uid = $cookies->getValue('cookie_login');
            $model->bill_uid = Yii::$app->request->get('bill_uid');
            $model->save();

            return Yii::$app->getResponse()->redirect(array('/bill/print', 
                'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'p'));        
        }

        return $this->render('generate', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
        ]);
    }

    
      /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPrint($bill_uid)
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
            
        $model = $this->findModel($bill_uid);
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        //$model1 = $this->findModel($rn);

        

>>>>>>> Stashed changes
        // $totalWardDays = 0;
        // $dailyWardCost = 0.0;
        // $totalTreatmentCost = 0.0;
        // $billable = 0.0;

        if ($this->request->isPost && $model->load($this->request->post())) {
<<<<<<< Updated upstream
            if(empty($model->bill_generation_datetime))
            {
                $model->bill_generation_datetime =  $date->format('Y-m-d H:i');
                // foreach ($modelWard as $index => $modelWard){
                //     $totalWardDays += "[$index]ward_number_of_days";
                //     $dailyWardCost = "[$index]daily_ward_cost";
                //     $totalTreatmentCost += "[$index]item_per_unit_cost" * "[$index]item_count";
                // }
                
                // $billable = ($totalWardDays * $dailyWardCost) + $totalTreatmentCost;
                // $model->bill_generation_billable_sum_rm = $billable;
                // $model->bill_generation_final_fee_rm = $billable;
            }
            if(!empty(Yii::$app->request->get('bill_print_responsible_uid')) && empty($model->bill_print_datetime))
            {
                $model->bill_print_datetime =  $date->format('Y-m-d H:i');
            }
            $model->bill_uid = Yii::$app->request->get('bill_uid');
            $model->save();

            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'p'));        
=======

            if($model->validate())
            {
                $blankmiddle = str_repeat("\n", 11);
    $fixfront = str_repeat("\x20", 8);
    $billdate = str_repeat("\x20", 62);
    $billrn = str_repeat("\x20", 7);
    $billname = str_repeat("\x20", 7); // adds 14 spaces
    $billadd1 = str_repeat("\x20", 7);
    $billadd2 = str_repeat("\x20", 7);
    $billadd3 = str_repeat("\x20", 7);
    $billkelas = str_repeat("\x20", 35);
    $billmasuk = str_repeat("\x20", 7);
    $billkeluar = str_repeat("\x20", 22);
    $billkumpulan = str_repeat("\x20", 6);
    $fixbackblank = str_repeat("\x20", 36);
    $fixbackblank2 = str_repeat("\x20", 34);
    $fixbackblank3 = str_repeat("\x20", 35);
                //$pa2 = new Patient_admission();
                //$p_a = Patient_admission::find()->limit(1)->all(); //doesnt get latest

                $p_a = Patient_admission::find()->orderBy(['rn' => SORT_DESC])->limit(1)->all();
                $p_i = Patient_information::find()->all();
                $t_d = Treatment_details::find()->one();
                $wad1 = ward::find()->one();
                $bil1 = bill::find()->limit(1)->all();
                $rn1 = ArrayHelper::getColumn($bil1,'rn');
                //$ed1 = ArrayHelper::getColumn($p_a,'entry_datetime');
                $iwcode  = ArrayHelper::getColumn($p_a,'initial_ward_code');
                $iwclass  = ArrayHelper::getColumn($p_a,'initial_ward_class');
                //$fdate  = ArrayHelper::getColumn($p_i, 'first_reg_date');
                $pname  = ArrayHelper::getColumn($p_i, 'name');
                $add1 = ArrayHelper::getColumn($p_i, 'address1');
                $add2 = ArrayHelper::getColumn($p_i, 'address2');
                $add3 = ArrayHelper::getColumn($p_i, 'address3');
                $tcode = ArrayHelper::getColumn($t_d, 'treatment_code');
                $tname = ArrayHelper::getColumn($t_d, 'treatment_name');
                $tprice = ArrayHelper::getColumn($t_d, 'item_per_unit_cost_rm');
                $tamount = ArrayHelper::getColumn($t_d, 'item_count');
                $t_utotal = ArrayHelper::getColumn($t_d, 'item_total_unit_cost_rm');
                $wcode = ArrayHelper::getColumn($wad1, 'ward_code');
                $wstartd = ArrayHelper::getColumn($wad1, 'ward_start_datetime');
                $wendd = ArrayHelper::getColumn($wad1, 'ward_end_datetime');
                $wnod = ArrayHelper::getColumn($wad1, 'ward_number_of_day');
                $bclass = ArrayHelper::getColumn($bil1, 'class');
                $bdwc = ArrayHelper::getColumn($bil1, 'daily_ward_cost');
                $bpdate = ArrayHelper::getColumn($bil1, 'bill_print_datetime');
                $bsum = ArrayHelper::getColumn($bil1, 'bill_generation_billable_sum_rm');
                $btotal = ArrayHelper::getColumn($bil1, 'bill_generation_final_fee_rm');
               // $piname = ArrayHelper::map($pa3,'name','nric');

               
                $brn = implode($rn1);
                $bdate = implode($bpdate);
               // $bdate = implode($ed1);
    
               // $x = new clearText();
    
                //print_r($x->clearVar($result));
                //print_r($x->clearVar($result2));
                $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                $printer = new Printer($connector);
                //$printer -> text("\n\n\x20\n\x20\n\x20\n\x20\n\n\n"); // \n = 0.4cm
                $printer -> text($billdate);
                $printer -> text(date("d/m/Y" , strtotime($bdate)) . "\n");
                $printer -> text($billname);
                $printer -> text($brn."\n"); // receipt number
                /*
                $printer -> text($billname);
                $printer -> text("Rosalind". "\n"); // patientname
  $printer -> text($billadd1);
  $printer -> text("addressline1" ."\n"); // date
  $printer -> text($billadd2);
  $printer -> text("addressline2"."\n"); //r/n
  $printer -> text($billadd3);
  $printer -> text("addressline3"); // time
  $printer -> text($blankmiddle);
  $printer -> text($billmasuk);
  $printer -> text("Caj Duduk Wad  (Tarikh Masuk  : "."05/03/2022"." )"."\n"); //tarikh masuk
  $printer -> text($billkeluar);
  $printer -> text("(Tarikh Keluar : "."20.03.2022"." )"."\n\n\n"); // tarikh keluar
  $printer -> text($billmasuk);
  $printer -> text("Kelas  "."3"." :  "."15"." Hari"); // class and day 
  $printer -> text($billkelas);
  $printer -> text("3.00". "      ". "45.00"."\n\n"); // kelas price and total price for class
  $printer -> text($fixfront);
  $printer -> text("Caj Pemeriksaan/Ujian Makmal \n"); // 
  $printer -> text($fixfront);
  $printer -> text("-----------------------------"."\n");
  $printer -> text($billkumpulan);
  $printer -> text("1G"."    "."UJIAN MAKMAL KUMPULAN G"."        x"."  "."64"."           "."128.00\n");
  $printer -> text($billkumpulan);
  $printer -> text("1D"."    "."UJIAN MAKMAL KUMPULAN D"."        x"."  "."1"."             "."10.00\n");
  $printer -> text($billkumpulan);
  $printer -> text("15D"."    "."PEMBEDAHAN D"."                  x"."  "."1"."             "."20.00\n\n");
  //$printer -> text("1G"."    ".mb_strimwidth($description,0, 25) ."      x"."  "."days".""); // need restrict length, might need loop
  $printer -> text($fixfront);
  $printer -> text("Caj Rawatan Harian\n");
  $printer -> text($fixfront);
  $printer -> text("------------------");
  $printer -> text(str_repeat("\x20" , 46)."0.00"."\n");
  $printer -> text(str_repeat("\x20" , 67)."----------\n");
  $printer -> text(str_repeat("\x20" , 70)."158.00"."\n\n\n\n");
  $printer -> text($fixfront);
  $printer -> text("Tolak Cagaran "."A1372993");
  $printer -> text(str_repeat("\x20" , 41)."30.00");
  $printer -> text("\n\x20\n\x20\n\x20\n\x20\n");
  $printer -> text(str_repeat("\x20" , 28)."JUMLAH YANG PERLU DIBAYAR ==>");
  $printer -> text(str_repeat("\x20" , 13)."128.00");
  $printer -> text("\n\n\n\n\n");
    */
               // $printer -> cut();
                $printer -> close(); 
    
                $model->bill_print_datetime =  $date->format('Y-m-d H:i');
                $model->bill_uid = Yii::$app->request->get('bill_uid');
                $cookies = Yii::$app->request->cookies;
                 $model->bill_print_responsible_uid = $cookies->getValue('cookie_login');
                $model->save();
    
                return Yii::$app->getResponse()->redirect(array('/bill/print', 
                  'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'p'));

            }
          
           
>>>>>>> Stashed changes
        }

        return $this->render('generate', [
            'model' => $model,
<<<<<<< Updated upstream
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
            'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
        ]);
=======
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
        ]); 
>>>>>>> Stashed changes
    }

    /**
     * Deletes an existing Bill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $bill_uid Bill Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($bill_uid)
    {
        $this->findModel($bill_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $bill_uid Bill Uid
     * @return Bill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($bill_uid)
    {
        if (($model = Bill::findOne(['bill_uid' => $bill_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
