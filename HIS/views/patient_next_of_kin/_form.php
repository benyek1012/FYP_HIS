<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_next_of_kin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-next-of-kin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nok_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patient_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nok_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nok_relationship')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nok_phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nok_email')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
