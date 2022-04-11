<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Ward */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ward-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ward_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ward_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ward_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ward_start_datetime')->textInput() ?>

    <?= $form->field($model, 'ward_end_datetime')->textInput() ?>

    <?= $form->field($model, 'ward_number_of_days')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
