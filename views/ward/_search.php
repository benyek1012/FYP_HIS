<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WardSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ward-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ward_uid') ?>

    <?= $form->field($model, 'bill_uid') ?>

    <?= $form->field($model, 'ward_code') ?>

    <?= $form->field($model, 'ward_name') ?>

    <?= $form->field($model, 'ward_start_datetime') ?>

    <?php // echo $form->field($model, 'ward_end_datetime') ?>

    <?php // echo $form->field($model, 'ward_number_of_days') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
