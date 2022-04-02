<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bill-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bill_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_free')->textInput() ?>

    <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nurse_responsilbe')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_generation_datetime')->textInput() ?>

    <?= $form->field($model, 'generation_responsible_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_generation_billable_sum_rm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_print_responsible_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_print_datetime')->textInput() ?>

    <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
