<?php

use yii\bootstrap4\Html;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_next_of_kin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-next-of-kin-form" id="NOk_Div"  style="display:none;">

<?php $form = kartik\form\ActiveForm::begin([
        'id' => 'patient-next-of-kin-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $nokuid = Base64UID::generate(32);
    $relationship = array(
        'father'=>'Father',
        'mother'=>'Mother',
        'husband/spouse' => 'Husband / Spouse',
        'brother' => 'Brother',
        'sister' => 'Sister',
        'son' => 'Son',
        'daughter' => 'Daughter',
        'other' => 'Other'
    );
    ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $nokuid])->label(false); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'patient_uid')->hiddenInput(['value'=> $value])->label(false); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_relationship')->dropDownList($relationship, ['prompt'=>'Please select relationship','maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_phone_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
        <?= $form->field($model, 'nok_address1')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'nok_address2')->textInput(['maxlength' => true])->label(false)?>
        <?= $form->field($model, 'nok_address3')->textInput(['maxlength' => true])->label(false)?>
    </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>

