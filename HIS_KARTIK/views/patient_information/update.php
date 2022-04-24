<?php

use kartik\date\DatePicker;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
    
/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="patient-information-update">
    <?php 
     $countries = array(
        'malaysia'=>'Malaysia',
        'indonesia'=>'Indonesia',
        'singapore' => 'Singapore',
        'thailand' => 'Thailand',
        'china' => 'China'
    );

    $sex = array(
        'male'=>Yii::t('app','Male'),
        'female'=>Yii::t('app','Female'),
    );

    $race = array(
        'malay'=> Yii::t('app','Malay'),
        'chinese'=> Yii::t('app','Chinese'),
        'indian'=> Yii::t('app','Indian'),
        'kadazandusun' => 'Kadazandusun',
        'iban' => 'Iban',
        'others'=> 'Others',
    );

    $form = kartik\form\ActiveForm::begin([
        'action' => ['patient_information/update', 'id' =>  $model->patient_uid],
        'id' => 'patient-information-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);     
?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'first_reg_date')->widget(DatePicker::classname(), 
        ['options' => ['placeholder' => 'Enter the fist registeration date ...'],
        'pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd' ],
        ])?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nric')->textInput(['maxlength' => true, 'value' => Yii::$app->request->get('ic')]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nationality')->dropDownList($countries, ['prompt'=>'Please select country','maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'sex')->dropDownList($sex, ['prompt'=>'Please select sex','maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'race')->dropDownList($race, ['prompt'=>'Please select race','maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'address2')->textInput(['maxlength' => true])->label(false)?>
            <?= $form->field($model, 'address3')->textInput(['maxlength' => true])->label(false)?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'job')->textInput(['maxlength' => true]) ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-outline-primary align-self-start']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>