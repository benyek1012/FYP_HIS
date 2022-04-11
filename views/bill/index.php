<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bills';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bill-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Bill', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'bill_uid',
            'rn',
            'status_code',
            'status_description',
            'class',
            //'daily_ward_cost',
            //'department_code',
            //'department_name',
            //'is_free',
            //'collection_center_code',
            //'nurse_responsilbe',
            //'bill_generation_datetime',
            //'generation_responsible_uid',
            //'bill_generation_billable_sum_rm',
            //'bill_generation_final_fee_rm',
            //'description',
            //'bill_print_responsible_uid',
            //'bill_print_datetime',
            //'bill_print_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'bill_uid' => $model->bill_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
