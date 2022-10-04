<?php

use app\models\Patient_admission;
use app\models\Cancellation;
use yii\bootstrap4\Html;
use kartik\datetime\DateTimePicker;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use app\models\Bill;
use app\models\DateFormat;
use app\models\Patient_information;
use app\models\Receipt;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
.disabled-link {
    pointer-events: none;
}
</style>


<div class="patient-admission-form">
    <!-- If the flash message existed, show it  -->
    <?php if(Yii::$app->session->hasFlash('msg')):?>
    <div id="flashError">
        <?= Yii::$app->session->getFlash('msg') ?>
    </div>
    <?php endif; ?>

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

    $row_receipt = (new \yii\db\Query())
    ->from('patient_admission')
    ->where(['rn' => Yii::$app->request->get('rn')])
    ->one();

    // $rows_patient_information = (new \yii\db\Query())
    // ->select('*')
    // ->from('patient_information')
    // ->all();
    // $all_Nric = array();
    // foreach( $rows_patient_information as $row){
    //     $all_Nric[$row['nric']] = $row['nric'] ;
    //   }  

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
    $patient_uid = Patient_admission::findone(['rn' => Yii::$app->request->get('rn')])->patient_uid;
    $patient_nric = Patient_information::findOne(['patient_uid' => $patient_uid])->nric;


    // foreach($rows_patient_admission as $row_patient_admission){
    //     if(empty($ward_class[$row_patient_admission['initial_ward_class']])){
    //         $ward_class[$row_patient_admission['initial_ward_class']] = $row_patient_admission['initial_ward_class'];
    //     }            
    // }


    // $form = kartik\form\ActiveForm::begin([
    //     'id' => 'patient-admission-form',
    //     'type' => 'vertical',
    //    // 'action' => ['patient_admission/transfer'],
    //     'fieldConfig' => [
    //         'template' => "{label}\n{input}\n{error}",
    //         'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
    //     ],
    // ]); 
    // 
    // <?=$form->field($modelpatient, 'nric')->widget(kartik\select2\Select2::classname(), [
    //     'data' => $all_Nric,
    //     'options' => ['placeholder' => Yii::t('app','Please select NRIC'), 'id' => 'nric',],
    //     'pluginOptions' => [
    //         'allowClear' => true,
    //         // 'tags' => true,
    //     ],
    // ])->label(Yii::t('app','Transfer To New Patient'));
    // <?= Html::submitButton(Yii::t('app','Yes'), ['class' => 'btn btn-success']) 

    // <?php kartik\form\ActiveForm::end();

    $cancellation = Cancellation::findAll(['cancellation_uid' => Yii::$app->request->get('rn')]);
    $billModel = new Bill();
    $checkDate = $billModel->isGenerated(Yii::$app->request->get('rn'));
    $receiptModel = new Receipt();
    $checkDeposit = $receiptModel->isGenerated(Yii::$app->request->get('rn'));
    if(!empty($cancellation) || $checkDate != false){ // || $checkDeposit != false
        $disabled = true;
        $linkDisabled = 'disabled-link';
    }
    else{
        $disabled = false;
        $linkDisabled = '';
    }

    $url = Url::toRoute(['/patient_admission/update', 'rn'=> Yii::$app->request->get('rn')]);

    $model->entry_datetime = DateFormat::convert($model->entry_datetime, 'datetime');
   
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

        <!-- <div class="col-sm-6">
            <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false) ?>
        </div> -->
        <!-- <div class="col-sm-6">
            <?= $form->field($model, 'type')->hiddenInput(['readonly' => true,'maxlength' => true, 'value' => Yii::$app->request->get('type')])->label(false) ?>
        </div> -->

        <div class="col-sm-6">
            <?= $form->field($model, 'initial_ward_code')->dropDownList($ward_code,
                [
                    'id' => 'initial_ward_code',
                    'prompt'=> Yii::t('app','Please select ward code'),
                    'disabled' => $disabled,
                    "change" => "function() { 
                        getFocusID('initial_ward_code');
                        submitPatientAdmissionForm();
                    }",
                ]
            ) ?>

            <!-- <?= $form->field($model, 'initial_ward_code')->widget(kartik\select2\Select2::classname(), [
                'data' => $ward_code,
                'options' => ['placeholder' => Yii::t('app','Please select ward code'), 'id' => 'initial_ward_code', 'disabled' => $disabled],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'tags' => true,
                ],
                'pluginEvents' => [
                    "change" => "function() { 
                        getFocusID('initial_ward_code');
                        submitPatientAdmissionForm();
                    }",
                ]
            ]); ?> -->
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'initial_ward_class')->dropDownList($ward_class, 
                [
                    'id' => 'initial_ward_class',
                    'prompt'=> Yii::t('app','Please select ward class'),
                    'disabled' => $disabled,
                    "change" => "function() { 
                        getFocusID('initial_ward_class');
                        submitPatientAdmissionForm();
                    }",
                ]
            ) ?>

            <!-- <?= $form->field($model, 'initial_ward_class')->widget(kartik\select2\Select2::classname(), [
                'data' => $ward_class,
                'options' => ['placeholder' => Yii::t('app','Please select ward class'), 'id' => 'initial_ward_class', 'disabled' => $disabled],
                'pluginOptions' => [
                    'allowClear' => true,
                    // 'tags' => true,
                ],
                'pluginEvents' => [
                    "change" => "function() { 
                        getFocusID('initial_ward_class');
                        submitPatientAdmissionForm();
                    }",
                ]
            ]); ?> -->
        </div>

        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'entry_datetime')->widget(DateTimePicker::classname(),[
            'options' => ['class' => 'entry_datetime', 'disabled' => $disabled],
            'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],
            'pluginEvents' => [
                "change" => "function() { 
                    getFocusID('patient_admission-entry_datetime');
                    submitPatientAdmissionForm();
                }",
            ],])?> -->

            <?= $form->field($model, 'entry_datetime')->textInput([
                'maxlength' => true,
                'class' => 'entry_datetime',
                'disabled' => $disabled,
                'onfocusout' => 'submitPatientAdmissionForm();',
                'onfocus' => "getFocusID('patient_admission-entry_datetime');",
            ]);?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'reference')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => "testing('{$url}')", 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'medical_legal_code')->textInput(['disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
        </div>

    </div>
<hr/>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_name')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_nric')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_address1')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
            <?= $form->field($model, 'guarantor_address2')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
            <?= $form->field($model, 'guarantor_address3')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'guarantor_phone_number')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
            <?= $form->field($model, 'guarantor_email')->textInput(['maxlength' => true, 'disabled' => $disabled, 'onfocusout' => ' submitPatientAdmissionForm();', 'onfocus' => 'getFocusID("")']) ?>
        </div>
    </div>

    <div class="form-group">
        <?php
        if($model->initial_ward_class == "UNKNOWN" || $model->initial_ward_code == "UNKNOWN" || 
            $model->initial_ward_class == null || $model->initial_ward_code == null){
            echo "<span class='badge badge-danger'>".Yii::t('app','Initial Ward Code and Initial Ward Class cannot be blank')."</span> <br/><br/>";
        }
        ?>

        <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-success', 'disabled' => $disabled, 'style' => 'display: none;']) ?>
        <?php if(empty($patient_nric)){
            ?><?= Html::button(Yii::t('app','Transfer'), ['class' => 'btn btn-info', 'id' => 'btnTransfer', 'disabled' => $disabled])?>
        <?php } ?>
        <?= Html::button(Yii::t('app','Change Registration Number'), ['class' => 'btn btn-primary', 'id' => 'btnChange', 'disabled' => $disabled])?>
        <?php
           // Modal for transfer patient
            Modal::begin([
                'title'=>'<h4>'.Yii::t('app','Transfer To New Patient').'</h4>',
                'id'=>'modal',
                'size'=>'modal-lg',
            ]);
            $form = kartik\form\ActiveForm::begin([
                    'id' => 'patient-admission-form',
                    'type' => 'vertical',
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                        'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                    ],
                ]); 
                
            ?>
        <div id='modalContent'>
            <?= $form->field($modelpatient, 'nric')->textInput([ 'autocomplete' =>'off', 'value' => ""]);?>
            <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-success', 'name' => 'transfer', 'value' => 'transfer', 'disabled' => $disabled])?>
        </div>
        <?php
            kartik\form\ActiveForm::end();
            Modal::end();

            // Modal for change RN
            Modal::begin([
                'title'=>'<h4>'.Yii::t('app','Change To New Registration Number').'</h4>',
                'id'=>'modal_change',
                'size'=>'modal-lg',
             ]);
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
        <div id='modalContent'>
            <?= $form->field($model_change_rn, 'rn')->textInput([ 'autocomplete' =>'off', 'value' => ""]);?>
            <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-success', 'name' => 'change', 'value' => 'change', 'disabled' => $disabled])?>
        </div>
        <?php
             kartik\form\ActiveForm::end();
             Modal::end();
        ?>
        <!-- <?= Html::submitButton(Yii::t('app','Save & Print All Forms'), ['class' => 'btn btn-success' , 'name' => 'actionPrint', 'value' => 'submit1']) ?> -->
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
<div>
    <?= Html::a(Yii::t('app', 'Registration Form'), ['/patient_admission/print1', 'rn' => Yii::$app->request->get('rn')], ['class'=>"btn btn-success {$linkDisabled}", 'disabled' => $disabled]) ?>
    <?= Html::a(Yii::t('app', 'Charge Sheet'), ['/patient_admission/print2', 'rn' => Yii::$app->request->get('rn')], ['class'=>"btn btn-success {$linkDisabled}", 'disabled' => $disabled]) ?>
    <?= Html::a(Yii::t('app', 'Case History Sheet'), ['/patient_admission/print3', 'rn' => Yii::$app->request->get('rn')], ['class'=>"btn btn-success {$linkDisabled}", 'disabled' => $disabled]) ?>
    <?= Html::a(Yii::t('app', 'Sticker'), ['/patient_admission/print4', 'rn' => Yii::$app->request->get('rn')], ['class'=>"btn btn-success {$linkDisabled}", 'disabled' => $disabled]) ?>
