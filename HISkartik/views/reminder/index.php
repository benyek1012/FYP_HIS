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

    <!-- <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

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
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'batch_date',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'reminder1count',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
            ],
            
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'reminder2count',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'reminder3count',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'responsible_uid',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                
            ],

            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{Recalculate}',
                'header' => ' ',
                'buttons' => [
                'Recalculate' => function ($url, $model, $key) {
                    return HTML::a('Recalculate', ['index', 'function' => 'recalculate', 'id' => $model->batch_date]);
                 },
                ],
            ],

            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{batch_create}',
                'header' => ' ',
                'buttons' => [
                'batch_create' => function ($url, $model, $key) {
                    return HTML::a('Create Batch', ['index', 'function' => 'create_batch', 'id' => $model->batch_date]);
                 },
                ],
            ],

            

            //'batch_date',
            //'reminder1',
            //'reminder2',
            //'reminder3',
            //'responsible',
            
        ],

    ]); ?>


</div>