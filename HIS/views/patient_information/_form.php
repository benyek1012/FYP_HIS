<?php


use kartik\date\DatePicker;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use GpsLab\Component\Base64UID\Base64UID;
    
/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-information-form">

    <?php $form = ActiveForm::begin([
        'action' => ['patient_information/update', 'id' =>  $model->patient_uid],
        'id' => 'patient-information-form',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    $uid = Base64UID::generate(32);
    $countries = array(
        'malaysia'=>'Malaysia',
        'indonesia'=>'Indonesia',
        'singapore' => 'Singapore',
        'thailand' => 'Thailand',
        'china' => 'China'
    );
    ?>

    <?= $form->field($model, 'first_reg_date')->widget(DatePicker::classname(), 
        ['options' => ['placeholder' => 'Enter the fist registeration date ...'],
        'pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd' ],
        ])?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nric')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nationality')->dropDownList($countries, ['prompt'=>'Please select country','maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->inline()->radioList(['male' => 'Male', 'female' => 'Female'])->label(true) ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'job')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-outline-primary align-self-start']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>