<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CancellationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cancellations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cancellation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cancellation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cancellation_uid',
            'table',
            'reason',
            'replacement_uid',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Cancellation $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'cancellation_uid' => $model->cancellation_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
