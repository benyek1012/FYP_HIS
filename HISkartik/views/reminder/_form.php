<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Reminder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reminder-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= $form->field($model, 'batch_datetime')->textInput() ?>

    <?= $form->field($model, 'reminder1')->textInput() ?>

    <?= $form->field($model, 'reminder2')->textInput() ?>

    <?= $form->field($model, 'reminder3')->textInput() ?>

    <?= $form->field($model, 'responsible')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
