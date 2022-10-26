<?php

use app\models\Lookup_inpatient_treatment_cost;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Lookup_inpatient_treatment_costSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lookup Inpatient Treatment Costs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-inpatient-treatment-cost-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lookup Inpatient Treatment Cost', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'inpatient_treatment_uid',
            'kod',
            'cost_rm',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Lookup_inpatient_treatment_cost $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'inpatient_treatment_uid' => $model->inpatient_treatment_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
