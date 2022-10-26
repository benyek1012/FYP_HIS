<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Lookup_inpatient_treatment_cost $model */

$this->title = $model->inpatient_treatment_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Inpatient Treatment Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lookup-inpatient-treatment-cost-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'inpatient_treatment_uid' => $model->inpatient_treatment_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'inpatient_treatment_uid' => $model->inpatient_treatment_uid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'inpatient_treatment_uid',
            'kod',
            'cost_rm',
        ],
    ]) ?>

</div>
