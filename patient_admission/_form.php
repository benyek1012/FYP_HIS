<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-admission-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'rn')->textInput(['maxlength' => true]) ?>

    
    <?= $form->field($model, 'entry_datetime')->widget(DateTimePicker::classname(), 
        ['options' => ['placeholder' => 'Enter the entry date and time ...'],
        'pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd hh:ii' ]
    ])?>

    <?= $form->field($model, 'patient_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'initial_ward_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'initial_ward_class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reference')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'medigal_legal_code')->textInput() ?>

    <?= $form->field($model, 'reminder_given')->textInput() ?>

    <?= $form->field($model, 'guarantor_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'guarantor_nric')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'guarantor_phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'guarantor_email')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
