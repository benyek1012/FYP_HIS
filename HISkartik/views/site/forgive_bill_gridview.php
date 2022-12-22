<?php

use app\controllers\BillController;
use app\controllers\Patient_informationController;
use app\controllers\ReceiptController;

?>

<?= kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [ 'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' =>
            function($model) {
                return ['value' => $model->rn, 'class' => 'checkbox-row', 'id' => 'checkbox'];
            }
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
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->nric;
            },
        ], 
        [
            'attribute' =>  'rn',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
        ], 
        [
            'attribute' =>  'bill_generation_final_fee_rm',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_generation_final_fee_rm;
            },
        ],
        [
            'attribute' =>  'class',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->class;
            },
        ],
        [
            'attribute' =>  'initial_ward_code',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            
        ],
        [
            'attribute' =>  'receipt_content_description',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new ReceiptController(null,null)) -> findModelByRn($data->rn))->receipt_content_description;
            },
        ],
        [
            'attribute' =>  'bill_print_id',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_print_id;
            },
        ],
        [
            'attribute' =>  'bill_print_datetime',
            'headerOptions'=>['style'=>'max-width: 100px;'],
            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            'value' => function($data){
                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_print_datetime;
            },
        ],
    ]
    ]);
?>