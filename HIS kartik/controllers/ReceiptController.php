<?php

namespace app\controllers;
require 'vendor/autoload.php';

use app\models\Bill_content_receipt;
use app\models\Bill;
use app\models\New_user;
use GpsLab\Component\Base64UID\Base64UID;


use Yii;
use app\models\Receipt;
use app\models\ReceiptSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Patient_admission;
use yii\data\ActiveDataProvider;
use app\models\Patient_information;
use yii\helpers\ArrayHelper;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use app\models\PrintForm;
use app\models\Serial;
use app\models\SerialNumber;

/**
 * ReceiptController implements the CRUD actions for Receipt model.
 */
class ReceiptController extends Controller
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
     * Lists all Receipt models.
     *
     * @return string
     */
    public function actionIndex()
    {

        $searchModel = new ReceiptSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
    /**
     * Lists all Receipt models.
     *
     * @return string
     */
    public function actionRecord()
    {
        $searchModel = new ReceiptSearch();
        // $dataProvider1 = new ActiveDataProvider([
        //     'query'=> Receipt::find()->where(['rn'=> Yii::$app->request->get('rn')])
        //     ->orderBy(['receipt_content_datetime_paid' => SORT_DESC]),
        //     'pagination'=>['pageSize'=>5],
        // ]);
        $dataProvider = $searchModel->transactionRecords($this->request->queryParams);

        // Print all record from customer
        if ($this->request->isPost)
        {
             // This is showing all RN from payment 
            $model_adm = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
            $model_rn = Patient_admission::findAll(['patient_uid' => $model_adm->patient_uid]);
        
            $rn_array = array();
            foreach($model_rn as $model)
            {
                $rn_array[] = $model->rn;
            
            }

            $query = Receipt::find()->where(['rn' => $rn_array]);
            echo "<pre>";
            var_dump($query);
            exit();
            echo "</pre>";
        }
        return $this->render('record', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        
    }
    

    /**
     * Displays a single Receipt model.
     * @param string $receipt_uid Receipt Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($receipt_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($receipt_uid),
        ]);
    }

    /**
     * Creates a new Receipt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Receipt();
        $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn')]);

        if ($this->request->isPost && $model->load($this->request->post())) {
            if(empty($model->receipt_content_datetime_paid))
            {
                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                $model->receipt_content_datetime_paid =  $date->format('Y-m-d H:i');
            }

            if($model->validate() && $model->save()){

                if($model->receipt_serial_number != SerialNumber::getSerialNumber("receipt"))
                {
                    $model_serial = SerialNumber::findOne(['serial_name' => "receipt"]);

                    $str = $model->receipt_serial_number;
                    $only_integer = preg_replace('/[^0-9]/', '', $str);
                    $model_serial->prepend = preg_replace('/[^a-zA-Z]/', '', $str);
                    $model_serial->digit_length = strlen($only_integer);
                    $model_serial->running_value = $only_integer;

                    $model_serial->save();    
                }
                else{
                    $model_serial = SerialNumber::findOne(['serial_name' => "receipt"]);
                    $model_serial->running_value =  $model_serial->running_value + 1;
                    $model_serial->save();    
                }

                $modeladmission = Patient_admission::findOne(['rn' => yii::$app->request->get('rn')]) ;
                $modelpatient = Patient_information::findOne(['patient_uid' => $modeladmission->patient_uid]);

                // Print Bill / Deposit 
                if($model->receipt_type !='refund')
                {
                    $nocagaran = " ";
                    $blankfront = str_repeat("\x20", 15); // adds 14 spaces
                    $fixbackblank = str_repeat("\x20", 33);
                    $fixbackblank2 = str_repeat("\x20", 31);
                    $fixbackblank3 = str_repeat("\x20", 32);
                    $entrydate = date("d/m/Y" , strtotime($model->receipt_content_datetime_paid));
                    $entrytime =date("H:i" , strtotime($model->receipt_content_datetime_paid));
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

                    if (Yii::$app->params['printerstatus'] == "true"){
                        $form = new PrintForm(PrintForm::Receipt);
                        //$form = new PrintForm(PrintForm::BorangDaftarMasuk);
                        $form->printNewLine(6);
                        $form->printElementArray(
                                [
                                    //receiptline 1, receipt serial number, ic
                                    [15, "\x20"],
                                    [8, $model->receipt_serial_number,true],
                                    [33,"\x20"],
                                    [14, $printic],
                                ]
                            );
                        $form->printNewLine(1);
                        $form->printElementArray(
                                [
                                    //line2 , pay date, rn
                                    [15, "\x20"],
                                    [10, $entrydate],
                                    [33, "\x20"],
                                    [11, $model->rn],
                                    
                                   // [13, "\x20"], for status in the future
                                ]
                        );
                        $form->printNewLine(1);

                        $form->printElementArray(
                            [
                                //line 3, pay time, bil number
                                [15, "\x20"],
                                [5, $entrytime],
                                [36, "\x20"],
                                [8, $model->receipt_content_bill_id, true],

                            ]
                        );
                        $form->printNewLine(1);
                        $form->printElementArray(
                            [
                                //line 4 akaun, total 
                                [15, "\x20"],
                                [1, " "], // akaun, maybe phase 2?
                                [40, "\x20"],
                                [9, $model->receipt_content_sum],
                            ]
                        );
                        $form->printNewLine(1);
                        $form->printElementArray(
                            [
                                // line 5, OP mayb phase 2?
                                [15, "\x20"],
                                [1, " "],
                            ]
                        );
                        $form->printNewLine(1);
                            $form->printElementArray(
                                [
                                    //line 6, cagaran and nama pembayar
                                    [15, "\x20"],
                                    [8, $nocagaran,true],
                                    [33, "\x20"],
                                    [20, $model->receipt_content_payer_name,true],
                                ]
                            );
                        $form->printNewLine(2);
                        $form->printElementArray(
                            [
                                //line 7, patient name, payment method 25,15 42-8
                                [15, "\x20"],
                                [25, $modelpatient->name,true],
                                [16, "\x20"],
                                [17, $model->receipt_content_payment_method,true],
                            ]
                        );
                        $form->printNewLine(2);
                        $form->printElementArray(
                            [
                                //line 8, penjelasan, descripton
                                [7, "\x20"],
                                [13, "Penjelasan : "],
                                [50, $model->receipt_content_description,true],
                                
                            ]
                        );
                        $form -> close();              
                    }
                } 

                return Yii::$app->getResponse()->redirect(array('/receipt/index', 
                'rn' => $model->rn));
            }
        }
        else
        {
            $model->receipt_content_datetime_paid = date("Y-m-d H:i:s");
            $model->receipt_responsible = Yii::$app->user->identity->getId();
         
            $model->receipt_serial_number = SerialNumber::getSerialNumber("receipt");

            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Receipt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $receipt_uid Receipt Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($receipt_uid)
    {
        $model = $this->findModel($receipt_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return Yii::$app->getResponse()->redirect(array('/receipt/update', 
            'receipt_uid' => $model->receipt_uid));   
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Receipt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $receipt_uid Receipt Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($receipt_uid)
    {
        $this->findModel($receipt_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Receipt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $receipt_uid Receipt Uid
     * @return Receipt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($receipt_uid)
    {
        if (($model = Receipt::findOne(['receipt_uid' => $receipt_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
