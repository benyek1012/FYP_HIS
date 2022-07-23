<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_treatmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-treatment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'treatment_uid') ?>

    <?= $form->field($model, 'treatment_code') ?>

    <?= $form->field($model, 'treatment_name') ?>

    <?= $form->field($model, 'class_1_cost_per_unit') ?>

    <?= $form->field($model, 'class_2_cost_per_unit') ?>

    <?php // echo $form->field($model, 'class_3_cost_per_unit') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
