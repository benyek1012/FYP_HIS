<?php

use app\controllers\BillController;
use app\controllers\Patient_informationController;
use app\controllers\ReceiptController;
use app\models\Bill;
use app\models\Patient_admission;
use yii\bootstrap4\Html;

?>

<?= kartik\grid\GridView::widget([
    //'pjax' => true,
    'dataProvider' => $dataProvider,
    'columns' => [
        [   
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' =>
            function($model) {
                return ['value' => $model->rn, 'class' => 'checkbox-row', 'id' => 'checkbox'];
            },
            'visible' => ($check != 'false') ? true : false
        ],
        [   
            'class' => 'yii\grid\SerialColumn', 
            'visible' => ($check == 'false') ? true : false
        ],
        [
            'attribute' =>  'name',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->name;
            },
        ], 
        [
            'attribute' =>  'nric',
            'format' => 'raw',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                $ic = ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->nric;
                return  Html::a($ic, \yii\helpers\Url::to(['/site/admission', 'id' => $data['patient_uid'], '#' => 'patient']));
            },
            'label' => Yii::t('app','NRIC/Passport')
        ], 
        [
            'attribute' =>  'rn',
            'format' => 'raw',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
            },
        ], 
        [
            'label' => Yii::t('app','Initial Ward'),
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value'=>function ($data) {
                return $data['initial_ward_code']. ' ( ' . ((new BillController(null,null)) -> findModelByRn($data->rn))->class . ' ) ';
            },
        ],
        [
            'attribute' =>  'receipt_content_description',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new ReceiptController(null,null)) -> findModelByRn($data->rn))->receipt_content_description;
            },
            'label' => Yii::t('app','Receipt Description')
        ],
        [
            'attribute' =>  'bill_print_id',
            'format' => 'raw',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                if((new Bill())->isPrinted($data->rn))
                {
                    $bill_print_id = ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_print_id;
                    $bill_uid = ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_uid;
                    return Html::a($bill_print_id, \yii\helpers\Url::to(['/bill/print','bill_uid' => $bill_uid, 'rn' => $data['rn']]));
                }       
            },
            'label' => Yii::t('app','Receipt Bill ID')
        ],
        [
            'attribute' =>  'bill_print_datetime',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            "format"=>"raw",
            'value' => function($data){
                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_print_datetime;
            },
            'label' => Yii::t('app','Bill Print Datetime'),
        ],
        [
            'label' => Yii::t('app','Billable Total').' (RM)',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  (new Patient_admission()) -> get_billable_sum($data->rn);
            },
        ],
        [
            'label' => Yii::t('app','Amount Due').' / '.Yii::t('app','Unclaimed Balance').' (RM)',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return (new Patient_admission()) ->get_bill($data->rn);
            },
        ],
    ]
]);

?>