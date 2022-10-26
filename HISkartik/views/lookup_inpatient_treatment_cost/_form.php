<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Lookup_inpatient_treatment_cost $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lookup-inpatient-treatment-cost-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inpatient_treatment_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kod')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cost_rm')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
