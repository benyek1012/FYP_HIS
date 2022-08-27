<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReminderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Reminders');
$this->params['breadcrumbs'][] = $this->title;


?>


<div class="reminder-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Recalculate', ['recalculate'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Print', ['print'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Export CSV', ['csv'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Export PDF', ['pdf'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'batch_datetime',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'reminder1',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],
            
            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'reminder2',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'reminder3',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'responsible',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            //'batch_datetime',
            //'reminder1',
            //'reminder2',
            //'reminder3',
            //'responsible',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Reminder $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'batch_uid' => $model->batch_uid]);
                 }
            ],
        ],
    ]); ?>


</div>