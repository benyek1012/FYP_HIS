<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Batch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="batch-form">

    <?php $form = kartik\form\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'batch')->hiddenInput(['value' => 1])->label(false); ?>

    <?= $form->field($model, 'file')->fileInput()->label(Yii::t('app','File')); ?>

   

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
