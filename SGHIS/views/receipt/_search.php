<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'receipt_uid') ?>

    <?= $form->field($model, 'rn') ?>

    <?= $form->field($model, 'receipt_type') ?>

    <?= $form->field($model, 'receipt_content_sum') ?>

    <?= $form->field($model, 'receipt_content_bill_id') ?>

    <?php // echo $form->field($model, 'receipt_content_description') ?>

    <?php // echo $form->field($model, 'receipt_content_date_paid') ?>

    <?php // echo $form->field($model, 'receipt_content_payer_name') ?>

    <?php // echo $form->field($model, 'receipt_content_payment_method') ?>

    <?php // echo $form->field($model, 'card_no') ?>

    <?php // echo $form->field($model, 'cheque_number') ?>

    <?php // echo $form->field($model, 'receipt_responsible') ?>

    <?php // echo $form->field($model, 'receipt_serial_number') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
