<?php

use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Patient_admission;
use app\models\Bill;
use app\models\Ward;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */


    // if (Yii::$app->params['printerstatus'] == "false"){
    //     var_dump('false');
    //     exit();
    // }
    // else{
    //     var_dump('treu');
    //     exit();
    // }
$url = Url::toRoute(['bill/refresh']);

$admission_model = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$modelWardDate = Ward::find()->where(['bill_uid' => Yii::$app->request->get('bill_uid')])->orderby(['ward_start_datetime' => SORT_ASC])->all(); 
         
if($modelWardDate != null){
    $modelDate = Ward::find()->where(['between', 'ward_start_datetime', $modelWard[0]->ward_start_datetime, $modelWard[0]->ward_end_datetime])->all();

}

if(empty( Yii::$app->request->get('bill_uid')))
{ 
    $initial_ward_class = $admission_model->initial_ward_class;
    $initial_ward_code = $admission_model->initial_ward_code;
}
else{
    $rows = (new \yii\db\Query())
    ->select(['*'])
    ->from('patient_admission')
    ->where(['rn' => Yii::$app->request->get('rn')])
    ->all();
    foreach($rows as $row){
        $initial_ward_class = $row['initial_ward_class'];
        $initial_ward_code = $row['initial_ward_code'];
    }  
}

$row_bill = (new \yii\db\Query())
->from('bill')
->where(['bill_uid' => Yii::$app->request->get('bill_uid')])
->one();


$isGenerated = false;
$isFree = false;
$isPrinted = false;
$finalFee = 0.00;

if(!empty($row_bill))
{
   $isGenerated = (new Bill()) -> isGenerated($row_bill['rn']);
   $isFree =  (new Bill()) -> isFree($row_bill['rn']);
   $isPrinted =  (new Bill()) -> isPrinted($row_bill['rn']);
   $finalFee =  (new Bill()) -> getFinalFee($row_bill['rn']);
}

$rows = (new \yii\db\Query())
->select('*')
->from('lookup_status')
->all();

$dayly_ward_cost = "";
$status_code = array();
$unit_class = "";
foreach($rows as $row){
  $status_code[$row['status_code']] = $row['status_code'] . ' - ' . $row['status_description'] ;
  if($initial_ward_class == "1a"){
      $unit_class = "1";
  }
  else if($initial_ward_class == "1b"){
    $unit_class = "1";
  }
  else if($initial_ward_class == "1c"){
    $unit_class = "1";
  }
  else if($initial_ward_class == "2"){
    $unit_class = "2";
  }
  else if($initial_ward_class == "3"){
    $unit_class = "3";
  }
}  


$ward_class = array(
    "1a" =>'1a', 
    "1b" =>'1b', 
    "1c" =>'1c', 
    "2" =>'2', 
    "3" =>'3', 
);

$rows = (new \yii\db\Query())
->select('*')
->from('lookup_department')
->all();

$department_code = array();
foreach($rows as $row){
    $department_code[$row['department_code']] = $row['department_code'] . ' - ' . $row['department_name'] ;
} 

$rows_nurse = (new \yii\db\Query())
->select('*')
->from('lookup_general')
->where(['category'=> 'Nurse'])
->all();

$rows_bill = (new \yii\db\Query())
->select('*')
->from('bill')
->where(['rn'=> Yii::$app->request->get('rn')])
->all();

$nurse_responsible = array();
foreach($rows_nurse as $row_nurse){
    $nurse_responsible[$row_nurse['code']] = $row_nurse['code'] . ' - ' . $row_nurse['name'] . ' [ ' .  $row_nurse['long_description'] . ' ]';  

}  

foreach($rows_bill as $row_bill){
    if(empty($nurse_responsible[$row_bill['nurse_responsible']])){
        $nurse_responsible[$row_bill['nurse_responsible']] = $row_bill['nurse_responsible'];
    }            
}

$rows = (new \yii\db\Query())
->select('*')
->from('patient_admission')
->where(['rn'=> Yii::$app->request->get('rn')])
->all();

$ward_code = "";
foreach($rows as $row){
    $ward_code = $row['initial_ward_code'];
} 

