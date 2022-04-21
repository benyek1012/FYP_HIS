<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */

$this->title = $model->bill_uid;
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bill-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'bill_uid' => $model->bill_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'bill_uid' => $model->bill_uid], [
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
            'bill_uid',
            'rn',
            'status_code',
            'status_description',
            'class',
            'daily_ward_cost',
            'department_code',
            'department_name',
            'is_free',
            'collection_center_code',
            'nurse_responsilbe',
            'bill_generation_datetime',
            'generation_responsible_uid',
            'bill_generation_billable_sum_rm',
            'bill_generation_final_fee_rm',
            'description',
            'bill_print_responsible_uid',
            'bill_print_datetime',
            'bill_print_id',
        ],
    ]) ?>

</div>
