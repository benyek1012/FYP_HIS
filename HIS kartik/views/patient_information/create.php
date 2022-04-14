<?php

use kartik\date\DatePicker;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use GpsLab\Component\Base64UID\Base64UID;
    
/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="patient-information-create">

<?php 
     $countries = array(
        'malaysia'=>'Malaysia',
        'indonesia'=>'Indonesia',
        'singapore' => 'Singapore',
        'thailand' => 'Thailand',
        'china' => 'China'
    );

    $sex = array(
        'male'=>'Male',
        'female'=>'Female',
    );

    $form = ActiveForm::begin([
        'action' => ['patient_information/create'],
        'id' => 'patient-information-form',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);     
?>

<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'first_reg_date')->widget(DatePicker::classname(), 
            ['pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd' ],
            ])?>
    </div>
    <div class="col-sn-6">
        <?= $form->field($model, 'patient_uid')->hiddenInput(['value'=> Base64UID::generate(32)])->label(false); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?php if(Yii::$app->request->get('ic') == 'undefined'){ ?>
            <?= $form->field($model, 'nric')->textInput(['maxlength' => true]) ?>
        <?php }else{ ?>
            <?= $form->field($model, 'nric')->textInput(['maxlength' => true, 'value' => Yii::$app->request->get('ic')]) ?>
        <?php } ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'nationality')->dropDownList($countries, ['prompt'=>'Please select country','maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'sex')->dropDownList($sex, ['prompt'=>'Please select sex','maxlength' => true]) ?> 
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'address2')->textInput(['maxlength' => true])->label(false)?>
        <?= $form->field($model, 'address3')->textInput(['maxlength' => true])->label(false)?>
    </div>
    
    <div class="col-sm-6">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'job')->textInput(['maxlength' => true]) ?>
    </div>

</div>
    
<div class="form-group">
    <?= Html::submitButton('Create', ['class' => 'btn btn-outline-primary align-self-start']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>