$rows = (new \yii\db\Query())
->select('*')
->from('lookup_ward')
->where(['ward_code'=> $ward_code])
->all();

$ward_name = "";
foreach($rows as $row){
    $ward_name = $row['ward_name'];
} 


$rows_ward = (new \yii\db\Query())
->select('ward_code')
->from('lookup_ward')
->all();

$wardcode = array();
foreach($rows_ward as $row_ward){
  $wardcode[$row_ward['ward_code']] = $row_ward['ward_code'];
} 

$rows = (new \yii\db\Query())
->select('*')
->from('lookup_treatment')
->all();

$treatment_code = array();
$unit_cost = "";
foreach($rows as $row){
    $treatment_code[$row['treatment_code']] = $row['treatment_code'];
    if($unit_class == "1"){
        $unit_cost = $row['class_1_cost_per_unit'];
    }
    else if($unit_class == "2"){
        $unit_cost = $row['class_2_cost_per_unit'];
    }
    else if($unit_class == "3"){
        $unit_cost = $row['class_3_cost_per_unit'];
    }
} 

$billuid = Base64UID::generate(32);

$free = array(
    0 => Yii::t('app', 'No'), //false
    1 => Yii::t('app', 'Yes'),    //true
);

$this->registerJs(
    "$('#statusCode').change(function() {
        var statusCode = $(this).val();
        var wardClass = $('#wardClass :selected').text();
        $.get('". Url::toRoute(['/bill/status'])."', {status : statusCode}, function(data){
            var data = $.parseJSON(data);
            $('#status_des').attr('value', data.status_description);
            if(wardClass == '1a') $('#ward_cost').attr('value', data.class_1a_ward_cost);
            else if(wardClass == '1b') $('#ward_cost').attr('value', data.class_1b_ward_cost);
            else if(wardClass == '1c') $('#ward_cost').attr('value', data.class_1c_ward_cost);
            else if(wardClass == '2') $('#ward_cost').attr('value', data.class_2_ward_cost);
            else if(wardClass == '3') $('#ward_cost').attr('value', data.class_3_ward_cost);
        });
    });"
);

$this->registerJs(
    "$('#wardClass').change(function() {
        var wardClass = $(this).val();
        var statusCode = $('#statusCode').val();
        $.get('". Url::toRoute(['/bill/status'])."', {status : statusCode}, function(data){
            var data = $.parseJSON(data);
        
            if(wardClass == '1a') $('#ward_cost').attr('value', data.class_1a_ward_cost);
            else if(wardClass == '1b') $('#ward_cost').attr('value', data.class_1b_ward_cost);
            else if(wardClass == '1c') $('#ward_cost').attr('value', data.class_1c_ward_cost);
            else if(wardClass == '2') $('#ward_cost').attr('value', data.class_2_ward_cost);
            else if(wardClass == '3') $('#ward_cost').attr('value', data.class_3_ward_cost);
            
            $('.treatmentCode', document).each(function(index, item){
                var billClass = $('#wardClass').val();
                var treatmentCode = this.value;
                $.get('". Url::toRoute(['/bill/treatment'])."', {treatment : treatmentCode}, function(data){
                    var data = $.parseJSON(data);
                    $('#treatment_details-'+index+'-treatment_name').attr('value', data.treatment_name);
                    if(billClass == '1a' || billClass == '1b' || billClass == '1c'){
                        $('#treatment_details-'+index+'-item_per_unit_cost_rm').attr('value', data.class_1_cost_per_unit);
                    }
                    if(billClass == '2'){
                        $('#treatment_details-'+index+'-item_per_unit_cost_rm').attr('value', data.class_2_cost_per_unit);
                    }
                    if(billClass == '3'){
                        $('#treatment_details-'+index+'-item_per_unit_cost_rm').attr('value', data.class_3_cost_per_unit);
                    }
                    calculateItemCost();
                });
            });
        });        
    });"
);

$this->registerJs(
    "$('#departmentCode').change(function() {
        var departmentCode = $(this).val();
        $.get('". Url::toRoute(['/bill/department'])."', {department : departmentCode}, function(data){
            var data = $.parseJSON(data);
            $('#departmentName').attr('value', data.department_name);
        });
    });"
);

