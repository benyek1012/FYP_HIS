<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-information-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'patient_uid')->textInput(['maxlength' => true]) ?>

    
    <?= $form->field($model, 'first_reg_date')->widget(DatePicker::classname(), 
        ['options' => ['placeholder' => 'Enter the fist registeration date ...'],
        'pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd' ]
        ])?>

    <?= $form->field($model, 'nric')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nationality')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
    <label for="Sex">Sex</label>
    <?= $form->field($model, 'sex')->radio(['label' => 'Male', 'value' => "Male", 'uncheck' => null]) ?>
    <?= $form->field($model, 'sex')->radio(['label' => 'Female', 'value' => "Female", 'uncheck' => null]) ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'job')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
