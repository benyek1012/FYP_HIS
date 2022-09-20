<?php

use app\models\New_user;
use app\models\Reminder;
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

    <?php if(Yii::$app->session->hasFlash('error_user')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('error_user') ?>
        </div>
    <?php endif; ?>

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
                'value'=>function ($data) {
                    return ($data->batch_date == Reminder::placeholder) ? " " : $data->batch_date;
                },
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
                'value'=>function ($data) {
                    $model_User = New_user::findOne(['user_uid' => $data['responsible_uid']]);
                    if(!empty($model_User))
                        return $model_User->getName();
                },
            ],

            [
                'class' =>  ActionColumn::className(),
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'template' => '{getReminderCalculate}{batchCreate}{downloadcsv}{exportPdf}{print}',
                'buttons' => [
                    'getReminderCalculate' => function ($url, $model, $key) {
                        return ($model->batch_date == Reminder::placeholder) ? 
                                HTML::a('Regenerate', ['index', 'function' => 'getReminderCalculate'],
                                        ['class' => 'btn btn-success btn-xs'])
                                : HTML::a('Regenerate', ['index', 'function' => 'getReminderCalculate'],
                                        ['class' => 'd-none btn btn-success btn-xs']);
                    },
                    'batchCreate' => function ($url, $model, $key) {
                        return ($model->batch_date == Reminder::placeholder) ? 
                            HTML::a('Create Batch', ['index', 'function' => 'batchCreate'], ['class' => 'btn btn-success btn-xs'])
                        : HTML::a('Create Batch', ['index', 'function' => 'batchCreate'], ['class' => 'd-none btn btn-success btn-xs']);
                    },
                    'downloadcsv' => function ($url, $model, $key) {
                        return ($model->batch_date != Reminder::placeholder) ? 
                            HTML::a('Export CSV', ['index', 'function' => 'downloadcsv', 'batch_date' => $model->batch_date],
                                ['class' => 'btn btn-success btn-xs'])
                        : HTML::a('Export CSV', ['index', 'function' => 'downloadcsv', 'batch_date' => $model->batch_date],
                                ['class' => 'd-none btn btn-success btn-xs']);
                    },
                    'exportPdf' => function ($url, $model, $key) {
                        return ($model->batch_date != Reminder::placeholder) ? 
                            HTML::a('Export Pdf', ['index', 'function' => 'exportPdf', 'batch_date' => $model->batch_date],
                                ['class' => 'btn btn-success btn-xs'])
                        :  HTML::a('Export Pdf', ['index', 'function' => 'exportPdf', 'batch_date' => $model->batch_date],
                        ['class' => 'd-none btn btn-success btn-xs']);
                    },
                    'print' => function ($url, $model, $key) {
                        return ($model->batch_date != Reminder::placeholder) ? 
                            HTML::a('Print Pdf', ['index', 'function' => 'print', 'batch_date' => $model->batch_date],
                                ['class' => 'btn btn-success btn-xs'])
                        :  HTML::a('Print Pdf', ['index', 'function' => 'print', 'batch_date' => $model->batch_date],
                        ['class' => 'd-none btn btn-success btn-xs']);
                    },
                ]
            ],
            

            //'batch_date',
            //'reminder1',
            //'reminder2',
            //'reminder3',
            //'responsible',
            
        ],

    ]); ?>


</div>