// $this->registerJs(
//     "$('.wardCode', document).each(function(index, item){
//           $(item).on('change', function() {
//             var wardCode = this.value;
//             $.get('". Url::to(['/bill/ward'])."', {ward : wardCode}, function(data){
//                 var data = $.parseJSON(data);
//                 $('#ward-'+index+'-ward_name').attr('value', data.ward_name);
//             });
//         });
//     });
//     "
// );

// $this->registerJs(
//     "$('.treatmentCode', document).each(function(index, item){
//         var billClass = $('#wardClass').val();
//         $(item).on('change', function() {
//             var treatmentCode = this.value;
//             $.get('". Url::to(['/bill/treatment'])."', {treatment : treatmentCode}, function(data){
//                 var data = $.parseJSON(data);
//                 $('#treatment_details-'+index+'-treatment_name').attr('value', data.treatment_name);
//                 if(billClass == '1a' || billClass == '1b' || billClass == '1c'){
//                     $('#treatment_details-'+index+'-item_per_unit_cost_rm').attr('value', data.class_1_cost_per_unit);
//                 }
//                 if(billClass == '2'){
//                     $('#treatment_details-'+index+'-item_per_unit_cost_rm').attr('value', data.class_2_cost_per_unit);
//                 }
//                 if(billClass == '3'){
//                     $('#treatment_details-'+index+'-item_per_unit_cost_rm').attr('value', data.class_3_cost_per_unit);
//                 }
//                 // calculateItemCost();
//             });
//         });
//     });
//     "
// );

// $this->registerJs(
//     "$('#addWardRow').on('click', function() { 
//         var countWard = $('#countWard').val();    
      
//         $.get('". Url::to(['/bill/wardRow'])."', {ward : countWard}, function(data){
//             // var data = $.parseJSON(data);
//             // $('#countWard').attr('value', data.length);
//             alert('asd')
//         });
//     });"
// );

// $this->registerJs(
//     "$('#addTreatmentRow').on('click', function() { 
//         var countTreatment = $('#countTreatment').val();    
       
//         $.get('". Url::to(['/bill/treatmentRow'])."', {treatment : countTreatment}, function(data){
//             var data = $.parseJSON(data);
//             $('#countTreatment').attr('value', data.length);
//         });
//     });"
// );

// $this->registerJs(
//     "$('.wardCode', document).each(function(index, item){
//         var billUid = $('#ward-bill-uid').val();
//         $.get('/bill/date', {bill_uid : billUid}, function(data){
//             var data = $.parseJSON(data);
//             if(data.length > 1){
//                 for(var i = 0; i < data.length; i++){
//                     $('#ward-'+i+'-ward_start_datetime').addClass('textColor');
//                     $('#ward-'+i+'-ward_end_datetime').addClass('textColor');
//                 }
//             }
//         });
//     });
//     "
// );

if(!empty($admission_model->initial_ward_code) && empty($modelWard->ward_code)){
    $this->registerJs(
        "$('.wardCode', document).each(function(index, item){
            var wardCode = this.value;

            $.get('". Url::toRoute(['/bill/ward'])."', {ward : wardCode}, function(data){
                var data = $.parseJSON(data);
                $('#ward-'+index+'-ward_name').attr('value', data.ward_name);
            });
        });
        "
    );
}

if(empty($print_readonly)) $print_readonly = false;

if($print_readonly)
{
    $this->registerJs(
        "$('#bill_details').CardWidget('collapse');
        $('#ward_div').CardWidget('collapse');
        $('#treatment_div').CardWidget('collapse');"
        
    );
}

$urlStatus = Url::toRoute(['/bill/status']);
$urlGenerate = Url::toRoute(['bill/generatebill', 'bill_uid' => Yii::$app->request->get('bill_uid')]);
?>

<style>
.textColor {
    color: red;
}
</style>

