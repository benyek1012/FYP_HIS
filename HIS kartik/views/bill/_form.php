<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use hail812\adminlte3\widgets\FlashAlert;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bill-form">


<?php $form = ActiveForm::begin([
        'id' => 'patient-information-form',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    $billuid = Base64UID::generate(32);
    $generationresponsibleuid = Base64UID::generate(32);
    $billprintresponsibleuid = Base64UID::generate(32);

    ?>

    <?= $form->field($model, 'bill_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $billuid])->label(false) ?>

    <?= $form->field($model, 'rn')->textInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')]) ?>

    <?= $form->field($model, 'status_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_free')->textInput() ?>

    <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nurse_responsilbe')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'bill_generation_datetime')->widget(DateTimePicker::classname(), 
        ['pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii' ]
    ])?>

    <?= $form->field($model, 'generation_responsible_uid')->textInput(['readonly' => true, 'maxlength' => true,'value' => $generationresponsibleuid]) ?>

    <?= $form->field($model, 'bill_generation_billable_sum_rm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_print_responsible_uid')->textInput(['readonly' => true, 'maxlength' => true,'value' => $billprintresponsibleuid]) ?>
    
    <?= $form->field($model, 'bill_print_datetime')->widget(DateTimePicker::classname(), 
        ['pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii' ]
    ])?>

    <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
