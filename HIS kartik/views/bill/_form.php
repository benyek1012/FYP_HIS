<?php

use app\controllers\BillController;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\Patient_admission;
use app\models\Bill;
use app\models\Treatment_details;
use app\models\Ward;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */

$admission_model = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);

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
->select(['bill_generation_datetime', 'is_free'])
->from('bill')
->where(['bill_uid' => Yii::$app->request->get('bill_uid')])
->one();


$rows = (new \yii\db\Query())
->select('*')
->from('lookup_status')
->all();

$dayly_ward_cost = "";
$status_code = array();
$unit_class = "";
foreach($rows as $row){
  $status_code[$row['status_code']] = $row['status_code'];
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
    $department_code[$row['department_code']] = $row['department_code'];
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
    0 =>'No', //false
    1 =>'Yes',    //true
);

$this->registerJs(
    "$('#statusCode').change(function() {
    var statusCode = $(this).val();
    var wardClass = $('#wardClass :selected').text();
    $.get('/bill/status', {status : statusCode}, function(data){
    var data = $.parseJSON(data);
    $('#status_des').attr('value', data.status_description);
    if(wardClass == '1a') $('#ward_cost').attr('value', data.class_1a_ward_cost);
    else if(wardClass == '1b') $('#ward_cost').attr('value', data.class_1b_ward_cost);
    else if(wardClass == '1c') $('#ward_cost').attr('value', data.class_1c_ward_cost);
    else if(wardClass == '2') $('#ward_cost').attr('value', data.class_2_ward_cost);
    else if(wardClass == '3') $('#ward_cost').attr('value', data.class_3_ward_cost);
    });
    });",
);

$this->registerJs(
    "$('#wardClass').change(function() {
        var wardClass = $(this).val();
        var statusCode = $('#statusCode :selected').text();
        $.get('/bill/status', {status : statusCode}, function(data){
            var data = $.parseJSON(data);
        
            if(wardClass == '1a') $('#ward_cost').attr('value', data.class_1a_ward_cost);
            else if(wardClass == '1b') $('#ward_cost').attr('value', data.class_1b_ward_cost);
            else if(wardClass == '1c') $('#ward_cost').attr('value', data.class_1c_ward_cost);
            else if(wardClass == '2') $('#ward_cost').attr('value', data.class_2_ward_cost);
            else if(wardClass == '3') $('#ward_cost').attr('value', data.class_3_ward_cost);
            
            $('.treatmentCode', document).each(function(index, item){
                var billClass = $('#wardClass').val();
                var treatmentCode = this.value;
                $.get('/bill/treatment', {treatment : treatmentCode}, function(data){
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
                    // calculateItemCost();
                });
            });
        });        
    });",
);

$this->registerJs(
    "$('#departmentCode').change(function() {
    var departmentCode = $(this).val();
    $.get('/bill/department', {department : departmentCode}, function(data){
    var data = $.parseJSON(data);
    $('#departmentName').attr('value', data.department_name);
    });
    });",
);

$this->registerJs(
    "$('.wardCode', document).each(function(index, item){
        $(item).on('change', function() {
            var wardCode = this.value;
            $.get('/bill/ward', {ward : wardCode}, function(data){
                var data = $.parseJSON(data);
                $('#ward-'+index+'-ward_name').attr('value', data.ward_name);
            });
        });
    });
    ",
);

$this->registerJs(
    "$('.treatmentCode', document).each(function(index, item){
        var billClass = $('#wardClass').val();
        $(item).on('change', function() {
            var treatmentCode = this.value;
            $.get('/bill/treatment', {treatment : treatmentCode}, function(data){
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
                // calculateItemCost();
            });
        });
    });
    ",
);

$this->registerJs(
    "$('#addWardRow').on('click', function() { 
        var countWard = $('#countWard').val();    
       
        $.get('/bill/wardRow', {ward : countWard}, function(data){
            var data = $.parseJSON(data);
            $('#countWard').attr('value', data.length);
        });
    });"
);

$this->registerJs(
    "$('#addTreatmentRow').on('click', function() { 
        var countTreatment = $('#countTreatment').val();    
       
        $.get('/bill/treatmentRow', {treatment : countTreatment}, function(data){
            var data = $.parseJSON(data);
            $('#countTreatment').attr('value', data.length);
        });
    });"
);

if(empty($print_readonly)) $print_readonly = false;

