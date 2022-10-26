<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Lookup_inpatient_treatment_costSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lookup-inpatient-treatment-cost-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'inpatient_treatment_uid') ?>

    <?= $form->field($model, 'kod') ?>

    <?= $form->field($model, 'cost_rm') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
