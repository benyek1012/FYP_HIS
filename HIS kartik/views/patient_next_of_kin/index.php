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
?>
<div class="patient-next-of-kin-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Patient Next Of Kin', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_name',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/site/nok']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_relationship',
                'editableOptions' => [
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'asPopover' => false,
                    'data' => [
                        'father'=>'Father',
                        'mother'=>'Mother',
                        'husband/spouse' => 'Husband / Spouse',
                        'brother' => 'Brother',
                        'sister' => 'Sister',
                        'son' => 'Son',
                        'daughter' => 'Daughter',
                        'other' => 'Other'
                    ],
                    'formOptions' => ['action' => ['/site/nok']],
                ],
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_phone_number',
                'editableOptions' => [
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/site/nok']],
                    ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nok_email',
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
