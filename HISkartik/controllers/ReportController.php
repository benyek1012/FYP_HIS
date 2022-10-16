<?php

namespace app\controllers;

use app\models\Bill;
use app\models\Reminder;
use app\models\New_user;
use app\models\ReminderSearch;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Pekeliling_import;
use app\models\Pekeliling_importSearch;
use app\models\Receipt;
use app\models\Reminder_pdf;
use app\models\Report;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use Yii;
use yii\data\ArrayDataProvider;


class ReportController extends Controller
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

    public function actionReport1()
    {
        $model = new Report();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $senarai_pada = $model->date_report;
                    if(!empty($senarai_pada))
                    {
                        Report::export_csv_report1($senarai_pada);
                    }
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        } 

        return $this->render('report1', [
            'model' => $model,
        ]);
    }

    public function actionReport5()
    {
        $model = new Report();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $year = $model->year;
                    $month = $model->month;
                    Report::export_csv_report5($year, $month);
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        } 

        return $this->render('report5', [
            'model' => $model,
        ]);
    }

    public function actionReport7()
    {
        $model = new Report();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $year = $model->year;
                    $month = $model->month;
                    Report::export_csv_report7($year, $month);
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        }

        return $this->render('report7', [
            'model' => $model,
        ]);
    }
    public function actionReport8()
    {
        $model = new Report();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $year = $model->year;
                    $month = $model->month;
                    Report::export_csv_report8($year, $month);
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        }

        return $this->render('report8', [
            'model' => $model,
        ]);
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
                $status_kod = (Bill::findOne(['rn' => $rn])->status_description);
                // reminder 1
                if($q['Reminder Number'] == 'reminder1'){
                    $pdf->content1($rn,$name,$datetime,$amount_due, $amount,$bill_No,$guarantor_name,$guarantor_address1,$guarantor_address2,$guarantor_address3,$address1,$address2,$address3,$status_kod);
                }
              
                // reminder 2
                if($q['Reminder Number']== 'reminder2'){
                    $pdf->content2($rn,$name,$datetime,$amount_due, $amount,$bill_No, $guarantor_name,$guarantor_address1,$guarantor_address2,$guarantor_address3,$address1,$address2,$address3,$status_kod);
                }
    
                // reminder 3
                if($q['Reminder Number'] == 'reminder3'){
                    $pdf->content3($rn,$name,$datetime,$amount_due, $amount,$bill_No, $guarantor_name,$guarantor_address1,$guarantor_address2,$guarantor_address3,$address1,$address2,$address3,$status_kod);
                }
                
            }

            $filename = $batch_date. '.pdf'; 
    
            //Close and output PDF document
            $pdf->Output($filename, 'D');
        }
    
       
    }
}