<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment_details */

$this->title = $model->treatment_details_uid;
$this->params['breadcrumbs'][] = ['label' => 'Treatment Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="treatment-details-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'treatment_details_uid' => $model->treatment_details_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'treatment_details_uid' => $model->treatment_details_uid], [
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
            'treatment_details_uid',
            'bill_uid',
            'treatment_code',
            'treatment_name',
            'item_per_unit_cost_rm',
            'item_count',
            'item_total_unit_cost_rm',
        ],
    ]) ?>

</div>
