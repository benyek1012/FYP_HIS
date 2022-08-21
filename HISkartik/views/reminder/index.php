<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReminderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reminders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Reminder', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'batch_datetime',
            'reminder1',
            'reminder2',
            'reminder3',
            'responsible',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Reminder $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'batch_uid' => $model->batch_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
