<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment_details */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="treatment-details-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'treatment_details_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'treatment_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'treatment_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'item_per_unit_cost_rm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'item_count')->textInput() ?>

    <?= $form->field($model, 'item_total_unit_cost_rm')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