<div class="bill-form">

    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'bill-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

    <a name="bill">
        <div class="card" id="bill_details">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Billing Details');?></h3>
                <div class="card-tools">
                    <!-- Collapse Button -->
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                            class="fas fa-minus"></i></button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <?= $form->field($model, 'bill_uid')->hiddenInput(['readonly' => true, 'maxlength' => true, 'value' => $billuid])->label(false) ?>

                    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false) ?>

                    <div class="col-sm-6">
                        <!-- <?= $form->field($model, 'status_code')->dropDownList($status_code, ['id'=>'statusCode',
                    'prompt'=> Yii::t('app','Please select status code'),'maxlength' => true, 'disabled' => $print_readonly]) ?> -->

                        <?= $form->field($model, 'status_code')->widget(kartik\select2\Select2::classname(), [
                            'data' => $status_code,
                            'disabled' => $print_readonly,
                            'options' => ['placeholder' => Yii::t('app','Please select status code'), 'id' => 'statusCode',],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_description')->textInput(['maxlength' => true, 'id'=>'status_des', 'readonly' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?php if(empty( Yii::$app->request->get('bill_uid'))){ ?>
                        <!-- <?= $form->field($model, 'class')->dropDownList($ward_class, 
                            ['id'=>'wardClass','prompt'=> Yii::t('app','Please select ward class'), 'value' => $initial_ward_class]) ?> -->

                        <?= $form->field($model, 'class')->widget(kartik\select2\Select2::classname(), [
                                'data' => $ward_class,
                                'options' => ['placeholder' => Yii::t('app','Please select ward class'), 'id' => 'wardClass', 'value' => $initial_ward_class],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]); ?>
                        <?php }else{ ?>
                        <!-- <?= $form->field($model, 'class')->dropDownList($ward_class, 
                            ['id'=>'wardClass','prompt'=> Yii::t('app','Please select ward class'), 'disabled' => $print_readonly]) ?> -->

                        <?= $form->field($model, 'class')->widget(kartik\select2\Select2::classname(), [
                                'data' => $ward_class,
                                'disabled' => $print_readonly,
                                'options' => ['placeholder' => Yii::t('app','Please select ward class'), 'id' => 'wardClass',],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ]); ?>
                        <?php } ?>

                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true, 'id'=>'ward_cost',  
                                'readonly' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <!-- <?= $form->field($model, 'department_code')->dropDownList($department_code, ['id'=>'departmentCode',
                    'prompt'=> Yii::t('app','Please select department code'),'maxlength' => true, 'disabled' => $print_readonly]) ?> -->

                        <?= $form->field($model, 'department_code')->widget(kartik\select2\Select2::classname(), [
                            'data' => $department_code,
                            'disabled' => $print_readonly,
                            'options' => ['placeholder' => Yii::t('app','Please select department code'), 'id' => 'departmentCode',],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'department_name')->textInput(['maxlength' => true, 'id'=>'departmentName', 
                             'readonly' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <!-- <?= $form->field($model, 'is_free')->dropDownList($free, ['disabled' => $print_readonly]) ?> -->
                        <!-- <?= $form->field($model, 'is_free')->radioList($free, ['value' => 0, 'custom' => true, 'inline' => true, 'disabled' => $print_readonly]); ?> -->

                        <?php if(empty( Yii::$app->request->get('bill_uid'))){ ?>
                        <?= $form->field($model, 'is_free')->radioList($free, ['value' => 0, 'custom' => true, 'inline' => true, 'disabled' => $print_readonly]); ?>
                        <?php }else{ ?>
                        <?= $form->field($model, 'is_free')->radioList($free, ['custom' => true, 'inline' => true, 'disabled' => $print_readonly]); ?>
                        <?php } ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <!-- <?= $form->field($model, 'nurse_responsible')->textInput(['maxlength' => true, 'disabled' => $print_readonly]) ?> -->

                        <?= $form->field($model, 'nurse_responsible')->widget(kartik\select2\Select2::classname(), [
                            'data' => $nurse_responsible,
                            'disabled' => $print_readonly,
                            'options' => ['placeholder' => Yii::t('app','Please select nurse responsible'), 'id' => 'nurse_responsible',],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => true
                            ],
                        ]); ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'disabled' => $print_readonly]) ?>
                    </div>
                </div>
                <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                <?= Html::submitButton(Yii::t('app','Update'), ['name' => 'updateBill', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "getDailyWardCost('{$urlStatus}');"]) ?>
                <?php }else{ ?>
                <?= Html::submitButton(Yii::t('app','Save'), ['name' => 'saveBill', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "getDailyWardCost('{$urlStatus}');"]) ?>
                <?php } ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <?php kartik\form\ActiveForm::end(); ?>
    </a>

    <a name="ward">
        <div class="card" id="ward_div" style="display:none;">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Ward Details');?></h3>
                <div class="d-flex justify-content-end">
                    <?php
                    if(!empty($model))
                        echo "<div>". Yii::t('app','Total')." : ". (new Bill) -> getTotalWardCost(Yii::$app->request->get('bill_uid'))."&nbsp&nbsp&nbsp&nbsp&nbsp"."</div>";
                    ?>
                    <div class="card-tools">
                        <!-- Collapse Button -->
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body" id="ward-div">
                <?= $this->render('/ward/_form', ['modelWard' => $modelWard]) ?>
            </div>
            <!-- /.card-body -->
        </div>
    </a>
    <!-- /.card -->

    <a name="treatment">
        <div class="card" id="treatment_div" style="display:none;">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Treatment Details');?></h3>
                <div class="d-flex justify-content-end">
                    <?php
                    if(!empty($model))
                        echo "<div>". Yii::t('app','Total')." : ". (new Bill()) -> getTotalTreatmentCost(Yii::$app->request->get('bill_uid'))."&nbsp&nbsp&nbsp&nbsp&nbsp"."</div>";
                    ?>
                    <div class="card-tools">
                        <!-- Collapse Button -->
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body" id="treatment-div">
                <?= $this->render('/treatment_details/_form', ['modelTreatment' => $modelTreatment]) ?>
            </div>
            <!-- /.card-body -->
        </div>
    </a>
    <!-- /.card -->

    <a name="billGeneration">
        <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'bill-generation-form',
        'type' => 'vertical',
        'action' => Url::toRoute(['bill/generatebill', 'bill_uid' => Yii::$app->request->get('bill_uid')]),
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>
        <div class="card" id="bill_div" <?php if(empty($generate)){ echo 'style="display:none;"'; }
            else echo 'style="display:block;"';
    ?>>
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Bill Generation Details');?></h3>
                <div class="card-tools">
                    <!-- Collapse Button -->
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                            class="fas fa-minus"></i></button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'bill_generation_billable_sum_rm')->textInput(
                        [
                            'readonly' => true, 
                            'disabled' => $print_readonly,
                            'maxlength' => true, 
                            'class' => 'billalbe', 
                            'value' => (new Bill()) -> calculateBillable(Yii::$app->request->get('bill_uid'))
                        ]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?php
                        if($isGenerated)
                        {
                            echo $form->field($model, 'bill_generation_final_fee_rm')->textInput(
                                [
                                    'readonly' => true, 
                                    'disabled' => $print_readonly,
                                    'maxlength' => true, 
                                    'class' => 'finalFee', 
                                    'value' =>  $finalFee
                                ]); 
                        }
                        else
                        {
                            echo $form->field($model, 'bill_generation_final_fee_rm')->textInput(
                            [
                                'readonly' => true, 
                                'disabled' => $print_readonly,
                                'maxlength' => true, 
                                'class' => 'finalFee', 
                                'value' =>  (new Bill()) -> calculateFinalFee(Yii::$app->request->get('bill_uid'))
                            ]);
                        }
                    ?>
                    </div>

                </div>
                <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                <?= Html::button(Yii::t('app','Generate'), ['name' => 'generate', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "generateBill('{$urlGenerate}'); getBillableAndFinalFee();"]) ?>
                <!-- <?= Html::submitButton(Yii::t('app','Generate'), ['name' => 'generate', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'getBillableAndFinalFee();']) ?> -->
                <?= Html::a(Yii::t('app','Delete'), ['/bill/delete', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')], ['class'=>'btn btn-danger']) ?>
                <?php } ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <?php kartik\form\ActiveForm::end(); ?>
    </a>

    <a name="printing">
        <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'bill-print-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>
        <div class="card" id="print_div" style="display:none;">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Printing Details');?></h3>
                <div class="card-tools">
                    <!-- Collapse Button -->
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                            class="fas fa-minus"></i></button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <!-- <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true, 'disabled' => (new Bill())  -> isPrinted(Yii::$app->request->get('rn'))]) ?> -->
                        <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true, 'readonly' => true, 'id' => 'serial_number']) ?>
                    </div>
                </div>
                <?php 
                if( !$isPrinted ){
            ?>
                <?= Html::submitButton(Yii::t('app', 'Print'), ['class' => 'btn btn-success']) ?>
                <?= Html::button(Yii::t('app', 'Reset'), ['class' => 'btn btn-primary', 
                    'onclick' => '(function ( $event ) {
                        document.getElementById("serial_number").readOnly = false; 
                        document.getElementById("serial_number").value = "";
                        document.getElementById("serial_number").focus();
                    })();' ]) ?>
                <?= Html::button(Yii::t('app', 'Refresh'), 
                        ['class' => 'btn btn-secondary', 'id' => 'refresh', 'onclick' => "refreshButton('{$url}')"]) ?>
                <?php }else{ echo "<span class='badge badge-primary'>".Yii::t('app','Bill has been printed')."</span> <br/><br/>" ?>

                <!-- If the flash message existed, show it  -->
                <?php if(Yii::$app->session->hasFlash('msg')):?>
                <div id="flashError">
                    <?= Yii::$app->session->getFlash('msg') ?>
                </div>
                <?php endif; ?>
                
                <?= Html::a(Yii::t('app','Delete'), ['/bill/delete', 'bill_uid' => Yii::$app->request->get('bill_uid'),
                     'rn' => Yii::$app->request->get('rn')], ['class'=>'btn btn-danger']) ?>
                <?php } ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <?php kartik\form\ActiveForm::end(); ?>
    </a>

