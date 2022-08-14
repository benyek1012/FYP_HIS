<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Newuser */
/* @var $form yii\widgets\ActiveForm */

if(empty(Yii::$app->request->get('user_uid'))){
    $url = ['new_user/create'];
    $disabled = false;
    $user_uid = Base64UID::generate(32);
    $authKey = Base64UID::generate(32);
}
else{
    $url = ['new_user/update', 'user_uid'=>$model['user_uid']];
    $disabled = true;
    $user_uid = $model->user_uid;
    $authKey = $model->authKey;
}

if($model->role_clerk){
    $role_clerk = true;
}
else{
    $role_clerk = false;
}

if($model->role_cashier){
    $role_cashier = true;
}
else{
    $role_cashier = false;
}

if($model->role_admin){
    $role_admin = true;
}
else{
    $role_admin = false;
}

if($model->role_guest_print){
    $role_guest_print = true;
}
else{
    $role_guest_print = false;
}
?>

<div class="newuser-form" id="user_div" <?php if(empty(Yii::$app->request->get('user_uid'))) echo 'style="display:none;"'; ?>>

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => $url,
        'id' => 'newuser-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

    <div class ="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
        </div>
        <?php
        if(empty(Yii::$app->request->get('user_uid'))){
        ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'user_password')->passwordInput(['maxlength' => true]) ?>
            </div>
        <?php
        }
        else{
        ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'new_password')->passwordInput(['maxlength' => true]) ?>
            </div>
        <?php
        }

        if(!empty(Yii::$app->request->get('user_uid'))){
        ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'confirm_new_password')->passwordInput(['maxlength' => true]) ?>
            </div>
        <?php
        }
        ?>
        
        <div class="col-sm-6">
            <?= $form->field($model, 'role_cashier')->checkbox(['checked'=> $role_cashier, 'uncheck'=>'0', 'value' => '1', 'disabled' => $disabled]) ?>
            <?= $form->field($model, 'role_clerk')->checkbox(['checked'=> $role_clerk, 'uncheck'=>'0', 'value' => '1', 'disabled' => $disabled]) ?>
            <?= $form->field($model, 'role_admin')->checkbox(['checked'=> $role_admin, 'uncheck'=>'0', 'value' => '1', 'disabled' => $disabled]) ?>
            <?= $form->field($model, 'role_guest_print')->checkbox(['checked'=> $role_guest_print, 'uncheck'=>'0', 'value' => '1', 'disabled' => $disabled]) ?>
        </div>
        <div class="col-sm-6" style="display:none;">
            < <?= $form->field($model, 'Case_Note')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
            <?= $form->field($model, 'Registration')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
            <?= $form->field($model, 'Charge_Sheet')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
            <?= $form->field($model, 'Sticker_Label')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?> 
        </div>
        <?php
        if(empty(Yii::$app->request->get('user_uid'))){
        ?>
            <div class="col-sm-6">
                <?= $form->field($model, 'retire')->textInput(['value' => '0', 'disabled' => $disabled]) ?>
            </div>
        <?php
        }
        ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'user_uid')->hiddenInput(['maxlength' => true, 'readonly' => true,'value' => $user_uid])->label(false)?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'authKey')->hiddenInput(['maxlength' => true, 'readonly' => true, 'value' => $authKey])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?php 
        if(empty(Yii::$app->request->get('user_uid'))){
        ?>
            <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
        <?php
        }
        else{
        ?>
            <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-success']) ?>
        <?php
        }
        ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
