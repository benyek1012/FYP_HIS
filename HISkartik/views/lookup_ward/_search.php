<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_wardSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-ward-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ward_uid') ?>

    <?= $form->field($model, 'ward_code') ?>

    <?= $form->field($model, 'ward_name') ?>

    <?= $form->field($model, 'sex') ?>

    <?= $form->field($model, 'min_age') ?>

    <?php // echo $form->field($model, 'max_age') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
