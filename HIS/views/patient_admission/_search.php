<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admissionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-admission-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'rn') ?>

    <?= $form->field($model, 'entry_datetime') ?>

    <?= $form->field($model, 'patient_uid') ?>

    <?= $form->field($model, 'initial_ward_code') ?>

    <?= $form->field($model, 'initial_ward_class') ?>

    <?php // echo $form->field($model, 'reference') ?>

    <?php // echo $form->field($model, 'medigal_legal_code') ?>

    <?php // echo $form->field($model, 'reminder_given') ?>

    <?php // echo $form->field($model, 'guarantor_name') ?>

    <?php // echo $form->field($model, 'guarantor_nric') ?>

    <?php // echo $form->field($model, 'guarantor_phone_number') ?>

    <?php // echo $form->field($model, 'guarantor_email') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
