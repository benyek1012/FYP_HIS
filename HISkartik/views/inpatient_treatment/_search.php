<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Inpatient_treatmentSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inpatient-treatment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'inpatient_treatment_uid') ?>

    <?= $form->field($model, 'bill_uid') ?>

    <?= $form->field($model, 'inpatient_treatment_cost_rm') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
