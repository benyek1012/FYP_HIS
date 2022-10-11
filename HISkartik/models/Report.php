<?php

namespace app\models;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use Yii;
use yii\data\ArrayDataProvider;

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

    public static function get_report1_query($senarai_pada)
    {
        // first day of the current year
        $start_date = date('Y-m-d', strtotime('first day of january this year'));
        //Add one day
        $time = strtotime($senarai_pada. ' + 1 days');
        $end_date = date('Y-m-d',$time);

        $result = \yii::$app->db->createCommand("CALL report1_query(:startDate, :endDate)")
        ->bindValue(':startDate' , $start_date)
        ->bindValue(':endDate' , $end_date)
        ->queryAll();

        if(!empty($result))
        return $result;

    }

    public static function export_csv_report1($senarai_pada){
       $query =  (new Report()) ->get_report1_query($senarai_pada);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
        ]);

        if($query != NULL)
        {
            $exporter = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'label' => 'Bil',
                        'value' => function($model, $index, $dataColumn) {
                           return is_null($model['receipt_uid']) ? null : $dataColumn + 1;
                        }
                    ],
                    [
                        'label' => 'Name Pendeposit',
                        'value' => function($model, $index, $dataColumn) {
                            $model_rn = Patient_admission::findOne(['rn' => $model['rn']]);
                            $model_patient = Patient_information::findOne(['patient_uid' => $model_rn['patient_uid']]);
                            if(!empty($model_patient->name))
                            {
                              return is_null($model['receipt_uid']) ? null : $model_patient->name;
                            }
                            else return NULL;
                        }
                    ],
                    [
                        'attribute' => 'receipt_serial_number',
                        'label' => 'No Rujukan',
                        'value' => function($model) {
                            return is_null($model['receipt_uid']) ? null : $model['receipt_serial_number'];
                        }
                    ],
                    [
                        'attribute' => 'receipt_content_datetime_paid',
                        'label' => 'Tarikh Deposit',
                        'value' => function($model) {
                            return is_null($model['receipt_uid']) ? null : $model['receipt_content_datetime_paid'];
                        }
                    ],
                    [
                        'attribute' => 'receipt_content_sum',
                        'label' => 'Baki / Amaun (RM)',
                        'value' => function($model) {
                            return is_null($model['receipt_uid']) ? null : $model['receipt_content_sum'];
                        }
                    ],
                    [
                        'attribute' => 'kod_akaun',
                        'label' => 'Kod Akaun',
                        'value' => function($model) {
                            return is_null($model['receipt_uid']) ? null : $model['kod_akaun'];
                        }
                    ],

                ],
            ]);
            $filename = 'report_senarai_baki_pendeposit.csv'; 
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
            $filename ='report_serahan_wang_kutipan.csv'; 
            return $exporter->export()->send($filename);
        }
    }
}