<?php

namespace app\controllers;
require 'vendor/autoload.php';

use app\models\Bill_content_receipt;
use app\models\Bill;
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
        // $dataProvider1 = new ActiveDataProvider([
        //     'query'=> Receipt::find()->where(['rn'=> Yii::$app->request->get('rn')])
        //     ->orderBy(['receipt_content_datetime_paid' => SORT_DESC]),
        //     'pagination'=>['pageSize'=>5],
        // ]);
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

            // $model_receipt = Receipt::findOne(['rn' => Yii::$app->request->get('rn'), 'receipt_type' => 'bill']);

            if($model->validate() && $model->save()){
                // if(!empty($model_bill))
                // {
                    // $model_found_duplicate = Bill_content_receipt::findOne(['bill_uid' => $model_bill->bill_uid]);
                    // if(empty($model_found_duplicate) && $model->receipt_type == 'bill')
                    // {
                    //     $model_bill_receipt = new Bill_content_receipt();
                    //     $model_bill_receipt->bill_content_receipt_uid = Base64UID::generate(32);
                    //     $model_bill_receipt->bill_uid = $model_bill->bill_uid;
                    //     $model_bill_receipt->rn = $model_bill->rn;
                    //     $model_bill_receipt->bill_generation_billable_sum_rm = $model_bill->bill_generation_billable_sum_rm;
                    //     $model_bill_receipt->save();
                    // }

                    $modeladmission = Patient_admission::findOne(['rn' => yii::$app->request->get('rn')]) ;
                    $modelpatient = Patient_information::findOne(['patient_uid' => $modeladmission->patient_uid]);
    
                    if($model->receipt_type !='refund')
                    {
                   
                      $nocagaran = " ";

               $blankfront = str_repeat("\x20", 15); // adds 14 spaces
                $fixbackblank = str_repeat("\x20", 33);
                $fixbackblank2 = str_repeat("\x20", 31);
                $fixbackblank3 = str_repeat("\x20", 32);
                $entrydate = date("d/m/Y" , strtotime($model->receipt_content_datetime_paid));
                $entrytime =date("H:i" , strtotime($model->receipt_content_datetime_paid));

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
                                    [14, $modelpatient->nric],
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
                                    [1, "\x20"],
                                    [1, " "],
                                ]
                            );
                            $form->printNewLine(1);
                                $form->printElementArray(
                                    [
                                        //line 6, cagaran and nama pembayar
                                        [15, "\x20"],
                                        [8, $nocagaran,true],
                                        [34, "\x20"],
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

                    return Yii::$app->getResponse()->redirect(array('/receipt/index', 
                    'rn' => $model->rn));
                   
                        }
                        else{
                            return Yii::$app->getResponse()->redirect(array('/receipt/index', 
                            'rn' => $model->rn));  
                        }
}       
   
              
                 
              }
            }
        // } 
        // else
        //  {
            $model->receipt_content_datetime_paid = date("Y-m-d H:i");
            $cookies = Yii::$app->request->cookies;
            $model->receipt_responsible = Yii::$app->user->identity->getId();
            $model->loadDefaultValues();
        // }

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
