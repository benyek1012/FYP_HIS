<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Pekeliling_import */
/* @var $form yii\widgets\ActiveForm */


$method_lookup = array(
    'status' =>  Yii::t('app','Status Lookup'),
    'treatment' => Yii::t('app','Treatment Codes'), 
    'ward' => Yii::t('app','Ward Codes'),
    'department' => Yii::t('app','Department Codes'),
    'fpp' => Yii::t('app','Full Paying Patient'),
 );
 
 $method_type = array(
     'insert' =>  Yii::t('app','Insert / Update'),
     'delete' => Yii::t('app','Delete old codes'), 
 );
?>

<div class="pekeliling-import-form">

    <?php $form = kartik\form\ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-sm-12 required">
            <?= $form->field($model, 'file')->fileInput(['class' => 'control-label','required'=>true])->label(Yii::t('app','File')); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'lookup_type')->radioList($method_lookup, 
                    ['maxlength' => true, 'id' => 'radio']) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'update_type')->radioList($method_type, 
                    ['maxlength' => true, 'id' => 'radio2']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>