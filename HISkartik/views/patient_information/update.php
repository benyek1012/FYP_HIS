<?php

use app\models\Patient_information;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\widgets\Pjax;
    
/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs(
    "$('#DOB').change(function() {
        var dob = $(this).val();
        var id = '".Yii::$app->request->get('id')."';
        $.get('". Url::toRoute(['/patient_information/dob'])."', {dob : dob, id : id}, function(data){
            var data = $.parseJSON(data);
            $('#age').attr('value', data);
        });
    });"
);

$this->registerJs(
    "$('#btn_dob').click(function() {
        var nric = $('#nric').val();
        var id = '".Yii::$app->request->get('id')."';
        $.get('". Url::toRoute(['/patient_information/load_dob_from_ic'])."', {nric : nric, id : id}, function(data){
            var data = $.parseJSON(data);
            $('#DOB').attr('value', data);
        });
    });"
);

$url = Url::toRoute(['/patient_information/getdob']);
$urlGender = Url::toRoute(['/patient_information/gender']);
$urlAge = Url::toRoute(['/patient_information/dob']);
$urlNationality = Url::toRoute(['/patient_information/nationality']);
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
        ->orderBy('length(code) ASC, code ASC')
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
        ->orderBy('length(code) ASC, code ASC')
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

    Pjax::begin(['id' => 'pjax-patient-information-form']);
    $form = kartik\form\ActiveForm::begin([
        'action' => ['patient_information/update', 'id' =>  $model->patient_uid],
        'options' => ['data-pjax' => true],
        'id' => 'patient-information-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);     
?>

    <div class="row">
        <div class="col-sm-6">
            <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){ ?><?= $form->field($model, 'name')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("");']);} 
            else { ?><?= $form->field($model, 'name')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}?>
        </div>
        <div class="col-sm-6">
            <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?><?= $form->field($model, 'nric')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'id' => 'nric',
             'value' => Yii::$app->request->get('ic'), 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}
             else { ?><?= $form->field($model, 'nric')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'id' => 'nric',
                'value' => Yii::$app->request->get('ic'), 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}?>
        </div>
        <div class="col-sm-6">
            <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?>
                <?= 
                // $form->field($model, 'DOB')->widget(DatePicker::classname(),[
                // 'options' => ['id' => 'DOB', 'disabled' => 'disabled', 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")'],
                // 'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd'],
                // 'pluginEvents' => [
                // ],]);

                $form->field($model, 'DOB')->textInput([
                    'autocomplete' =>'off', 
                    'maxlength' => true,
                    'id' => 'DOB', 
                    'disabled' => 'disabled',
                    'onfocusout' => 'submitPatientInformationForm();',
                    'onfocus' => 'getFocusID("")',
                    'placeholder' => 'yyyy-mm-dd', 
                ]);
            }
            else{?>
                <?= 
                // $form->field($model, 'DOB')->widget(DatePicker::classname(),[
                //     'options' => ['id' => 'DOB', 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")'],
                //     'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd'],
                //     'pluginEvents' => [
                //     ],]);

                $form->field($model, 'DOB')->textInput([
                    'autocomplete' =>'off', 
                    'maxlength' => true,
                    'id' => 'DOB', 
                    'onfocusout' => 'submitPatientInformationForm();',
                    'onfocus' => 'getFocusID("")',
                    'placeholder' => 'yyyy-mm-dd', 
                ]);
            }?>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm">
                    <?= $form->field($model, 'age')->textInput(['autocomplete' =>'off', 'readonly' => true,'maxlength' => true,
                        'id' => 'age', 'value' => $model->getAgeFromDatePicker(), 'tabindex' => '-1']);?>
                </div>
                <div class="col-sm align-self-center" style="padding-top:16px">
                    <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?>
                    <?= Html::button(Yii::t('app','Load DOB'), ['class' => 'btn btn-outline-primary', 'disabled' => 'disabled','id' => "btn_dob"]);}
                    else {
                        ?> <?= Html::button(Yii::t('app','Load DOB'), ['class' => 'btn btn-outline-primary', 'id' => "btn_dob"]);
                    }?>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
        <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?>            
            <?= $form->field($model, 'race')->widget(kartik\select2\Select2::classname(), [
                'data' => $race,
                'options' => ['placeholder' => Yii::t('app','Please select race'), 'disabled' => 'disabled','id' => 'race'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                ],
                'pluginEvents' => [
                    "change" => "function() { 
                        getFocusID('race');
                        submitPatientInformationForm();
                    }",
                ]
            ]); 
            }
            else{?>
                <?= $form->field($model, 'race')->widget(kartik\select2\Select2::classname(), [
                    'data' => $race,
                    'options' => ['placeholder' => Yii::t('app','Please select race'), 'id' => 'race'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],
                    'pluginEvents' => [
                        "change" => "function() { 
                            getFocusID('race');
                            submitPatientInformationForm();
                        }",
                    ]
                ]);  
            }?>
        </div>
        
        <div class="col-sm-6">
                <?php  if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?>
                    <?= $form->field($model, 'nationality')->widget(kartik\select2\Select2::classname(), [
                    'data' => $countries,
                    'options' => ['placeholder' => Yii::t('app','Please select nationality'), 'id' => 'nationality','disabled' => 'disabled'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],
                    'pluginEvents' => [
                        "change" => "function() { 
                            getFocusID('nationality');
                            submitPatientInformationForm();
                        }",
                    ]
                    ]);
                }
                else{?>
                    <?= $form->field($model, 'nationality')->widget(kartik\select2\Select2::classname(), [
                        'data' => $countries,
                        'options' => ['placeholder' => Yii::t('app','Please select nationality'), 'id' => 'nationality'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => true,
                        ],
                        'pluginEvents' => [
                            "change" => "function() { 
                                getFocusID('nationality');
                                submitPatientInformationForm();
                            }",
                        ],
                    ]);
                }?>
        </div>
        <div class="col-sm-6">
        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?><?= $form->field($model, 'sex')->radioList($sex, ['custom' => true,'inline' => true,'disabled' => true]);}
        else {?> <?= $form->field($model, 'sex')->radioList($sex, ['custom' => true, 'inline' => true]);}?>
        </div>

        <div class="col-sm-6">
        <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'phone_number')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}
         else {
            ?> <?= $form->field($model, 'phone_number')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);
         }?>
        </div>

        <div class="col-sm-6">
        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'address1')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}
        else {?> <?= $form->field($model, 'address1')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}?>
        <?php
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'address2')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}
        else {?> <?= $form->field($model, 'address2')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")'])->label(false);}?>
        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'address3')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}
        else {?> <?= $form->field($model, 'address3')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")'])->label(false);}?>
         
        </div>
        
        <div class="col-sm-6">
        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'email')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}
        else {?> <?= $form->field($model, 'email')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}?>

        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'job')->textInput(['autocomplete' =>'off', 'maxlength' => true,'readonly' => true, 'onfocusout' => 'submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}
        else {?> <?= $form->field($model, 'job')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'onfocusout' => ' submitPatientInformationForm();', 'onfocus' => 'getFocusID("")']);}?>

        </div>
    </div>

