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
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_name',
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
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' => function ($model) {
                    return [                
                        'asPopover' => false,
                        'formOptions' => ['action' => ['/site/nok']],
                    ];
                }
            ],                     
        ],
    ]); ?>


</div>
