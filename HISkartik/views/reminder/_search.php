<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReminderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reminder-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>



    <?= $form->field($model, 'batch_datetime') ?>

    <?= $form->field($model, 'reminder1') ?>

    <?= $form->field($model, 'reminder2') ?>

    <?= $form->field($model, 'reminder3') ?>

    <?php // echo $form->field($model, 'responsible') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
