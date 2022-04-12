<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BillSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bill-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'bill_uid') ?>

    <?= $form->field($model, 'rn') ?>

    <?= $form->field($model, 'status_code') ?>

    <?= $form->field($model, 'status_description') ?>

    <?= $form->field($model, 'class') ?>

    <?php // echo $form->field($model, 'daily_ward_cost') ?>

    <?php // echo $form->field($model, 'department_code') ?>

    <?php // echo $form->field($model, 'department_name') ?>

    <?php // echo $form->field($model, 'is_free') ?>

    <?php // echo $form->field($model, 'collection_center_code') ?>

    <?php // echo $form->field($model, 'nurse_responsilbe') ?>

    <?php // echo $form->field($model, 'bill_generation_datetime') ?>

    <?php // echo $form->field($model, 'generation_responsible_uid') ?>

    <?php // echo $form->field($model, 'bill_generation_billable_sum_rm') ?>

    <?php // echo $form->field($model, 'bill_generation_final_fee_rm') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'bill_print_responsible_uid') ?>

    <?php // echo $form->field($model, 'bill_print_datetime') ?>

    <?php // echo $form->field($model, 'bill_print_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
