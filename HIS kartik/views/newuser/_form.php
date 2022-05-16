<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Newuser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="newuser-form" id="user_div" style="display:none;">

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => ['newuser/create', 'user_uid'=>$model['user_uid']],
        'id' => 'newuser-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    $user_uid = Base64UID::generate(32);
    $authKey = Base64UID::generate(32);
    ?>

    <div class ="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'user_uid')->textInput(['readonly' => true, 'maxlength' => true, 'value' => $user_uid]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'user_password')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'role')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'retire')->textInput(['value' => '1']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'authKey')->textInput(['maxlength' => true, 'readonly' => true, 'value' => $authKey]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
