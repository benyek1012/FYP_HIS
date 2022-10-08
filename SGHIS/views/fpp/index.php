<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FppSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fpps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fpp-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Fpp', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'kod',
            'name',
            'additional_details',
            'min_cost_per_unit',
            'max_cost_per_unit',
            //'number_of_units',
            //'total_cost',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Fpp $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'kod' => $model->kod]);
                 }
            ],
        ],
    ]); ?>


</div>
