<?php

use app\models\Inpatient_treatment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Inpatient_treatmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Inpatient Treatments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inpatient-treatment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Inpatient Treatment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'inpatient_treatment_uid',
            'bill_uid',
            'inpatient_treatment_cost_rm',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Inpatient_treatment $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'inpatient_treatment_uid' => $model->inpatient_treatment_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
