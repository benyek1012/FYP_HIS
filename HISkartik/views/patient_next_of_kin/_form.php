<?php

use yii\bootstrap4\Html;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_next_of_kin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-next-of-kin-form" id="NOk_Div"  style="display:none;">

<?php $form = kartik\form\ActiveForm::begin([
        'id' => 'patient-next-of-kin-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $nokuid = Base64UID::generate(32);

    $rows_relationship = (new \yii\db\Query())
    ->select('*')
    ->from('lookup_general')
    ->where(['category'=> 'Relationship'])
    ->all();
    
    $relationship = array();
    foreach($rows_relationship as $row_relationship){
        $relationship[$row_relationship['code']] = $row_relationship['code'] . ' - ' . $row_relationship['name'] . ' [ ' .  $row_relationship['long_description'] . ' ]';  
    } 
   
    ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $nokuid])->label(false); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'patient_uid')->hiddenInput(['value'=> $value])->label(false); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'nok_relationship')->dropDownList($relationship, 
                    ['prompt'=> Yii::t('app','Please select relationship'),'maxlength' => true]) ?> -->

            <?= $form->field($model, 'nok_relationship')->widget(kartik\select2\Select2::classname(), [
                'data' => $relationship,
                'options' => ['placeholder' => Yii::t('app','Please select relationship'), 'id' => 'relationship',],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                ],
            ]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nok_phone_number')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'nok_email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
        <?= $form->field($model, 'nok_address1')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'nok_address2')->textInput(['maxlength' => true])->label(false)?>
        <?= $form->field($model, 'nok_address3')->textInput(['maxlength' => true])->label(false)?>
    </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-outline-primary align-self-start']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>

