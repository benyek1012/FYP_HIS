<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CancellationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cancellation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'cancellation_uid') ?>

    <?= $form->field($model, 'table') ?>

    <?= $form->field($model, 'reason') ?>

    <?= $form->field($model, 'replacement_uid') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
