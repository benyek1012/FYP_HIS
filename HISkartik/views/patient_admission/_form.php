<?php

use yii\bootstrap4\Html;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-admission-form">

    <?php 
    $rows_patient_admission = (new \yii\db\Query())
    ->select('*')
    ->from('patient_admission')
    ->where(['rn'=> Yii::$app->request->get('rn')])
    ->all();

    $rows = (new \yii\db\Query())
    ->select('*')
    ->from('lookup_ward')
    ->all();

    $ward_code = array();
    foreach($rows as $row){
      $ward_code[$row['ward_code']] = $row['ward_code'] . " - " . $row['ward_name'];
    }  

    $rows_patient_information = (new \yii\db\Query())
    ->select('*')
    ->from('patient_information')
    ->all();
    $all_Nric = array();
    foreach( $rows_patient_information as $row){
        $all_Nric[$row['nric']] = $row['nric'] ;
      }  

    // foreach($rows_patient_admission as $row_patient_admission){
    //     if(empty($ward_code[$row_patient_admission['initial_ward_code']])){
    //         $ward_code[$row_patient_admission['initial_ward_code']] = $row_patient_admission['initial_ward_code'];
    //     }            
    // }

    $ward_class = array(
        "1a" =>'1a', 
        "1b" =>'1b', 
        "1c" =>'1c', 
        "2" =>'2', 
        "3" =>'3', 
    );

    // foreach($rows_patient_admission as $row_patient_admission){
    //     if(empty($ward_class[$row_patient_admission['initial_ward_class']])){
    //         $ward_class[$row_patient_admission['initial_ward_class']] = $row_patient_admission['initial_ward_class'];
    //     }            
    // }


    $form = kartik\form\ActiveForm::begin([
        'id' => 'patient-admission-form',
        'type' => 'vertical',
       // 'action' => ['patient_admission/transfer'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>
    <?=$form->field($modelpatient, 'nric')->widget(kartik\select2\Select2::classname(), [
        'data' => $all_Nric,
        'options' => ['placeholder' => Yii::t('app','Please select NRIC'), 'id' => 'nric',],
        'pluginOptions' => [
            'allowClear' => true,
            // 'tags' => true,
        ],
    ])->label(Yii::t('app','Transfer To New Patient'));?>
    <?= Html::submitButton(Yii::t('app','Yes'), ['class' => 'btn btn-success']) ?>

    <?php kartik\form\ActiveForm::end(); 




    $form = kartik\form\ActiveForm::begin([
            'id' => 'patient-admission-form',
            'type' => 'vertical',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
            ],
        ]); 

    ?>

    <?= $form->field($model, 'patient_uid')->hiddenInput(['value'=> Yii::$app->request->get('id')])->label(false); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'type')->hiddenInput(['readonly' => true,'maxlength' => true, 'value' => Yii::$app->request->get('type')])->label(false) ?>
        </div>

        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'initial_ward_code')->dropDownList($ward_code, 
             ['prompt'=> Yii::t('app','Please select ward code')]
            ) ?> -->

            <?= $form->field($model, 'initial_ward_code')->widget(kartik\select2\Select2::classname(), [
                'data' => $ward_code,
                'options' => ['placeholder' => Yii::t('app','Please select ward code'), 'id' => 'initial_ward_code',],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'tags' => true,
                ],
            ]); ?>
        </div>

        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'initial_ward_class')->dropDownList($ward_class, 
             ['prompt'=> Yii::t('app','Please select ward class')]
            ) ?> -->

            <?= $form->field($model, 'initial_ward_class')->widget(kartik\select2\Select2::classname(), [
                'data' => $ward_class,
                'options' => ['placeholder' => Yii::t('app','Please select ward class'), 'id' => 'initial_ward_class',],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'tags' => true,
                ],
            ]); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'entry_datetime')->widget(DateTimePicker::classname(),[
            'options' => ['class' => 'entry_datetime'],
            'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],
            'pluginEvents' => [
                
            ],])?>
        </div>

        <div class="col-sm-6">
           <?= $form->field($model, 'reference')->textInput(['maxlength' => true]) ?> 
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'medical_legal_code')->textInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'reminder_given')->textInput(['disabled' => true, ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_nric')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_phone_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?php
        if($model->initial_ward_class == "UNKNOWN" || $model->initial_ward_code == "UNKNOWN" || 
            $model->initial_ward_class == null || $model->initial_ward_code == null){
            echo "<span class='badge badge-danger'>".Yii::t('app','Initial Ward Code and Initial Ward Class cannot be blank')."</span> <br/><br/>";
        }
        ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-success']) ?>
        <!-- <?= Html::submitButton(Yii::t('app','Save & Print All Forms'), ['class' => 'btn btn-success' , 'name' => 'actionPrint', 'value' => 'submit1']) ?> -->
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
<div>
    <?= Html::a(Yii::t('app', 'Registration Form'), ['/patient_admission/print1', 'rn' => Yii::$app->request->get('rn')], ['class'=>'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Charge Sheet'), ['/patient_admission/print2', 'rn' => Yii::$app->request->get('rn')], ['class'=>'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Case History Sheet'), ['/patient_admission/print3', 'rn' => Yii::$app->request->get('rn')], ['class'=>'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Sticker'), ['/patient_admission/print4', 'rn' => Yii::$app->request->get('rn')], ['class'=>'btn btn-success']) ?>
</div>
<br/>

<!-- If the flash message existed, show it  -->
<?php if(Yii::$app->session->hasFlash('msg')):?>
<div id="flashError">
    <?= Yii::$app->session->getFlash('msg') ?>
</div>
<?php endif; ?>