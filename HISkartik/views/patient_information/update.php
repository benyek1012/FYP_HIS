<?php

use kartik\date\DatePicker;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
    
/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="patient-information-update">
    <?php 
        $rows_patient_information = (new \yii\db\Query())
        ->select('*')
        ->from('patient_information')
        ->where(['patient_uid'=> Yii::$app->request->get('id')])
        ->all();

        $rows_nationality = (new \yii\db\Query())
        ->select('*')
        ->from('lookup_general')
        ->where(['category'=> 'Nationality'])
        ->all();
        
        $countries = array();
        foreach($rows_nationality as $row_nationality){
            $countries[$row_nationality['code']] = $row_nationality['code'] . ' - ' . $row_nationality['name'] . ' [ ' .  $row_nationality['long_description'] . ' ]';  
        } 

        foreach($rows_patient_information as $row_patient_information){
            if(empty($countries[$row_patient_information['nationality']])){
                $countries[$row_patient_information['nationality']] = $row_patient_information['nationality'];
            }            
        }

        $rows_sex = (new \yii\db\Query())
        ->select('*')
        ->from('lookup_general')
        ->where(['category'=> 'Sex'])
        ->all();
        
        $sex = array();
        foreach($rows_sex as $row_sex){
            $sex[$row_sex['code']] = Yii::t('app', $row_sex['name']);
        } 

        $rows_race = (new \yii\db\Query())
        ->select('*')
        ->from('lookup_general')
        ->where(['category'=> 'Race'])
        ->all();
        
        $race = array();
        foreach($rows_race as $row_race){
            $race[$row_race['code']] = $row_race['code'] . ' - ' . $row_race['name'] . ' [ ' .  $row_race['long_description'] . ' ]';  
        } 

        foreach($rows_patient_information as $row_patient_information){
            if(empty($race[$row_patient_information['race']])){
                $race[$row_patient_information['race']] = $row_patient_information['race'];
            }            
        }

    $form = kartik\form\ActiveForm::begin([
        'action' => ['patient_information/update', 'id' =>  $model->patient_uid],
        'id' => 'patient-information-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);     
?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'nric')->textInput(['maxlength' => true, 'value' => Yii::$app->request->get('ic')]) ?>
        </div>
        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'nationality')->dropDownList($countries, 
                    ['prompt'=> Yii::t('app','Please select nationality'),'maxlength' => true]) ?> -->
                    
            <?= $form->field($model, 'nationality')->widget(kartik\select2\Select2::classname(), [
                'data' => $countries,
                'options' => ['placeholder' => Yii::t('app','Please select nationality'), 'id' => 'nationality',],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                ],
            ]); ?>
        </div>
        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'sex')->dropDownList($sex, 
                    ['prompt'=> Yii::t('app','Please select sex'),'maxlength' => true]) ?> -->

            <?= $form->field($model, 'sex')->radioList($sex, ['custom' => true, 'inline' => true]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'race')->widget(kartik\select2\Select2::classname(), [
                'data' => $race,
                'options' => ['placeholder' => Yii::t('app','Please select race'), 'id' => 'race',],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                ],
            ]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'job')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'address2')->textInput(['maxlength' => true])->label(false)?>
            <?= $form->field($model, 'address3')->textInput(['maxlength' => true])->label(false)?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-outline-primary align-self-start']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>