<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_statusSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-status-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'status_uid') ?>

    <?= $form->field($model, 'status_code') ?>

    <?= $form->field($model, 'status_description') ?>

    <?= $form->field($model, 'class_1a_ward_cost') ?>

    <?= $form->field($model, 'class_1b_ward_cost') ?>

    <?php // echo $form->field($model, 'class_1c_ward_cost') ?>

    <?php // echo $form->field($model, 'class_2_ward_cost') ?>

    <?php // echo $form->field($model, 'class_3_ward_cost') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
