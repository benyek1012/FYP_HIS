<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_department */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-department-form" id="LOD_div" style="display:none;">

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => ['lookup_department/create', 'department_uid'=>$model['department_uid']],
        'id' => 'lookup-department-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $department_uid = Base64UID::generate(32);
    ?>

<div class ="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'department_code')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'department_name')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'phone_number')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'address1')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'address2')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'address3')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'department_uid')->hiddenInput(['readonly' => true, 'maxlength' => true, 'value' => $department_uid])->label(false)?>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
