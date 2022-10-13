<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admissionSearch */
/* @var $form yii\widgets\ActiveForm */

$rows = (new \yii\db\Query())
->select('*')
->from('lookup_ward')
->orderBy('length(ward_code) ASC, ward_code ASC')
->all();

$ward_code = array();
foreach($rows as $row){
  $ward_code[$row['ward_code']] = $row['ward_code'] . " - " . $row['ward_name'];
}  
?>

<div class="patient-admission-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ward_code')->widget(kartik\select2\Select2::classname(), [
                'data' => $ward_code,
                'options' => ['placeholder' => Yii::t('app','Please select ward code'),
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'tags' => true,
                ]]]); 
    ?>
            
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>