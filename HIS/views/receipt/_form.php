<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'receipt_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_content_bill_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_content_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_content_date_paid')->textInput() ?>

    <?= $form->field($model, 'receipt_content_payer_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_content_payment_method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'card_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cheque_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_responsible')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_serial_number')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
