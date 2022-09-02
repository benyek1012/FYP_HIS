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
        <?= Html::a(Yii::t('app','Create Batch'), ['index'], ['class' => 'btn btn-success']) ?>
    </p>-->

    <?= $error ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'emptyCell' => HTML::a('Create Batch', ['index', 'function' => 'batchCreate'], ['class' => 'btn btn-success']),
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
                'template' => '{getReminderCalculate}',
                'header' => 'Recalculate',
                'buttons' => [
                'getReminderCalculate' => function ($url, $model, $key) {
                    return HTML::a('Recalculate', ['index', 'function' => 'getReminderCalculate']);
                 },
                ],
            ],

            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{batchCreate}',
                'header' => 'Create Batch',
                'buttons' => [
                'batchCreate' => function ($url, $model, $key) {
                    return HTML::a('Create Batch', ['index', 'function' => 'batchCreate']);
                 },
                ],
            ],

            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{downloadcsv}',
                'header' => 'Download CSV',
                'buttons' => [
                'downloadcsv' => function ($url, $model, $key) {
                    return HTML::a('Download CSV', ['index', 'function' => 'downloadcsv', 'batch_date' => $model->batch_date]);
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