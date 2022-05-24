<?php

namespace app\controllers;
require 'vendor/autoload.php';

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

        if ($this->request->isPost && $model->load($this->request->post())) {
            if(empty($model->receipt_content_datetime_paid))
            {
                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                $model->receipt_content_datetime_paid =  $date->format('Y-m-d H:i');
            }

            if($model->validate() && $model->save()){

                $modeladmission = Patient_admission::findOne(['rn' => yii::$app->request->get('rn')]) ;
                $modelpatient = Patient_information::findOne(['patient_uid' => $modeladmission->patient_uid]);

                $getresitno = ArrayHelper::toArray($model->receipt_serial_number);
                $getic = ArrayHelper::toArray($modelpatient->nric);
                $getpaymentdate = arrayHelper::toArray($model->receipt_content_datetime_paid);
                $getrn = arrayHelper::toArray($model->rn);
                $getbillno = arrayHelper::toArray($model->receipt_content_bill_id);
                $gettotal = arrayHelper::toArray($model->receipt_content_sum);
                $getpayername = arrayHelper::toArray($model->receipt_content_payer_name);
                $getname = ArrayHelper::toArray($modelpatient->name);
                $getpaymentmethod = arrayHelper::toArray($model->receipt_content_payment_method);
                $getreceiptcontent = arrayHelper::toArray($model->receipt_content_description);
                
                if($model->receipt_type !='refund')
              {
                $printresit = implode($getresitno);
                $printic = implode($getic);
                $printpaydatetime = implode($getpaymentdate);
                $printrn = implode($getrn);
                $printbil = implode($getbillno);
                $printtotal = implode($gettotal);
                $printpayername = implode($getpayername);
                $printpatientname = implode($getname);
                $printpaymentmethod = implode($getpaymentmethod);
                $printreceiptcontent = implode($getreceiptcontent);
                $nocagaran = " ";
            
               
    
               $blankfront = str_repeat("\x20", 15); // adds 14 spaces
                $fixbackblank = str_repeat("\x20", 33);
                $fixbackblank2 = str_repeat("\x20", 31);
                $fixbackblank3 = str_repeat("\x20", 32);
                $entrydate = date("d/m/Y" , strtotime($model->receipt_content_datetime_paid));
                $entrytime =date("H:i" , strtotime($model->receipt_content_datetime_paid));

                 if (Yii::$app->params['printerstatus'] == "true"){
                    $form = new PrintForm(PrintForm::Receipt);
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
                            if($model->receipt_type =='bill')
                            {
                                $form->printElementArray(
                                    [
                                        //line 6, cagaran and nama pembayar
                                        [15, "\x20"],
                                        [8, $model->receipt_serial_number,true],
                                        [33, "\x20"],
                                        [25, $model->receipt_content_payer_name,true],
                                    ]
                                );
                            }
                            else
                            {
                                $form->printElementArray(
                                    [
                                        //line 6, cagaran and nama pembayar
                                        [15, "\x20"],
                                        [8, $nocagaran,true],
                                        [34, "\x20"],
                                        [20, $model->receipt_content_payer_name,true],
                                    ]
                                );
                            }
                           
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
               
   
                //$connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/EPSON");
                // $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                // $printer = new Printer($connector);
                // $printer -> text("\n\n\x20\n\n\x20\n\n\n");
                // $printer -> text($blankfront); // space= 0.3cm， receipt column 1
                // $printer -> text($printresit); // receipt number
                // $printer -> text($fixbackblank); //receipt column 2
                // $printer -> text($printic."\n"); // no.K/P
                // $printer -> text($blankfront);

                // $printer -> text(date("d/m/Y", strtotime($printpaydatetime))."  ");
                // $printer -> text($fixbackblank2);
                // $printer -> text($printrn."\n"); // rn

                // $printer -> text($blankfront);
                // $printer -> text(date("H:i:s", strtotime($printpaydatetime)));
                // $printer -> text($fixbackblank);
                // $printer -> text($printbil."\n"); //no.Bil

                // $printer -> text($blankfront);
                // $printer -> text(" "); // Akaun
                // $printer -> text($fixbackblank3 ."        "); fixblank3 + 8
                // $printer -> text($printtotal."\n"); //total price

                // $printer -> text($blankfront);
                // $printer -> text("  \n"); // Op (example required)

                // $printer -> text($blankfront);
                // $printer -> text($nocagaran); // No.Cagaran
                // $printer -> text(str_repeat("\x20", 56 - 15 - strlen($nocagaran)));// fixbackblank
                // $printer -> text(mb_strimwidth(strtoupper($printpayername),0, 30)."\n\n"); // guarrantor name

                // $printer -> text($blankfront);
                // $blankback = str_repeat("\x20", 55 - 14 - strlen($printpatientname));
                // $printer -> text(strtoupper($printpatientname)); // patient name

                // $printer -> text($blankback);
                // $printer -> text(strtoupper($printpaymentmethod)."\n\n"); //Cara Bayaran
                // $printer -> text(str_repeat("\x20" , 7)."Penjelasan :");
                // $printer ->text(strtoupper($printreceiptcontent));
                
                
                // $printer -> close(); 
                
                // return Yii::$app->getResponse()->redirect(array('/receipt/index', 
                // 'rn' => $model->rn));   
                 
              }
            }
        } else {
            $model->receipt_content_datetime_paid = date("Y-m-d H:i");
            $cookies = Yii::$app->request->cookies;
            $model->receipt_responsible = $cookies->getValue('cookie_login');
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
