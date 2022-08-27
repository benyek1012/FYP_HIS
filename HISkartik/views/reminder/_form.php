<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Reminder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reminder-form" id="RM_div" style="display:none;">

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => ['reminder/index','batch_uid'=>$model['batch_uid']],
        'id' => 'reminder-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); ?>


    <div class ="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'batch_datetime')->textInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'reminder1')->textInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'reminder2')->textInput() ?>
        </div> 
        <div class="col-sm-6">
            <?= $form->field($model, 'reminder3')->textInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'responsible')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>

