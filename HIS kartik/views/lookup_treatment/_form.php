<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_treatment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-treatment-form" id="LOT_div" style="display:none;">

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => ['lookup_treatment/create', 'treatment_uid'=>$model['treatment_uid']],
        'id' => 'lookup-treatment-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $treatment_uid = Base64UID::generate(32);
    ?>
<div class ="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'treatment_code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'treatment_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'class_1_cost_per_unit')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'class_2_cost_per_unit')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'class_3_cost_per_unit')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'treatment_uid')->hiddenInput(['readonly' => true, 'maxlength' => true, 'value' => $treatment_uid])->label(false)?>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
