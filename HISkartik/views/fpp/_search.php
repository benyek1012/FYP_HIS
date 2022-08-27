<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FppSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fpp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'kod') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'additional_details') ?>

    <?= $form->field($model, 'min_cost_per_unit') ?>

    <?= $form->field($model, 'max_cost_per_unit') ?>

    <?php // echo $form->field($model, 'number_of_units') ?>

    <?php // echo $form->field($model, 'total_cost') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
