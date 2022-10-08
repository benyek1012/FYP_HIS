<?php

use kartik\grid\GridView;
use app\models\Cancellation;
use app\models\Patient_admission;
use app\models\Patient_information;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_admissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="patient-admission-index">

    <?php if(Yii::$app->session->hasFlash('cancellation_error')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('cancellation_error') ?>
        </div>
    <?php endif; ?>
    <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> 
        <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Patient admission record is not founded'),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'rn',
                'format' => 'raw',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data, $key, $index){ 
                    // $model_admission = Patient_admission::findOne(['rn' => $data->rn]);

                    // if(!empty($model_admission)){
                    //     $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_admission->rn]);

                    //     if(!empty($model_cancellation)){
                    //         return $data['rn'];
                    //     }
                    //     else{
                    //         return ($index == 0) ?
                    //         '<b>'.Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']])).'</b>'
                    //         :  Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                    //     }
                    // }

                    return ($index == 0) ?
                    '<b>'.Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']])).'</b>'
                    :  Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                },
            ],
            [
                'attribute' => 'entry_datetime',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $date = new DateTime($data['entry_datetime']);
                    $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                        // title
                        'title' => $date->format('Y-m-d H:i A') ,
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;'
                    ] );
                    return $tag;
                },
            ],
            [
                'attribute' => 'initial_ward_code',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            ],
            [
                'attribute' => 'initial_ward_class',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            ],
            [
                'attribute' =>  'reference',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            ],
            // [
            //     'attribute' =>  'medical_legal_code',
            //     'headerOptions'=>['style'=>'max-width: 100px;'],
            //     'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            // ],
            // [
            //     'attribute' =>  'reminder_given',
            //     'headerOptions'=>['style'=>'max-width: 100px;'],
            //     'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            // ],
            [
                'attribute' =>  'guarantor_name',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            ], 
            [
                'attribute' => 'billable_sum',
                'label' => Yii::t('app','Billable Total').' (RM)',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value' => function($data){
                    return  (new Patient_admission()) -> get_billable_sum($data->rn);
                },
            ],
            [
                'attribute' => 'final_fee',
                'label' => Yii::t('app','Amount Due').' / '.Yii::t('app','Unclaimed Balance').' (RM)',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value' => function($data){
                    return (new Patient_admission()) ->get_bill($data->rn);
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{my_button}', 
                'header' => 'Print X',
                
                'buttons'=>[
                    'my_button'=>function ($url, $data) {
                        $t = '#';
                        return Html::a(Yii::t('app', 'Registration Form'), \yii\helpers\Url::to(['/patient_admission/print', 'rn' => $data['rn']]), ['class'=>"btn btn-success"])."<br/>"."<br/>".
                        Html::a(Yii::t('app', 'Charge Sheet'), \yii\helpers\Url::to(['/patient_admission/print', 'rn' => $data['rn']]), ['class'=>"btn btn-success"])."<br/>"."<br/>".
                        Html::a(Yii::t('app', 'Case History Sheet'), \yii\helpers\Url::to(['/patient_admission/print', 'rn' => $data['rn']]), ['class'=>"btn btn-success"])."<br/>"."<br/>".
                        Html::a(Yii::t('app', 'Sticker'), \yii\helpers\Url::to(['/patient_admission/print', 'rn' => $data['rn']]), ['class'=>"btn btn-success"]) ;
                    },
                   
                ],
                
                
            ],
        ],
    ]);
    } 
    else{?> 
        <?= kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'showOnEmpty' => false,
            'emptyText' => Yii::t('app','Patient admission record is not founded'),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                // [
                //     'class' => 'kartik\grid\ExpandRowColumn',
                //     'expandTitle' => Yii::t('app', 'Cancellation'),
                //     'collapseTitle' => Yii::t('app', 'Cancellation'),
                //     'expandAllTitle' => Yii::t('app', 'Cancellation'),
                //     'collapseAllTitle' => Yii::t('app', 'Cancellation'),
                //     'expandIcon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                //         <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                //         <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                //         </svg>',
                //     'collapseIcon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                //         <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                //         <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                //         </svg>',
                //     'value' => function($model, $key, $index, $column) {
                //         return GridView::ROW_COLLAPSED;
                //     },
                //     'detailRowCssClass' => GridView::TYPE_LIGHT,
                //     'disabled' => function($data) {
                //         $model_admission = Patient_admission::findOne(['rn' => $data->rn]);

                //         if(!empty($model_admission)){
                //             $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_admission->rn]);

                //             // Receipt Cancelled
                //             if(!empty($model_cancellation)){
                //                 return true;
                //             }
                //             // else if(!empty($model_bill) && $model_receipt->receipt_type != 'bill'){
                //             //     return true;
                //             // }
                //             // else if(!empty($model_bill_del) && $model_receipt->receipt_type == 'bill'){
                //             //     foreach($model_bill_del as $bill){
                //             //         if($bill->bill_print_id == $model_receipt->receipt_content_bill_id){
                //             //             return true;
                //             //         }
                //             //     }
                //             // }
                //             else{
                //                 return false;
                //             }
                //         }
                //     },
                //     'detail' => function($data, $index) {
                //         $model_admission = Patient_admission::findOne(['rn' => $data->rn]);
                        
                //         if(!empty($model_admission)){
                //             $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_admission->rn]);

                //             if(!empty($model_cancellation)){
                //                 return null;
                //             }
                //             else{                
                //                 $model_cancellation = new Cancellation();
                //                 return Yii::$app->controller->renderPartial('//cancellation/create', [
                //                     'model_admission' => $model_admission,
                //                     'model_cancellation' => $model_cancellation,
                //                     'type' => 'admission',
                //                 ]);

                //                 // $modelpatient = Patient_information::findOne(['patient_uid' => $model_admission->patient_uid]);
                //                 // return $this->render('update', [
                //                 //     'model' => $model_admission,
                //                 //     'modelpatient' => $modelpatient
                //                 // ]);
                //             }
                //         }
                //     },
                // ],
                [
                    'attribute' => 'rn',
                    'format' => 'raw',
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                    'value'=>function ($data, $key, $index){ 
                        // $model_admission = Patient_admission::findOne(['rn' => $data->rn]);

                        // if(!empty($model_admission)){
                        //     $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_admission->rn]);

                        //     if(!empty($model_cancellation)){
                        //         return $data['rn'];
                        //     }
                        //     else{
                        //         return ($index == 0) ?
                        //         '<b>'.Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']])).'</b>'
                        //         :  Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                        //     }
                        // }

                        return ($index == 0) ?
                        '<b>'.Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']])).'</b>'
                        :  Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                    },
                ],
                [
                    'attribute' => 'entry_datetime',
                    "format"=>"raw",
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                    'value'=>function ($data) {
                        $date = new DateTime($data['entry_datetime']);
                        $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                            // title
                            'title' => $date->format('Y-m-d H:i A') ,
                            'data-placement' => 'top' ,
                            'data-toggle'=>'tooltip',
                            'style' => 'white-space:pre;'
                        ] );
                        return $tag;
                    },
                ],
                [
                    'attribute' => 'initial_ward_code',
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                ],
                [
                    'attribute' => 'initial_ward_class',
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                ],
                [
                    'attribute' =>  'reference',
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                ],
                // [
                //     'attribute' =>  'medical_legal_code',
                //     'headerOptions'=>['style'=>'max-width: 100px;'],
                //     'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // ],
                // [
                //     'attribute' =>  'reminder_given',
                //     'headerOptions'=>['style'=>'max-width: 100px;'],
                //     'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // ],
                [
                    'attribute' =>  'guarantor_name',
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                ], 
                [
                    'attribute' => 'billable_sum',
                    'label' => Yii::t('app','Billable Total').' (RM)',
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                    'value' => function($data){
                        return  (new Patient_admission()) -> get_billable_sum($data->rn);
                    },
                ],
                [
                    'attribute' => 'final_fee',
                    'label' => Yii::t('app','Amount Due').' / '.Yii::t('app','Unclaimed Balance').' (RM)',
                    'headerOptions'=>['style'=>'max-width: 100px;'],
                    'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                    'value' => function($data){
                        return (new Patient_admission()) ->get_bill($data->rn);
                    },
                ],

                // [
                //     'attribute' => 'Type',
                //     "format"=>"raw",
                //     'headerOptions'=>['style'=>'max-width: 100px;'],
                //     'contentOptions'=>['style'=>'max-width: 120px;vertical-align:middle'],
                //     'value'=>function ($data) {
                //         $model_admission = Patient_admission::findOne(['rn' => $data->rn]);

                //         if(!empty($model_admission)){
                //             $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_admission->rn]);

                //             foreach($model_cancellation as $model_cancellation){
                //                 $model_new_admission = Patient_admission::findOne(['rn' => $model_cancellation->replacement_uid]);
                //             }

                //             if(!empty($model_cancellation)){
                //                 $type = 'Admission Cancelled';
                //                 $title = $model_new_admission->rn;
                //                 $class = 'badge-danger';
                //             }
                //             else{
                //                 $type = 'Admission';
                //                 $title = '';
                //                 $class = 'badge-success';
                //             }
                //         }

                //         $tag = Html::tag('span', Yii::t('app', $type) , [
                //             'class' => 'badge ' . $class,
                //             'title' => $title,
                //             'data-placement' => 'top' ,
                //             'data-toggle'=>'tooltip',
                //             'style' => 'white-space:pre;'
                //         ]);
                //         return $tag;

                //     },
                //     'label' => Yii::t('app','Type')
                // ],
            ],
        ]);
    }
?>
</div>

<?php
$js = <<< SCRIPT
/* To initialize BS3 tooltips set this below */
$(function () { 
    $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
        html:true
    });
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs ( $js );
?>