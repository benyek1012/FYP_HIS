<?php

use yii\helpers\Html;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-form">

    <?php  
        $receipt = array(
            'deposit'=>'Deposit',
            'bill'=>'Bill',
            'refund'=>'Refund',
        );
    
        $form = kartik\form\ActiveForm::begin([
        'id' => 'patient-information-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);      ?>

    <?= $form->field($model, 'receipt_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Base64UID::generate(32)])->label(false); ?>

    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=>'Please select receipt','maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_description')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_date_paid')->textInput() ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_payer_name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_payment_method')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'card_no')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'cheque_number')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_responsible')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_serial_number')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>


</div>