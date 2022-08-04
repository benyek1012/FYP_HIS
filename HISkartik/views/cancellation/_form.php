<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cancellation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cancellation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cancellation_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'table')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'replacement_uid')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
