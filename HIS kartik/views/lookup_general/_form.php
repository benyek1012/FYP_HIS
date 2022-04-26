<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_general */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-general-form" id="LOK_div" style="display:none;">

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => ['lookup_general/create', 'lookup_general_uid'=>$model['lookup_general_uid']],
        'id' => 'lookup-general-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $lookup_general_uid = Base64UID::generate(32);
    ?>

    <div class ="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'lookup_general_uid')->textInput(['readonly' => true, 'maxlength' => true, 'value' => $lookup_general_uid]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'long_description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'recommend')->textInput(['value' => '1']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
