<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_fpp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-fpp-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'kod')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'min_cost_per_unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'max_cost_per_unit')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
