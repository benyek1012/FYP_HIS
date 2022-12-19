<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admissionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-information-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

<?php
    if(empty($model->discharge_date)){
        $value = date('Y-m-d');
    }
    else{
        $value = $model->discharge_date;
    }
    ?>

    <?= $form->field($model, 'discharge_date')->textInput(['autocomplete' =>'off', 'value' => $value]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
