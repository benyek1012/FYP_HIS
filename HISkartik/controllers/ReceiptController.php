<?php

namespace app\controllers;
require 'vendor/autoload.php';

use app\models\Bill_content_receipt;
use app\models\Bill;
use app\models\Cancellation;
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

use app\models\PrintForm;
use app\models\Serial;
use app\models\SerialNumber;
use yii\helpers\Json;

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

    public function actionKod_akaun($type, $rn){
        $kod_akaun = (new Receipt) -> verifyKodAkaun($type, $rn);
        
        echo Json::encode($kod_akaun);
    }

    public function actionCancellation()
    {
        $model_cancellation = new Cancellation();
        $model = new Receipt();
        $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn')]);

        $searchModel = new ReceiptSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT

        if ($this->request->isPost && $model->load($this->request->post()) && $model_cancellation->load($this->request->post())) {
            if($model_cancellation->checkbox_replacement == true){
                $model_cancellation->responsible_uid = Yii::$app->user->identity->getId();
                $model_cancellation->replacement_uid = null;
                $model_cancellation->deleted_datetime =  $date->format('Y-m-d H:i:s');

                if($model_cancellation->validate()){
                    $model_cancellation->save();

                    return Yii::$app->getResponse()->redirect(array('/receipt/index', 
                    'rn' => $model->rn));
                }
                else{
                    Yii::$app->session->setFlash('cancellation_error', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Cancellation Fail!').'</strong>'.'</div>'
                    );
                }
            }
            else{
                if(Yii::$app->request->get('outside') == 'false'){
                    // if(empty($model->receipt_content_datetime_paid))
                    // {
                        $model->receipt_content_datetime_paid =  $date->format('Y-m-d H:i:s');
                    // }
                }
                else{
                    $model->receipt_serial_number = null;
                }
                
                $model_cancellation->responsible_uid = Yii::$app->user->identity->getId();
                $model_cancellation->replacement_uid = $model->receipt_uid;
                $model_cancellation->deleted_datetime =  $date->format('Y-m-d H:i:s');
                
                if($model->validate() && $model_cancellation->validate()){

                    if($model->receipt_type == 'bill' || $model->receipt_type == 'deposit' || $model->receipt_type == 'other')
                    {
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
                    }

                    $modeladmission = Patient_admission::findOne(['rn' => yii::$app->request->get('rn')]) ;
                    $modelpatient = Patient_information::findOne(['patient_uid' => $modeladmission->patient_uid]);

                    // Print Bill / Deposit 
                    if(Yii::$app->request->get('outside') == 'false'){
                        if(($model->receipt_type !='refund') && ($model->receipt_type !='exception'))
                        {
                            $error = PrintForm::printReceipt($model, $modelpatient);
                            if(!empty($error))
                            {
                                Yii::$app->session->setFlash('msg', '
                                <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
                            }
                        }
                    }

                    $model->save();
                    $model_cancellation->save();
                    
                    return Yii::$app->getResponse()->redirect(array('/receipt/index', 
                    'rn' => $model->rn));
                }
                
                else
                {
                    Yii::$app->session->setFlash('cancellation_error', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Cancellation Fail!').'</strong>'.'</div>'
                    );
                }
            }
        }
        else
        {
            $model->receipt_content_datetime_paid = date("Y-m-d H:i:s");
            $model->receipt_responsible = Yii::$app->user->identity->getId();
         
            $model->receipt_serial_number = SerialNumber::getSerialNumber("receipt");

            $model->loadDefaultValues();
        }

        return $this->render('index', [
            'model' => $model,
            'cancellation' => false,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRefresh()
    {
        return SerialNumber::getSerialNumber("receipt");
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

        // $dataProvider = $searchModel->transactionRecords($this->request->queryParams);

        // Print all record from customer
        $searchModel = new ReceiptSearch();
        if ($this->request->isPost && $searchModel->load($this->request->post()))
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
            // echo "<pre>";
            // var_dump($query);
            // exit();
            // echo "</pre>";
        }
        return $this->render('record', [
            'searchModel' => $searchModel,
            // 'dataProvider' => $dataProvider,
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
            if(Yii::$app->request->get('outside') == 'false'){
                // if(empty($model->receipt_content_datetime_paid))
                // {
                    $date = new \DateTime();
                    $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                    $model->receipt_content_datetime_paid =  $date->format('Y-m-d H:i:s');
                // }
            }
            else{
                $model->receipt_serial_number = null;
            }

            if($model->validate() && $model->save()){
                if($model->receipt_type == 'bill' || $model->receipt_type == 'deposit' || $model->receipt_type == 'other')
                {
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
                }

                $modeladmission = Patient_admission::findOne(['rn' => yii::$app->request->get('rn')]) ;
                $modelpatient = Patient_information::findOne(['patient_uid' => $modeladmission->patient_uid]);

                // Print Bill / Deposit 
                if(Yii::$app->request->get('outside') == 'false'){
                    if(($model->receipt_type !='refund') && ($model->receipt_type !='exception'))
                    {
                        $error = PrintForm::printReceipt($model, $modelpatient);
                        if(!empty($error))
                        {
                            Yii::$app->session->setFlash('msg', '
                            <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
                        }
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
            'cancellation' => false,
            'model_bill' => null,
            'index' => 0,
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
    public function findModelByRn($rn)
    {
        if (($model = Receipt::findOne(['rn' => $rn])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}