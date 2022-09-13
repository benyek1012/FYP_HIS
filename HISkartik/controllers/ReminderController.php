<?php

namespace app\controllers;

use app\models\Bill;
use app\models\Reminder;
use app\models\New_user;
use app\models\ReminderSearch;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Pdf;
use app\models\Pdf_html;
use Exception;
use FPDF as GlobalFPDF;
use Dompdf\Dompdf;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use Yii;

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
            'error' => $error,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*public function actionBatch($responsible_uid)
    {
        $model = $this->findModel($responsible_uid);

        $model->responsible_uid = Yii::$app->user->identity->responsible_uid;
         
        $model::batchCreate($responsible_uid);

        return $this->redirect(['index']);
    }*/

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

        

        $query = Patient_admission::find()
        ->select('patient_admission.*')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        ->joinWith('bill',true)
        ->joinWith('receipt',true)
        ->joinWith('reminder',true)
        ->where(['batch_date'=>$batch_date])
        ->groupBy(['rn']);

        // echo "<pre>";
        // var_dump($query->all());
        // exit;
        // echo "</pre>";
        
       
        // if(!empty($isreminder1))
        // {
        //     $isreminder1->modify('+14 day');
        // }
        // else
        // return '';


        $exporter = new CsvGrid([
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 100, // export batch size
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'rn',
                    'label' => 'RN',
                ],
                [
                    'attribute' => 'patientU.nric',
                    'label' => 'IC',
                ],
                [
                    'attribute' => 'patientU.race',
                    'label' => 'Race',
                ],
               [
                    'attribute' => 'patientU.address1',
                    'label' => 'address1',
                ],
                [
                    'attribute' => 'patientU.address2',
                    'label' => 'address2',
                ],
                [
                    'attribute' => 'patientU.address3',
                    'label' => 'address3',
                ],
                [
                    'attribute' => 'guarantor_nric',
                    'label' => 'Guarantor Ic',
                    'format' =>'text',
                ],
               //guarantor address?
                /*[
                    'attribute' => 'gurantor_address',
                    'label' => 'Guarantor Address',
                    'value' => function($model, $index, $dataColumn) {

                        return $model->gurantor_address;

                    },
                ],*/

                [
                    'attribute' => 'entry_datetime',
                    'label' => 'Entry Datetime',
                    'value' => function($model, $index, $dataColumn) {

                        return $model->entry_datetime;

                    },
                ],

                [
                    'attribute' => 'reminder1',
                    'label' => 'Reminder 1',
                    'value' => function($model, $index, $dataColumn) {

                        return $model->reminder1;
                    }
                ],
                [
                    'attribute' => 'reminder2',
                    'label' => 'Reminder 2',
                    'value' => function($model, $index, $dataColumn) {

                        return $model->reminder2;
                    }
                ],
                [
                    'attribute' => 'reminder3',
                    'label' => 'Reminder 3',
                    'value' => function($model, $index, $dataColumn) {

                        return $model->reminder3;
                    }
                ],
                [
                    'attribute' => 'bills.final_ward_datetime',
                    'label' => 'Discharge Date',

                    
                ],
                [
                    'attribute' => '',
                    'label' => 'Reminder Date1',
                    'value' => function($model, $index, $dataColumn) {
                        if(!empty($model->reminder1))
                        {
                            $remindate =  date_create_from_format('Y-m-d H:i:s',$model->bill->final_ward_datetime);

                           return date_add($remindate,date_interval_create_from_date_string("14 days"))->format('Y-m-d');
                           //return 'xx'. $model->bill->final_ward_datetime. ' xx';
                            //  return gettype($model->bill->final_ward_datetime);
                        }
                        else
                        return NULL;
                    }
                    
                ],
                [
                    'attribute' => 'bills.final_ward_datetime',
                    'label' => 'Reminder Date2',

                    
                ],
                [
                    'attribute' => 'bills.final_ward_datetime',
                    'label' => 'Reminder Date3',

                    
                ],

                [
                    'attribute' => 'bills.bill_generation_final_fee_rm',

                    'label' => 'Amount Owe',

                  
                ],


            ],
        ]);
        return $exporter->export()->send('items.csv');

        return $this->render('update', [
			'model' => $model,
		]);
    }

    public function print($batch_date)
    {
        $query = Patient_admission::find()
        ->select('patient_admission.*')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        ->joinWith('bill',true)
        ->joinWith('receipt',true)
        ->joinWith('reminder',true)
        ->where(['batch_date'=>$batch_date])
        ->groupBy(['rn']);

        echo "<pre>";
        var_dump($query->all());
        exit;
        echo "</pre>";
    }

    public function exportPDF($batch_date)
    {
        // $model = $this->findModel($batch_date);
        // $content = "Batch Date : ".preg_replace("<<br/>>","\r\n", $model->batch_date);
        // $filename = $batch_date. '.pdf'; 

        // $pdf = new Pdf_html();
        // $pdf->AliasNbPages();
        // // Add new pages
        // $pdf->AddPage();
        // $pdf->content_first_page();
        // $pdf->Output('D', $filename);

        // $query = Patient_admission::find()
        // ->select('patient_information.patient_uid')
        // ->from('patient_admission')
        // ->joinWith('patient_information',true)
        // ->joinWith('reminder',true)
        // ->where(['in','batch_date',$batch_date])
        // ->one();
      
        // $AmountDue = (new Patient_information())-> getBalanceRM($query);

        $model = $this->findModel($batch_date);
        $content = "Batch Date : ".preg_replace("<<br/>>","\r\n", $model->batch_date);
        $filename = $batch_date. '.pdf'; 
        $pdf = new Pdf_html();
        $pdf->AliasNbPages();
        $pdf->setMargins(22, 8, 11.6);
        // Add new pages
        $pdf->AddPage();
        $pdf->content1();
        $pdf->AddPage();
        $pdf->content2();
        $pdf->AddPage();
        $pdf->content3();
        $pdf->Output('D', $filename);
    }
}