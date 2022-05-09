<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_ward */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-ward-form" id="LOW_div" style="display:none;">

<?php $form = kartik\form\ActiveForm::begin([
        'action' => ['lookup_ward/create', 'ward_uid'=>$model['ward_uid']],
        'id' => 'lookup-ward-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $ward_uid = Base64UID::generate(32);
    $sex = array(
        'male'=>Yii::t('app','Male'),
        'female'=>Yii::t('app','Female'),
    );
    ?>

<div class ="row">



    <div class="col-sn-6">
        <?= $form->field($model, 'ward_uid')->hiddenInput(['readonly' => true, 'maxlength' => true, 'value' => $ward_uid])->label(false); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'ward_code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'ward_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
    <?= $form->field($model, 'sex')->dropDownList($sex, ['prompt'=>'Please select sex','maxlength' => true]) ?> 
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'min_age')->textInput() ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'max_age')->textInput() ?>
    </div>
</div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