</div>

<!-- <div class="form-group">
<?php if(Yii::$app->controller->action->id != "guest_printer_dashboard"){?><?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-outline-primary align-self-start', 'id' => 'update']);} ?>
</div> -->

<?php kartik\form\ActiveForm::end(); ?>
<?php Pjax::end(); ?>

</div>

<script>
    var focusID = '';

    function submitPatientInformationForm(){
        var form = $('#patient-information-form');
        var formData = $('#'+focusID).serialize();

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"), 
            data: formData,

            success: function (data) {
                // if(data[0] == "s"){
                    // $.pjax.reload({container: '#pjax-patient-information-form'});
                    $.get('<?php echo $url ?>', {id : '<?php echo Yii::$app->request->get('id') ?>'}, function(data){
                        var data = $.parseJSON(data);
                        $('#DOB').attr('value', data.DOB);
                    });

                    $.get('<?php echo $urlGender ?>', {nric: $('#nric').val(), id : '<?php echo Yii::$app->request->get('id') ?>'}, function(data){
                        var data = $.parseJSON(data);

                        if($('#patient-information-sex--0').val() == data){
                            $('#patient-information-sex--0').prop('checked', true);
                        }

                        if($('#patient-information-sex--1').val() == data){
                            $('#patient-information-sex--1').prop('checked', true);
                        }
                    });

                    $.get('<?php echo $urlAge ?>', {dob : $('#DOB').val(), id : '<?php echo Yii::$app->request->get('id') ?>'}, function(data){
                        var data = $.parseJSON(data);
                        $('#age').attr('value', data);
                    });

                    // $.get('<?php echo $urlNationality ?>', {id : '<?php echo Yii::$app->request->get('id') ?>'}, function(data){
                    //     var data = $.parseJSON(data);
                    //     console.log(data);
                    //     // $("#nationality").val(data).trigger('change').trigger('close');
                    //     if(data == true){
                    //         $('#nationality').val("Malaysia");
                    //         $('#nationality').select2({
                    //             placeholder: 'Please select nationality',
                    //             allowClear: true,
                    //             tags: true,
                    //             width: '100%',
                    //             minimumInputLength: 2,
                    //             // matcher: function(params, data) {
                    //             //     return matchInformation(params, data);
                    //             // },
                    //         });
                    //     }
                    // });
                // }
            },
        });
    }

    function getFocusID(id) {
        if(id == ''){
            focusID = document.activeElement.id;
        }
        else{
            focusID = id;
        }
    }

    function matchInformation(params, data) {
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
$("input[type='radio']").on('change', function() {
    getFocusID("");
    submitPatientInformationForm();
});

$(document).ready(function() {
    $('#race').select2({
        placeholder: 'Please select race',
        allowClear: true,
        tags: true,
        width: '100%',
        minimumInputLength: 2,
        // matcher: function(params, data) {
        //     return matchInformation(params, data);
        // },
    });
});

$(document).ready(function() {
    $('#nationality').select2({
        placeholder: 'Please select nationality',
        allowClear: true,
        tags: true,
        width: '100%',
        minimumInputLength: 2,
        // matcher: function(params, data) {
        //     return matchInformation(params, data);
        // },
    });
});
JS;
$this->registerJS($script);
?>