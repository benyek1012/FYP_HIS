<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */

$this->title = $model->receipt_uid;
$this->params['breadcrumbs'][] = ['label' => 'Receipts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="receipt-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'receipt_uid' => $model->receipt_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'receipt_uid' => $model->receipt_uid], [
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
            'receipt_uid',
            'rn',
            'receipt_type',
            'receipt_content_sum',
            'receipt_content_bill_id',
            'receipt_content_description',
            'receipt_content_date_paid',
            'receipt_content_payer_name',
            'receipt_content_payment_method',
            'card_no',
            'cheque_number',
            'receipt_responsible',
            'receipt_serial_number',
        ],
    ]) ?>

</div>
