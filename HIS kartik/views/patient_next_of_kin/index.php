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

$this->title = 'Patient Next Of Kins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-next-of-kin-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Patient Next Of Kin', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php     
        $relationship = array(
        'father'=>'Father',
        'monther'=>'Monther',
        'couple' => 'Couple',
        'brother' => 'Brother',
        'sister' => 'Sister',
        'other' => 'Other'
    );// echo $this->render('_search', ['model' => $searchModel]); 
    ?>


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
                        'monther'=>'Monther',
                        'couple' => 'Couple',
                        'brother' => 'Brother',
                        'sister' => 'Sister',
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
        ],
    ]); ?>


</div>