?>

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
        <div class="card">
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
                    <?= $form->field($model, 'bill_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $billuid])->label(false) ?>

                    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false) ?>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_code')->dropDownList($status_code, ['id'=>'statusCode',
                    'prompt'=>'Please select status code','maxlength' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_description')->textInput(['maxlength' => true, 'id'=>'status_des', 'readonly' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?php if(empty( Yii::$app->request->get('bill_uid'))){ ?>
                        <?= $form->field($model, 'class')->dropDownList($ward_class, 
                            ['id'=>'wardClass','prompt'=>'Please select ward class', 'value' => $initial_ward_class]) ?>
                        <?php }else{ ?>
                        <?= $form->field($model, 'class')->dropDownList($ward_class, 
                            ['id'=>'wardClass','prompt'=>'Please select ward class', 'disabled' => $print_readonly]) ?>
                        <?php } ?>

                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true, 'id'=>'ward_cost',  
                                'readonly' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'department_code')->dropDownList($department_code, ['id'=>'departmentCode',
                    'prompt'=>'Please select department code','maxlength' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'department_name')->textInput(['maxlength' => true, 'id'=>'departmentName', 
                             'readonly' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'is_free')->dropDownList($free, ['disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'nurse_responsible')->textInput(['maxlength' => true, 'disabled' => $print_readonly]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'disabled' => $print_readonly]) ?>
                    </div>
                </div>
                <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                <?= Html::submitButton(Yii::t('app','Update'), ['name' => 'updateBill', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'getDailyWardCost();']) ?>
                <?php }else{ ?>
                <?= Html::submitButton(Yii::t('app','Save'), ['name' => 'saveBill', 'value' => 'true', 'class' => 'btn btn-success']) ?>
                <?php } ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <?php kartik\form\ActiveForm::end(); ?>
    </a>

    <a name="ward">
        <?php $form = kartik\form\ActiveForm::begin([
            'id' => 'ward-form',
            'type' => 'vertical',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
            ],
        ]); 
    ?>

        <div class="card" id="ward_div" style="display:none;">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Ward Details');?></h3>
                <div class="d-flex justify-content-end">
                    <?php
                if(!empty($model))
                    echo "<div>".Bill::getTotalWardCost(Yii::$app->request->get('bill_uid'))."&nbsp&nbsp&nbsp&nbsp&nbsp"."</div>";
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
            <div class="card-body">
                <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else{ ?>
                <?= Html::submitButton('+', ['id' => 'addWardRow', 'name' => 'addWardRow', 'value' => 'true', 'class' => 'btn btn-info btn-xs']) ?>
                <?= Html::submitButton('-', ['name' => 'removeWardRow', 'value' => 'true', 'class' => 'btn btn-danger btn-xs']) ?>
                <?php } ?>
                <input type="hidden" id="countWard" name="countWard" value="<?php echo count($modelWard); ?>">
                <table id="ward-table">
                    <tr>
                        <td><?php echo Yii::t('app','Ward Code');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Ward Name');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Ward Start Datetime');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Ward End Datetime');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Ward Number of Days');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td> </td>
                    </tr>

                    <?php foreach ($modelWard as $index => $modelWard) { ?>
                    <tr>
                        <td>
                            <?= $form->field($modelWard, "[$index]ward_code")->dropDownList($wardcode, ['class' => 'wardCode',
                             'prompt'=>'Select ward code', 'maxlength' => true, 'value' => $modelWard->ward_code,
                              'disabled' => $print_readonly])->label(false) ?>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?= $form->field($modelWard, "[$index]ward_name")->textInput(['maxlength' => true, 'class' => 'wardName',
                                            'value'=>$modelWard->ward_name,  'readonly' => true, 'disabled' => $print_readonly])->label(false) ?>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?= $form->field($modelWard, "[{$index}]ward_start_datetime")->widget(DateTimePicker::classname(),[
                        'options' => ['class' => 'start_date', 'disabled' => $print_readonly],
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],
                        'pluginEvents' => [
                            'change' => 'function () {
                                calculateDays();
                             }',
                        ],])->label(false)?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?= $form->field($modelWard, "[{$index}]ward_end_datetime")->widget(DateTimePicker::classname(),[
                        'options' => ['class' => 'end_date', 'disabled' => $print_readonly], 
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],   
                        'pluginEvents' => [
                            'change' => 'function () {
                                calculateDays();
                             }',
                        ],])->label(false)?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                        <td><?= $form->field($modelWard, "[$index]ward_number_of_days")->textInput(['maxlength' => true,
                                             'class' => 'day',  'readonly' => true, 'disabled' => $print_readonly])->label(false) ?>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                            <?php }else{ ?>
                            <?= Html::a("x", ["/ward/delete", "ward_uid" => $modelWard->ward_uid, 'bill_uid' => Yii::$app->request->get('bill_uid'),
                                 'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-xs"]) ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </table>

                <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                <?= Html::submitButton('Save Ward', ['name' => 'saveWard', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'calculateDays();']) ?>
                <?php }else{ ?>
                <?= Html::submitButton('Save Ward', ['name' => 'saveWard', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'calculateDays();']) ?>
                <?php } ?>

            </div>
            <!-- /.card-body -->
        </div>
    </a>
    <!-- /.card -->
    <?php kartik\form\ActiveForm::end(); ?>

    <a name="treatment">
        <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'treatment-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

        <div class="card" id="treatment_div" style="display:none;">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Treatment Details');?></h3>
                <div class="d-flex justify-content-end">
                    <?php
                if(!empty($model))
                    echo "<div>".Bill::getTotalTreatmentCost(Yii::$app->request->get('bill_uid'))."&nbsp&nbsp&nbsp&nbsp&nbsp"."</div>";
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
            <div class="card-body">
                <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else{ ?>
                <?= Html::submitButton('+', ['id' => 'addTreatmentRow', 'name' => 'addTreatmentRow', 'value' => 'true', 'class' => 'btn btn-info btn-xs']) ?>
                <?= Html::submitButton('-', ['id' => 'removeTreatmentRow', 'name' => 'removeTreatmentRow', 'value' => 'true', 'class' => 'btn btn-danger btn-xs']) ?>
                <?php } ?>
                <input type="hidden" id="countTreatment" name="countTreatment"
                    value="<?php echo count($modelTreatment); ?>">
                <table>
                    <tr>
                        <td><?php echo Yii::t('app','Treatment Code');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Treatment Name');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Item Per Unit Cost (RM)');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Item Count');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo Yii::t('app','Item Total Unit Cost (RM)');?></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td> </td>
                    </tr>
                    <?php foreach ($modelTreatment as $index => $modelTreatment) { ?>
                    <tr>
                        <td><?= $form->field($modelTreatment, "[$index]treatment_code")->dropDownList($treatment_code,['class' => 'treatmentCode',
                                'prompt'=>'Select treatment code','maxlength' => true, 'disabled' => $print_readonly])->label(false) ?>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?= $form->field($modelTreatment ,"[$index]treatment_name")->textInput(['maxlength' => true, 'class' => 'treatmentName',
                                    'readonly' => true, 'disabled' => $print_readonly])->label(false) ?>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>



                        <div class="col-sm-6">
                            <td>
                                <?php 
                                  if(empty( Yii::$app->request->get('bill_uid'))){ 
                      if($initial_ward_class == "1a"){?>
                                <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '1_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                                <?php 
                      }
                      
                      else if($initial_ward_class == "1b"){?>
                                <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '1_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                                <?php 
                      }

                      else if($initial_ward_class == "1c"){?>
                                <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '1_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                                <?php 
                      }

                      else if($initial_ward_class == "2"){?>
                                <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '2_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                                <?php 
                      }

                      else if($initial_ward_class == "3"){?>
                                <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '3_unit_cos','onchange' => 'calculateItemCost();'])->label(false) ?>
                                <?php 
                      }
                    }else{ ?>
                                <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(
                                    [ 'readonly' => true, 'disabled' => $print_readonly])->label(false) ?>
                                <?php    }
              ?>
                            </td>
                        </div>

                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?= $form->field($modelTreatment, "[$index]item_count")->textInput(['class' => 'item_num',
                                    'disabled' => $print_readonly, 'onchange' => 'calculateItemCost();'])->label(false) ?>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><?= $form->field($modelTreatment, "[$index]item_total_unit_cost_rm")->textInput([ 'readonly' => true,
                                     'disabled' => $print_readonly, 'class' => 'item_total_cost'])->label(false) ?>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                            <?php }else{ ?>
                            <?= Html::a("x", ["/treatment_details/delete", "treatment_details_uid" => $modelTreatment->treatment_details_uid,
                                 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-xs"]) ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </table>

                <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                <?= Html::submitButton('Save Treatment', ['name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'calculateItemCost();']) ?>
                <?php }else{ ?>
                <?= Html::submitButton('Save Treatment', ['name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'calculateItemCost();']) ?>
                <?php } ?>

            </div>
        </div>
        <!-- /.card-body -->
</div>
</a>
<!-- /.card -->
<?php kartik\form\ActiveForm::end(); ?>

<?php $form = kartik\form\ActiveForm::begin([
        'id' => 'bill-generation-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

<a name="billGeneration">
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
                            'value' => Bill::calculateBillable(Yii::$app->request->get('bill_uid'))
                        ]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(
                        [
                            'readonly' => true, 
                            'disabled' => $print_readonly,
                            'maxlength' => true, 
                            'class' => 'finalFee', 
                            'value' => Bill::calculateFinalFee(Yii::$app->request->get('bill_uid'))
                        ]) ?>
                </div>

            </div>
            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
            <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
            <!-- <?= Html::submitButton(Yii::t('app','Generate'), ['name' => 'generate', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'getBillableAndFinalFee();']) ?> -->
            <?= Html::a('Generate', ['/bill/generate', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn'), 'generate' => 'true'], ['class'=>'btn btn-success']) ?>
            <?php }?>
            <?php if($row_bill['is_free'] == 1){ ?> 
            <?= Html::a('Delete', ['/bill/delete', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn'), '#' => 'b'], ['class'=>'btn btn-success']) ?>
            <?php } ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</a>
<?php kartik\form\ActiveForm::end(); ?>

<?php $form = kartik\form\ActiveForm::begin([
        'id' => 'bill-print-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

<a name="printing">
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
                    <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true, 'disabled' => Bill::checkExistPrint(Yii::$app->request->get('rn'))]) ?>
                </div>
            </div>
            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
            <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Delete', ['/bill/delete', 'bill_uid' => Yii::$app->request->get('bill_uid'),
                     'rn' => Yii::$app->request->get('rn'), '#' => 'p'], ['class'=>'btn btn-success']) ?>
            <?php } ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</a>


<!-- <div class="form-group">

    <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
    <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
    <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
    <?= Html::submitButton(Yii::t('app','Generate'), ['class' => 'btn btn-success']) ?>
    <?php }else{ ?>
    <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    <?php } ?>
</div> -->


<?php kartik\form\ActiveForm::end(); ?>

</div>


<script>
<?php if(!empty( Yii::$app->request->get('bill_uid'))){?>
document.getElementById("bill_div").style.display = "block";
document.getElementById("ward_div").style.display = "block";
document.getElementById("treatment_div").style.display = "block";
document.getElementById('print_div').style.display = "none";
<?php } if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid')) 
            && $row_bill['is_free'] != 1){ ?>
document.getElementById("print_div").style.display = "block";
document.getElementById('card_div').style.display = "block";
<?php } ?>

function calculateDays() {
    var countWard = $("#countWard").val();

    for (var i = 0; i < countWard; i++) {
        var date1 = new Date($("#ward-" + i + "-ward_start_datetime").val());
        var date2 = new Date($("#ward-" + i + "-ward_end_datetime").val());

        var SDT = date1.getDate() + "/" + (date1.getMonth() + 1) + "/" + date1.getFullYear();
        var EDT = date2.getDate() + "/" + (date2.getMonth() + 1) + "/" + date2.getFullYear();

        function parseDate(str) {
            var mdy = str.split("/");
            return new Date(mdy[2], mdy[1] - 1, mdy[0]);
        }

        function datediff(first, second) {
            // Take the difference between the dates and divide by milliseconds per day.
            // Round to nearest whole number to deal with DST.
            return Math.round((second - first) / (1000 * 60 * 60 * 24));
        }

        if (date1 != "Invalid Date" && date2 != "Invalid Date") {
            var timeDifference = datediff(parseDate(SDT), parseDate(EDT));
            var daysDiff = timeDifference;
            var days = Math.round(daysDiff);

            if (date2.getHours() >= 12)
                days += 1;

            if (date1.getDate() == date2.getDate())
                days = 1;

            $("#ward-" + i + "-ward_number_of_days").val(days);
        }
    }
}

function calculateItemCost() {
    var countTeatment = $("#countTreatment").val();

    for (var i = 0; i < countTeatment; i++) {
        var itemPerUnit = $('#treatment_details-' + i + '-item_per_unit_cost_rm').val();
        var itemCount = $('#treatment_details-' + i + '-item_count').val();

        if (itemCount != '' && itemPerUnit != "") {
            var totalCost = parseFloat(itemPerUnit) * parseFloat(itemCount);
            $('#treatment_details-' + i + '-item_total_unit_cost_rm').val(totalCost);
        }
    }
}

function getBillableAndFinalFee(){
    $('#bill-bill_generation_billable_sum_rm').val(<?php echo Bill::calculateBillable(Yii::$app->request->get('bill_uid')); ?>);
    $('#bill-bill_generation_final_fee_rm').val(<?php echo Bill::calculateFinalFee(Yii::$app->request->get('bill_uid')); ?>);
}

function getDailyWardCost() {
    var wardClass = $('#wardClass').val();
    var statusCode = $('#statusCode :selected').text();
    $.get('/bill/status', {status : statusCode}, function(data){
        var data = $.parseJSON(data);
    
        if(wardClass == '1a') $('#ward_cost').attr('value', data.class_1a_ward_cost);
        else if(wardClass == '1b') $('#ward_cost').attr('value', data.class_1b_ward_cost);
        else if(wardClass == '1c') $('#ward_cost').attr('value', data.class_1c_ward_cost);
        else if(wardClass == '2') $('#ward_cost').attr('value', data.class_2_ward_cost);
        else if(wardClass == '3') $('#ward_cost').attr('value', data.class_3_ward_cost);
    });   
}

// The function below will start the confirmation dialog
function confirmAction() {
    var answer = confirm("Are you sure to generate bill?");
    if (answer) {
        window.location.href = window.location + '&confirm=true';
    } else {
        window.location.href = history.back();
    }
}

</script>