</div>


<script>
<?php if(!empty( Yii::$app->request->get('bill_uid'))){?>
document.getElementById("bill_div").style.display = "block";
document.getElementById("ward_div").style.display = "block";
document.getElementById("treatment_div").style.display = "block";
document.getElementById('print_div').style.display = "none";
<?php } if( $isGenerated && Yii::$app->request->get('bill_uid') &&  $isFree != 1){ ?>
document.getElementById("print_div").style.display = "block";
document.getElementById('card_div').style.display = "block";
<?php } ?>

function getBillableAndFinalFee() {
    $('#bill-bill_generation_billable_sum_rm').val(
        <?php echo (new Bill())  -> calculateBillable(Yii::$app->request->get('bill_uid')); ?>);
    $('#bill-bill_generation_final_fee_rm').val(
        <?php echo (new Bill())  -> calculateFinalFee(Yii::$app->request->get('bill_uid')); ?>);
}

function getDailyWardCost(url) {
    var wardClass = $('#wardClass').val();
    var statusCode = $('#statusCode :selected').text();
    $.get(url, {
        status: statusCode
    }, function(data) {
        var data = $.parseJSON(data);

        if (wardClass == '1a') $('#ward_cost').attr('value', data.class_1a_ward_cost);
        else if (wardClass == '1b') $('#ward_cost').attr('value', data.class_1b_ward_cost);
        else if (wardClass == '1c') $('#ward_cost').attr('value', data.class_1c_ward_cost);
        else if (wardClass == '2') $('#ward_cost').attr('value', data.class_2_ward_cost);
        else if (wardClass == '3') $('#ward_cost').attr('value', data.class_3_ward_cost);

        calculateItemCost();
    });
}

<?php if( Yii::$app->language == "en"){ ?>
// The function below will start the confirmation  dialog
function confirmAction(url) {
    var answer = confirm("Are you sure to generate bill?");
    if (answer) {
        window.location.href = url + '&confirm=true';
    }
    // else {
    //     window.location.href = history.go(-1);
    // }
}
<?php }else{?>
// The function below will start the confirmation  dialog
function confirmAction(url) {
    var answer = confirm("Adakah anda pasti menjana bil?");
    if (answer) {
        window.location.href = url + '&confirm=true';
    }
    // else {
    // window.location.href = history.go(-1);
    // }
}
<?php } ?>

function refreshButton(url) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            document.getElementById("serial_number").value = this.responseText;
            document.getElementById("serial_number").readOnly = true;
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}

function generateBill(url) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            console.log(this.responseText);
            if (this.responseText == false) {
                confirmAction(url);
            }
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}
</script>