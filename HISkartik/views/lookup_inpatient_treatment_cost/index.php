<?php

use app\models\Lookup_inpatient_treatment_cost;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Lookup_inpatient_treatment_costSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Lookup Inpatient Treatment');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-inpatient-treatment-cost-index">

<?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                // data-key in gridview
                'data' => ['key' => $index],
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'kod',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_fpp/fpp']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'cost_rm',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_fpp/fpp']],
                // ]
            ],
        ],
    ]); ?>


</div>
