<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;
    
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
            <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){ ?><?= $form->field($model, 'name')->textInput(['maxlength' => true,'readonly' => true]);} 
            else { ?><?= $form->field($model, 'name')->textInput(['maxlength' => true]);}?>
        </div>
        <div class="col-sm-6">
            <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?><?= $form->field($model, 'nric')->textInput(['maxlength' => true,'readonly' => true, 'id' => 'nric',
             'value' => Yii::$app->request->get('ic')]);}
             else { ?><?= $form->field($model, 'nric')->textInput(['maxlength' => true, 'id' => 'nric',
                'value' => Yii::$app->request->get('ic')]);}?>
        </div>
        <div class="col-sm-6">
            <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?>
                <?= $form->field($model, 'DOB')->widget(DatePicker::classname(),[
                'options' => ['id' => 'DOB', 'disabled' => 'disabled'],
                'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd'],
                'pluginEvents' => [
                ],]);
            }
            else{?>
                <?= $form->field($model, 'DOB')->widget(DatePicker::classname(),[
                    'options' => ['id' => 'DOB'],
                    'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd'],
                    'pluginEvents' => [
                        
                    ],]);
            }?>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm">
                    <?= $form->field($model, 'age')->textInput(['readonly' => true,'maxlength' => true,
                        'id' => 'age', 'value' => $model->getAgeFromDatePicker()]);?>
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
                'options' => ['placeholder' => Yii::t('app','Please select race'), 'disabled' => 'disabled','id' => 'race',],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                ],
            ]);
            }
            else{?>
                <?= $form->field($model, 'race')->widget(kartik\select2\Select2::classname(), [
                    'data' => $race,
                    'options' => ['placeholder' => Yii::t('app','Please select race'), 'id' => 'race',],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],
                ]); 
            }?>
        </div>
        
        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'nationality')->dropDownList($countries, 
                    ['prompt'=> Yii::t('app','Please select nationality'),'maxlength' => true]) ?> -->

                <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?>
                    <?= $form->field($model, 'nationality')->widget(kartik\select2\Select2::classname(), [
                    'data' => $countries,
                    'options' => ['placeholder' => Yii::t('app','Please select nationality'), 'id' => 'nationality','disabled' => 'disabled'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],
                    ]);
                }
                else{?>
                    <?= $form->field($model, 'nationality')->widget(kartik\select2\Select2::classname(), [
                        'data' => $countries,
                        'options' => ['placeholder' => Yii::t('app','Please select nationality'), 'id' => 'nationality',],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => true,
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
        <?php if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true,'readonly' => true]);}
         else {
            ?> <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]);
         }?>
        </div>

        <div class="col-sm-6">
        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'address1')->textInput(['maxlength' => true,'readonly' => true]);}
        else {?> <?= $form->field($model, 'address1')->textInput(['maxlength' => true]);}?>
        <?php
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'address2')->textInput(['maxlength' => true,'readonly' => true]);}
        else {?> <?= $form->field($model, 'address2')->textInput(['maxlength' => true]);}?>
        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'address3')->textInput(['maxlength' => true,'readonly' => true]);}
        else {?> <?= $form->field($model, 'address3')->textInput(['maxlength' => true]);}?>
         
        </div>
        
        <div class="col-sm-6">
        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'email')->textInput(['maxlength' => true,'readonly' => true]);}
        else {?> <?= $form->field($model, 'email')->textInput(['maxlength' => true]);}?>

        <?php 
        if(Yii::$app->controller->action->id == "guest_printer_dashboard"){?> <?= $form->field($model, 'job')->textInput(['maxlength' => true,'readonly' => true]);}
        else {?> <?= $form->field($model, 'job')->textInput(['maxlength' => true]);}?>

        </div>
    </div>

</div>

<div class="form-group">
<?php if(Yii::$app->controller->action->id != "guest_printer_dashboard"){?><?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-outline-primary align-self-start', 'id' => 'update']);} ?>
</div>

<?php kartik\form\ActiveForm::end(); ?>

</div>