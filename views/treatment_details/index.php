<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Treatment_detailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Treatment Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="treatment-details-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Treatment Details', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'treatment_details_uid',
            'bill_uid',
            'treatment_code',
            'treatment_name',
            'item_per_unit_cost_rm',
            //'item_count',
            //'item_total_unit_cost_rm',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'treatment_details_uid' => $model->treatment_details_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
