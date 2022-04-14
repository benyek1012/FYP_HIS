<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-admission-form">

    <?php $form = kartik\form\ActiveForm::begin([
            'id' => 'patient-admission-form',
            'type' => 'vertical',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
            ],
        ]); 

        $rows = (new \yii\db\Query())
        ->select(['rn'])
        ->from('patient_admission')
        ->where(['type' => Yii::$app->request->get('type')])
        ->all();
        $SID = "1" + count($rows);

        if(Yii::$app->request->get('type') == 'Normal'){
            $rn = date('Y')."/".sprintf('%06d', $SID);
        }
        else{
            $rn = date('Y')."/9".sprintf('%05d', $SID);
        }

    ?>

    <?= $form->field($model, 'patient_uid')->hiddenInput(['value'=> Yii::$app->request->get('id')])->label(false); ?>
    <div class="row">
    <div class="col-sm-6">
            <?= $form->field($model, 'type')->textInput(['readonly' => true,'maxlength' => true, 'value' => Yii::$app->request->get('type')]) ?>
        </div>
        <div class="col-sm-6">
            <?php if(Yii::$app->request->get('id')){ ?>
            <?= $form->field($model, 'rn')->textInput(['readonly' => true, 'maxlength' => true,'value' => $rn]) ?>
            <?php }else{ ?>
            <?= $form->field($model, 'rn')->textInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')]) ?>
            <?php } ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'entry_datetime')->widget(DateTimePicker::classname(), 
                ['options' => ['placeholder' => 'Enter the entry date and time ...'],
                'pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd hh:ii' ]
            ])?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'initial_ward_code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'initial_ward_class')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'reference')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'medigal_legal_code')->textInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'reminder_given')->textInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_nric')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_phone_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>