</div>
<br />

<?php
$this->registerJs(
"$('#btnTransfer').on('click',function () {
    $('#modal').modal('show')
        .find('#modalContent')
        .load($(this).attr('value'));
    });"
);

$this->registerJs(
    "$('#btnChange').on('click',function () {
        $('#modal_change').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
        });"
    );
?>

<script>
function testing(url) {
    var wardForm = $('#patient-admission-form');
    var formData = wardForm.serialize();

    $.ajax({
        url: wardForm.attr("action"),
        type: wardForm.attr("method"),
        data: formData,

        success: function(data) {
            // $(wardForm).trigger('reset');
            // console.log(wardForm.attr("method"));
        },
    });
}

var focusID = '';

function submitPatientAdmissionForm() {
    var form = $('#patient-admission-form');
    var formData = $('#' + focusID).serialize();

    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,

        success: function(data) {
            // $.pjax.reload({container: '#pjax-patient-admission-form'});
        },
    });
}

function getFocusID(id) {
    if (id == '') {
        focusID = document.activeElement.id;
    } else {
        focusID = id;
    }
}

function matchAdmission(params, data) {
    // Search first letter
    // params.term = params.term || '';
    // var code = data.text.split(" - ");
    // console.log(indexOf(params.term.toUpperCase()));
    // if (code[0].toUpperCase().find(params.term.toUpperCase()) == 0) {
    //     return data;
    // }
    // return null;

    // Search code 
    // If search is empty we return everything
    if ($.trim(params.term) === '') return data;

    // Compose the regex
    var regex_text = '.*';
    regex_text += (params.term).split('').join('.*');
    regex_text += '.*'

    // Case insensitive
    var regex = new RegExp(regex_text, "i");

    // Splite code and name
    var code = data.text.split(" - ");

    // If no match is found we return nothing
    if (!regex.test(code[0])) {
        return null;
    }

    // Else we return everything that is matching
    return data;
}
</script>

<?php 
$script = <<< JS
$(document).ready(function() {
    $('#initial_ward_code').select2({
        placeholder: 'Please select ward code',
        allowClear: true,
        width: '100%',
        matcher: function(params, data) {
            return matchAdmission(params, data);
        },
    });
});

$(document).ready(function() {
    $('#initial_ward_class').select2({
        placeholder: 'Please select ward class',
        allowClear: true,
        width: '100%',
        matcher: function(params, data) {
            return matchAdmission(params, data);
        },
    });
});
JS;
$this->registerJS($script);
?>