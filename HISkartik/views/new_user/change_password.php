<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Newuser */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::$app->user->identity->username;
?>

<?php if(Yii::$app->session->hasFlash('error_password')):?>
    <div id = "flashError">
        <?= Yii::$app->session->getFlash('error_password') ?>
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
            <?= $form->field($model, 'user_password')->passwordInput(['maxlength' => true, 'value' => '']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true, 'value' => '']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
