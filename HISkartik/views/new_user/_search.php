<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NewuserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="newuser-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_uid') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'user_password') ?>

    <?= $form->field($model, 'role_cashier') ?>

    <?= $form->field($model, 'role_clerk') ?>

    <?= $form->field($model, 'role_admin') ?>

    <?= $form->field($model, 'role_guest_print') ?>

    <?= $form->field($model, 'Case_Note') ?>

    <?= $form->field($model, 'Registration') ?>

    <?= $form->field($model, 'Charge_Sheet') ?>

    <?= $form->field($model, 'Sticker_Label') ?>

    <?= $form->field($model, 'retire') ?>

    <?php // echo $form->field($model, 'authKey') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
