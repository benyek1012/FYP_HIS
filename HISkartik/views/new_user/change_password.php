<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Newuser */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::$app->user->identity->username;
?>

<?php if(Yii::$app->session->hasFlash('success')):?>
    <div id = "flashError">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<div class="change-password-form">

    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'change-password-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

    <div class ="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'original_password')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'new_password')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'confirm_new_password')->passwordInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
