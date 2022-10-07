<?php

namespace app\models;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use Yii;


class Report extends \yii\db\ActiveRecord{

    public $date_report;
    public $year;
    public $month;

    /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return [  
            [['date_report','year','month'], 'safe'],
        ];
    }

    public static function export_csv_report1($senarai_pada){
        //Add one day
        $time = strtotime($senarai_pada. ' + 1 days');
        $new_senarai_pada = date('Y-m-d',$time);

        $query = Receipt::find()
        ->where(['<=','receipt_content_datetime_paid',$new_senarai_pada])
        ->andWhere(['receipt_type' => 'deposit'])
        ->groupBy(['receipt_serial_number', 'receipt_content_datetime_paid']);
       
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if($query != NULL)
        {
            $exporter = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'label' => 'Bil',
                        'value' => function($model, $index, $dataColumn) {
                           return $dataColumn + 1;
                        }
                    ],
                    [
                        'label' => 'Name Pendeposit',
                        'value' => function($model, $index, $dataColumn) {
                            $model_rn = Patient_admission::findOne(['rn' => $model['rn']]);
                            $model_patient = Patient_information::findOne(['patient_uid' => $model_rn['patient_uid']]);
                            if(!empty($model_patient->name))
                            {
                              return $model_patient->name;
                            }
                            else return NULL;
                        }
                    ],
                    [
                        'attribute' => 'receipt_serial_number',
                        'label' => 'No Rujukan',
                    ],
                    [
                        'attribute' => 'receipt_content_datetime_paid',
                        'label' => 'Tarikh Deposit',
                    ],
                    [
                        'attribute' => 'receipt_content_sum',
                        'label' => 'Baki / Amaun (RM)',
                    ],

                ],
            ]);
            $filename = $senarai_pada. '_report.csv'; 
            return $exporter->export()->send($filename);
        }
    }

    public static function export_csv_report7($year, $month)
    {
        $query = Receipt::find()
        ->select('kod_akaun, COUNT(`receipt_serial_number`) as receipt_serial_number, SUM(`receipt_content_sum`) as receipt_content_sum')
        ->from('receipt')
        ->groupBy('kod_akaun')
        ->where(['EXTRACT(YEAR FROM receipt_content_datetime_paid)' => $year])
        ->andWhere(['EXTRACT(MONTH FROM receipt_content_datetime_paid)' => $month]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if($query != NULL)
        {
            $exporter = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'kod_akaun',
                        'label' => 'KOD HASI',
                    ],
                    [
                        'attribute' => 'receipt_serial_number',
                        'label' => 'BILANG RESIT',
                    ],
                    [
                        'attribute' => 'receipt_content_sum',
                        'label' => 'JUMLAH TERIMAAN(RM)',
                    ],
                ],
            ]);
            $filename ='1.csv'; 
            return $exporter->export()->send($filename);
        }
    }
  
}