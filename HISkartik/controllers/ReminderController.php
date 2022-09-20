<?php

namespace app\controllers;

use app\models\Bill;
use app\models\Reminder;
use app\models\New_user;
use app\models\ReminderSearch;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Reminder_pdf;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * ReminderController implements the CRUD actions for Reminder model.
 */
class ReminderController extends Controller
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
     * Lists all Reminder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        Reminder::createPlaceholderIfNotExists();
        $error = NULL;

        if ($this->request->get() && !empty($_GET['function'])) {

            //$model = new Reminder();
            $userid = Yii::$app->user->identity->id;
            if ($_GET['function'] == 'getReminderCalculate')
                Reminder::getReminderCalculate($userid);
            if ($_GET['function'] == 'batchCreate')
            {
                try{
                    Reminder::batchCreate($userid);
                } catch(Exception $e){
                    $error = $e->getMessage();
                    Yii::$app->session->setFlash('error_user', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    '.$error.'</div>');
                }
            }
            if ($_GET['function'] == 'downloadcsv')
            {
                $this->exportCSV($_GET['batch_date']);
            }
            if ($_GET['function'] == 'exportPdf')
            {
                $this->exportPDF($_GET['batch_date']);
            }
            if ($_GET['function'] == 'print')
            {
                $this->print($_GET['batch_date']);
            }
                
            //echo $userid;
            //echo $table;
            //$this->$username::getId($table);
            //return $username->_toString($table);
            //return $_GET['function'];
            //return $this->$table->redirect(['index']);
        }

        
        $searchModel = new ReminderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Reminder model.
     * @param string $batch_date Batch Date
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($batch_date)
    {
        return $this->render('view', [
            'model' => $this->findModel($batch_date),
        ]);
    }

    /**
     * Creates a new Reminder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Reminder();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'batch_date' => $model->batch_date]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Reminder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $batch_date Batch Date
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($batch_date)
    {
        $model = $this->findModel($batch_date);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'batch_date' => $model->batch_date]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Reminder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $batch_date Batch Date
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($batch_date)
    {
        $this->findModel($batch_date)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Reminder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $batch_date Batch Date
     * @return Reminder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($batch_date)
    {
        if (($model = Reminder::findOne(['batch_date' => $batch_date])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function exportCSV($batch_date) //Teo fill export CSV code here
    {
        // to be filled in RN, 
	// IC, 
	// RACE, 
	// ADDRESS,
	// GUARANTOR IC,
	// GUARANTOR ADDRESS,
	// ENTRY DATE, 
	// Reminder Number,
	// Reminder Batch Date, 
	// Bill.Final Ward Date as 'Discharge Date', 
	// Discharge Date + 14/28/42 as Reminder Date,  
	// Bill.payable_fee, 
	// Bill.payable_fee 
	// 	- SUM(Receipt Amount where receipt_type <> Refund && receipt_date < Reminder Date) 
	// 	+ SUM(Receipt Amount where receipt_type <> Refund && receipt_date < Reminder Date)
	// 	as AMOUNT PAYABLE
    
    //To be filtered rn that has value on reminder1,2,3; exist in batch_date
        $model = $this->findModel($batch_date);
        $query = $model->getReminderNumberRows($batch_date);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
        ]);

        if($query != NULL)
        {
            $exporter = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'rn',
                        'label' => 'RN',
                    ],
                    [
                        'attribute' => 'nric',
                        'label' => 'IC',
                    ],
                    [
                        'attribute' => 'race',
                        'label' => 'Race',
                    ],
                [
                        'attribute' => 'address1',
                        'label' => 'address1',
                    ],
                    [
                        'attribute' => 'address2',
                        'label' => 'address2',
                    ],
                    [
                        'attribute' => 'address3',
                        'label' => 'address3',
                    ],
                    [
                        'attribute' => 'guarantor_nric',
                        'label' => 'Guarantor Ic',
                        'format' =>'text',
                    ],
                    [
                        'attribute' => 'guarantor_address1',
                        'label' => 'Guarantor Address 1',
                    ],
                    [
                        'attribute' => 'guarantor_address2',
                        'label' => 'Guarantor Address 2',
                    ],
                    [
                        'attribute' => 'guarantor_addres3',
                        'label' => 'Guarantor Address 3',
                    ],
                    [
                        'attribute' => 'entry_datetime',
                        'label' => 'Entry Datetime',
                    ],
                    [
                        'attribute' => 'Reminder Number',
                        'value' => function($model, $index, $dataColumn) {
                            if(!empty($model['Reminder Number']))
                            {
                                if($model['Reminder Number'] == 'reminder1') return '1';
                                else if($model['Reminder Number'] == 'reminder2') return '2';
                                else if($model['Reminder Number'] == 'reminder3') return '3';
                            }
                            else return NULL;
                        }
                    ],
                    [
                        'attribute' => 'Batch date',
                    ],
                    [
                        'attribute' => 'Discharge Date',
                    ],
                    [
                        'attribute' => '',
                        'label' => 'Reminder Date',
                        'value' => function($model, $index, $dataColumn) {
                            return (new Reminder())->getReminderDate($model['Reminder Number'], $model['Discharge Date']);
                        }
                    ],
                    [
                        'attribute' => 'Billable Fee',
                    ],
                
                    [
                    // 'attribute' => 'bills.bill_generation_final_fee_rm',

                        'label' => 'Amount Due',
                        'value' => function($model, $index, $dataColumn) {
                            $remindate = ((new Reminder()) -> getReminderDate($model['Reminder Number'], $model['Discharge Date'])); 

                            return ((new Reminder()) -> calculateAmountdue($model['rn'],$model['Billable Fee'],$remindate));
                        }

                    ],


                ],
            ]);
            $filename = $batch_date. '.csv'; 
            
            return $exporter->export()->send($filename);
        }
    }

    public function print($batch_date)
    {
        $model = $this->findModel($batch_date);
        $query = $model->getReminderNumberRows($batch_date);

        echo "<pre>";
        var_dump($query);
        exit;
        echo "</pre>";
    }

    public function exportPDF($batch_date)
    {
        $model = $this->findModel($batch_date);

        $pdf = new Reminder_pdf();   // create TCPDF object with default constructor args

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->setPrintFooter(false);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->setMargins(22, 22, 11.6);
        
        $query = $model->getReminderNumberRows($batch_date);

        if($query != NULL)
        {
            foreach ($query as $q)
            {           
                $reminder_date = (new Reminder())->getReminderDate($q['Reminder Number'], $q['Discharge Date']);
                $pdf->setData($reminder_date);
                $pdf->AddPage();
                $ic = $q['nric'];
                $name = (Patient_information::findOne(['nric' => $ic]))->name;
                $address1 = (Patient_information::findOne(['nric' => $ic]))->address1;
                $address2 = (Patient_information::findOne(['nric' => $ic]))->address2;
                $address3 = (Patient_information::findOne(['nric' => $ic]))->address3;
                $rn = $q['rn'];
                $datetime = date("Y-m-d", strtotime((Bill::findOne(['rn' => $rn]))->bill_generation_datetime));
                $remindate = ((new Reminder()) -> getReminderDate($q['Reminder Number'], $q['Discharge Date'])); 
                $amount_due = "RM ".((new Reminder()) -> calculateAmountdue($q['rn'],$q['Billable Fee'],$remindate));
                $amount = "RM ".$q['Billable Fee'];
                $bill_No = (Bill::findOne(['rn' => $rn]))->bill_print_id;
                $guarantor_name = (Patient_admission::findOne(['rn' => $rn]))->guarantor_name;
                $guarantor_address1 = (Patient_admission::findOne(['rn' => $rn]))->guarantor_address1;
                $guarantor_address2 = (Patient_admission::findOne(['rn' => $rn]))->guarantor_address2;
                $guarantor_address3 = (Patient_admission::findOne(['rn' => $rn]))->guarantor_address3;
                // reminder 1
                if($q['Reminder Number'] == 'reminder1'){
                    $pdf->content1($rn,$name,$datetime,$amount_due, $amount,$bill_No,$guarantor_name,$guarantor_address1,$guarantor_address2,$guarantor_address3,$address1,$address2,$address3);
                }
              
                // reminder 2
                if($q['Reminder Number']== 'reminder2'){
                    $pdf->content2($rn,$name,$datetime,$amount_due, $amount,$bill_No, $guarantor_name,$guarantor_address1,$guarantor_address2,$guarantor_address3,$address1,$address2,$address3);
                }
    
                // reminder 3
                if($q['Reminder Number'] == 'reminder3'){
                    $pdf->content3($rn,$name,$datetime,$amount_due, $amount,$bill_No, $guarantor_name,$guarantor_address1,$guarantor_address2,$guarantor_address3,$address1,$address2,$address3);
                }
                
            }

            $filename = $batch_date. '.pdf'; 
    
            //Close and output PDF document
            $pdf->Output($filename, 'D');
        }
    
       
    }
}