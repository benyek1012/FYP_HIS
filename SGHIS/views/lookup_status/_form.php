<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_status */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-status-form" id="LOS_div" style="display:none;">

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => ['lookup_status/create', 'status_uid'=>$model['status_uid']],
        'id' => 'lookup-status-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $status_uid = Base64UID::generate(32);
    ?>

<div class ="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'status_code')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'status_description')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'class_1a_ward_cost')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6"> 
        <?= $form->field($model, 'class_1b_ward_cost')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'class_1c_ward_cost')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'class_2_ward_cost')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'class_3_ward_cost')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'status_uid')->hiddenInput(['readonly' => true, 'maxlength' => true, 'value' => $status_uid])->label(false)?>
    </div>
</div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>


    <?php kartik\form\ActiveForm::end(); ?>

</div>
