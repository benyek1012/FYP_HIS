<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_next_of_kin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-next-of-kin-form">

<?php $form = ActiveForm::begin([
        'id' => 'patient-information-form',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    $nokuid = Base64UID::generate(32);
    $relationship = array(
        'father'=>'Father',
        'monther'=>'Monther',
        'couple' => 'Couple',
        'brother' => 'Brother',
        'sister' => 'Sister',
        'other' => 'Other'
    );
    ?>

    <?= $form->field($model, 'nok_uid')->textInput(['readonly' => true, 'maxlength' => true,'value' => $nokuid]) ?>

    <?= $form->field($model, 'patient_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nok_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nok_relationship')->dropDownList($relationship, ['prompt'=>'Please select relationship','maxlength' => true]) ?>

    <?= $form->field($model, 'nok_phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nok_email')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
