<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Cancellation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cancellation-form">

    <?php $form = kartik\form\ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'reason')->textarea(['rows' => '6']) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'cancellation_uid')->hiddenInput(['maxlength' => true, 'value' => Base64UID::generate(32)])->label(false); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'table')->hiddenInput(['maxlength' => true, 'value' => $model_receipt->receipt_type])->label(false); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'replacement_uid')->hiddenInput(['maxlength' => true, 'value' => $model_receipt->receipt_uid])->label(false); ?>
        </div> 
    </div>

    <!-- <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div> -->

    <?php kartik\form\ActiveForm::end(); ?>

</div>
