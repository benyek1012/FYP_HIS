<?php

use yii\bootstrap4\Dropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_next_of_kinSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$rows_patient_nok = (new \yii\db\Query())
->select('*')
->from('patient_next_of_kin')
->where(['patient_uid'=> Yii::$app->request->get('id')])
->all();

$rows_relationship = (new \yii\db\Query())
->select('*')
->from('lookup_general')
->where(['category'=> 'Relationship'])
->all();

$relationship = array();
foreach($rows_relationship as $row_relationship){
    $relationship[$row_relationship['code']] = $row_relationship['code'];
} 

foreach($rows_patient_nok as $row_patient_nok){
    if(empty($relationship[$row_patient_nok['nok_relationship']])){
        $relationship[$row_patient_nok['nok_relationship']] = $row_patient_nok['nok_relationship'];
    }            
}

?>
<div class="patient-next-of-kin-index">

    <?= kartik\grid\GridView::widget([
        'pjax' => true,
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                // data-key in gridview
                'data' => ['key' => $index],
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_name',
                'refreshGrid' => true,
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/site/nok']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_relationship',
                'refreshGrid' => true,
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' => [
                    'size' => 'md',
                    'inputType' => Editable::INPUT_SELECT2,
                    'asPopover' => false,
                    'options' => [
                        'data' => $relationship,
                        'class' => 'patient-nok-relationship', 
                        'pluginOptions' => [
                            'tags' => true,
                            'width' => '200px',
                        ],
                    ],
                    'formOptions' => ['action' => ['/site/nok']],
                ],
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_phone_number',
                'refreshGrid' => true,
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' => [
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/site/nok']],
                    ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_email',
                'refreshGrid' => true,
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' => function ($model) {
                    return [                
                        'asPopover' => false,
                        'formOptions' => ['action' => ['/site/nok']],
                    ];
                }
            ],   
            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_address1',
                'refreshGrid' => true,
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' => function ($model) {
                    return [                
                        'asPopover' => false,
                        'formOptions' => ['action' => ['/site/nok']],
                    ];
                }
            ],
            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_address2',
                'refreshGrid' => true,
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' => function ($model) {
                    return [                
                        'asPopover' => false,
                        'formOptions' => ['action' => ['/site/nok']],
                    ];
                }
            ],
            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_address3',
                'refreshGrid' => true,
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' => function ($model) {
                    return [                
                        'asPopover' => false,
                        'formOptions' => ['action' => ['/site/nok']],
                    ];
                }
            ],  
            [
                'attribute' => 'nok_datetime_updated',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $date = new DateTime($data['nok_datetime_updated']);
                    $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                        // title
                        'title' => $date->format('Y-m-d H:i A') ,
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;',
                        'id' => 'nok_datetime_updated'
                    ] );
                    return $tag;
                },
            ],                
        ],
    ]); ?>

</div>