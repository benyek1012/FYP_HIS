<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_fpp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-fpp-form" id="LOF_div" style="display:none;">

    <?php $form = kartik\form\ActiveForm::begin([
        'action' => ['lookup_fpp/create', 'kod' => $model['kod']],
        'id' => 'lookup-fpp-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]);
    ?>

    <div class ="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'kod')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>
            
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'min_cost_per_unit')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'max_cost_per_unit')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
