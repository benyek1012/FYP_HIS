<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment_detailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="treatment-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'treatment_details_uid') ?>

    <?= $form->field($model, 'bill_uid') ?>

    <?= $form->field($model, 'treatment_code') ?>

    <?= $form->field($model, 'treatment_name') ?>

    <?= $form->field($model, 'item_per_unit_cost_rm') ?>

    <?php // echo $form->field($model, 'item_count') ?>

    <?php // echo $form->field($model, 'item_total_unit_cost_rm') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
