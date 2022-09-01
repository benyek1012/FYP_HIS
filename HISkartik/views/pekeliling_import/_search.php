<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Pekeliling_importSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pekeliling-import-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'pekeliling_uid') ?>

    <?= $form->field($model, 'upload_datetime') ?>

    <?= $form->field($model, 'approval1_responsible_uid') ?>

    <?= $form->field($model, 'approval2_responsible_uid') ?>

    <?= $form->field($model, 'file_import') ?>

    <?php // echo $form->field($model, 'lookup_type') ?>

    <?php // echo $form->field($model, 'error') ?>

    <?php // echo $form->field($model, 'scheduled_datetime') ?>

    <?php // echo $form->field($model, 'executed_datetime') ?>

    <?php // echo $form->field($model, 'execute_responsible_uid') ?>

    <?php // echo $form->field($model, 'update_type') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
