<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Cancellation */
/* @var $form yii\widgets\ActiveForm */

if($type == 'bill'){
    $url = Url::toRoute(['/bill/cancellation', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')]);
}
else if($type == 'admission'){
    $url = Url::toRoute(['/patient_admission/cancellation', 'rn' => $model_admission->rn, 'type' => $model_admission->type, 'id' => $model_admission->patient_uid]);
}
?>

<div class="cancellation-form">

    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'cancellation-form',
        'action' => $url,
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'reason')->textarea(['rows' => '6']) ?>
        </div>

        <!-- <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'cancellation_uid')->hiddenInput(['maxlength' => true, 'value' => Base64UID::generate(32)])->label(false); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'table')->hiddenInput(['maxlength' => true, 'value' => ''])->label(false); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model_cancellation, 'replacement_uid')->hiddenInput(['maxlength' => true, 'value' => ''])->label(false); ?>
        </div>  -->
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Cancellation'), ['class' => 'btn btn-danger']) ?> 
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
