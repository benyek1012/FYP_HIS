<?php

use app\controllers\BillController;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\Patient_admission;
use app\models\Treatment_details;
use app\models\Ward;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */

$admission_model = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);

if(empty( Yii::$app->request->get('bill_uid'))) $initial_ward_class = $admission_model->initial_ward_class;
else{
    $rows = (new \yii\db\Query())
    ->select(['*'])
    ->from('patient_admission')
    ->where(['rn' => Yii::$app->request->get('rn')])
    ->all();
    foreach($rows as $row){
        $initial_ward_class = $row['initial_ward_class'];
    }  
}

$row = (new \yii\db\Query())
->select(['bill_generation_datetime'])
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
    $.get('/bill/status', {status : statusCode}, function(data){
    var data = $.parseJSON(data);
    $('#status_des').attr('value', data.status_description);
    $('#1a_ward_cost').attr('value', data.class_1a_ward_cost);
    $('#1b_ward_cost').attr('value', data.class_1b_ward_cost);
    $('#1c_ward_cost').attr('value', data.class_1c_ward_cost);
    $('#2_ward_cost').attr('value', data.class_2_ward_cost);
    $('#3_ward_cost').attr('value', data.class_3_ward_cost);
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
    "$('#wardCode').change(function() {
    var wardCode = $(this).val();
    $.get('/bill/ward', {ward : wardCode}, function(data){
    var data = $.parseJSON(data);
    $('#wardName').attr('value', data.ward_name);
    });
    });",
);

$this->registerJs(
    "$('#treatmentCode').change(function() {
    var treatmentCode = $(this).val();
    $.get('/bill/treatment', {treatment : treatmentCode}, function(data){
    var data = $.parseJSON(data);
    $('#treatmentName').attr('value', data.treatment_name);
    });
    });",
);

$this->registerJs(
    "$('.item_num').on('change', function() { 
        var itemPerUnit = $('.item_per_unit_cost').val();
        var itemCount = $('.item_num').val();

        if(itemPerUnit != ''){
            var totalCost = parseFloat(itemPerUnit) * parseFloat(itemCount);
        }
        
        $('.item_total_cost').val(totalCost); 
        $('.total_treatment_amount').html('(RM ' + totalCost + ')');
    });"
);

$this->registerJs(
    "$('.item_per_unit_cost').on('change', function() { 
        var itemPerUnit = $('.item_per_unit_cost').val();
        var itemCount = $('.item_num').val();

        if(itemCount != ''){
            var totalCost = parseFloat(itemPerUnit) * parseFloat(itemCount);
        }
        
        $('.item_total_cost').val(totalCost); 
        $('.total_treatment_amount').html('(RM ' + totalCost + ')');
    });"
);

// if(!empty(Yii::$app->request->get('bill_uid'))){
//     $totalWardDays = 0;
//     $dailyWardCost = 0.0;
//     $totalTreatmentCost = 0.0;
//     $billable = 0.0;

//     foreach ($modelWard as $index => $modelWard){
//         $totalWardDays += (float) "[$index]ward_number_of_days";
//         $dailyWardCost = (float) "[$index]daily_ward_cost";
//         $totalTreatmentCost += (float) "[$index]item_per_unit_cost" * (float) "[$index]item_count";
//     }
    
//     $billable = ($totalWardDays * $dailyWardCost) + $totalTreatmentCost;

//     var_dump(floatval($dailyWardCost));
//     exit();
// }

?>

<div class="bill-form">


    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'ward-dynamic-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

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
                    'prompt'=>'Please select status code','maxlength' => true]) ?>
                 </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'status_description')->textInput(['maxlength' => true, 'id'=>'status_des']) ?>
                </div>

                <div class="col-sm-6">
                    <?php if(empty( Yii::$app->request->get('bill_uid'))){ ?>
                    <?= $form->field($model, 'class')->textInput(['maxlength' => true,'value' => $initial_ward_class]) ?>
                    <?php }else{ ?>
                    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>
                    <?php } ?>
                </div>

                <div class="col-sm-6">
                    <?php 
                      if($initial_ward_class == "1a"){?>
                        <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true, 'id'=>'1a_ward_cost']) ?>
                      <?php 
                      }
                      
                      else if($initial_ward_class == "1b"){?>
                        <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true, 'id'=>'1b_ward_cost']) ?>
                      <?php 
                      }

                      else if($initial_ward_class == "1c"){?>
                        <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true, 'id'=>'1c_ward_cost']) ?>
                      <?php 
                      }

                      else if($initial_ward_class == "2"){?>
                        <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true, 'id'=>'2_ward_cost']) ?>
                      <?php 
                      }

                      else if($initial_ward_class == "3"){?>
                        <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true, 'id'=>'3_ward_cost']) ?>
                      <?php 
                      }
                      ?>
                  
                </div>



                <div class="col-sm-6">
                    <?= $form->field($model, 'department_code')->dropDownList($department_code, ['id'=>'departmentCode',
                    'prompt'=>'Please select department code','maxlength' => true]) ?>
                 </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'department_name')->textInput(['maxlength' => true, 'id'=>'departmentName']) ?>
                </div>


                <div class="col-sm-6">
                    <?= $form->field($model, 'is_free')->dropDownList($free) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'nurse_responsible')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Ward Details');?></h3>
            <br>
            <h3 class="card-title total_ward_cost"></h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table>
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
                </tr>



                <?php foreach ($modelWard as $index => $modelWard) { ?>
                <tr>
                    <td><?= $form->field($modelWard, "[$index]ward_code")->textInput([  'id'=>'wardCode',
                    'value'=>$ward_code,'maxlength' => true])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelWard, "[$index]ward_name")->textInput(['maxlength' => true, 'id'=>'wardName','value'=>$ward_name])->label(false) ?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelWard, "[{$index}]ward_start_datetime")->widget(DateTimePicker::classname(),['options' => ['class' => 'start_date'],
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],
                        'pluginEvents' => [
                            'change' => 'function () {
                                var date1 = new Date($(".start_date").val());
                                var date2 = new Date($(".end_date").val());
                                var item = $(".item_count").val();
                                var dailyWardCost = $(".daily_ward_cost").val();
                            
                                if(date2 != ""){
                                    var timeDifference = date2.getTime() - date1.getTime();
                                    var milliSecondsOneSecond = 1000;
                                    var secondInOneHour = 3600;
                                    var hoursInOneDay = 24;

                                    var daysDiff = timeDifference  / ( milliSecondsOneSecond * secondInOneHour *  hoursInOneDay);
                                    var days = Math.ceil(daysDiff);

                                    var totalWardCost = parseFloat(dailyWardCost) * parseFloat(days);
                                    
                                    $(".day").val(days);
                                    $(".total_ward_cost").html("(RM " + totalWardCost + ")");
                                }
                             }',
                        ],])->label(false)?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelWard, "[{$index}]ward_end_datetime")->widget(DateTimePicker::classname(),['options' => ['class' => 'end_date'], 
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],   
                        'pluginEvents' => [
                            'change' => 'function () {
                                var date1 = new Date($(".start_date").val());
                                var date2 = new Date($(".end_date").val());
                                var item = $(".item_count").val();
                                var dailyWardCost = $(".daily_ward_cost").val();
                            
                                if(date1 != ""){
                                    var timeDifference = date2.getTime() - date1.getTime();
                                    var milliSecondsOneSecond = 1000;
                                    var secondInOneHour = 3600;
                                    var hoursInOneDay = 24;

                                    var daysDiff = timeDifference  / ( milliSecondsOneSecond * secondInOneHour *  hoursInOneDay);
                                    var days = Math.ceil(daysDiff);

                                    var totalWardCost = parseFloat(dailyWardCost) * parseFloat(days);
                                    
                                    $(".day").val(days);
                                    $(".total_ward_cost").html("(RM " + totalWardCost + ")");
                                }
                             }',
                        ],])->label(false)?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                    <td><?= $form->field($modelWard, "[$index]ward_number_of_days")->textInput(['maxlength' => true, 'class' => 'day'])->label(false) ?>
                    </td>
                </tr>
                <script>
                // function calDiff(){
                //     var date1 = new Date($("[{$index}]ward_start_datetime").val());
                //     var date2 = new Date($("[{$index}]ward_end_datetime").val());

                //     var timeDifference = date2.getTime() - date1.getTime();
                //     alert(timeDifference);
                // }
                </script>
                <?php } ?>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Treatment Details');?></h3>
            <br>
            <h3 class="card-title total_treatment_amount"></h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
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
                </tr>
                <?php foreach ($modelTreatment as $index => $modelTreatment) { ?>
                <tr>


                    <td><?= $form->field($modelTreatment, "[$index]treatment_code")->dropDownList($treatment_code,['id'=>'treatmentCode',
                    'prompt'=>'Select reatment code','maxlength' => true])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment ,"[$index]treatment_name")->textInput(['maxlength' => true, 'id'=>'treatmentName'])->label(false) ?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                    <td><?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(['class' => 'item_per_unit_cost', 'value' => $unit_cost])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]item_count")->textInput(['class' => 'item_num'])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]item_total_unit_cost_rm")->textInput(['class' => 'item_total_cost'])->label(false) ?>
                    </td>
                <tr>
                    <?php } ?>
            </table>
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<a name="b">
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
                            'maxlength' => true, 
                            'class' => 'billalbe', 
                            'value' => BillController::getBillable(Yii::$app->request->get('bill_uid'))
                        ]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(
                        [
                            'maxlength' => true, 
                            'class' => 'finalFee', 
                            'value' => BillController::getFinalFee(Yii::$app->request->get('bill_uid'))
                        ]) ?>
                </div>

            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</a>

<a name="p">
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
                    <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</a>


<div class="form-group">

    <?php if(!empty( $row['bill_generation_datetime']) && (!empty(Yii::$app->request->get('bill_uid')))){ ?>
    <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
    <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
    <?= Html::submitButton(Yii::t('app','Generate'), ['class' => 'btn btn-success']) ?>
    <?php }else{ ?>
    <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    <?php } ?>
</div>


<?php kartik\form\ActiveForm::end(); ?>

</div>


<script>
<?php if(!empty( Yii::$app->request->get('bill_uid'))){?>
document.getElementById("bill_div").style.display = "block";
document.getElementById('print_div').style.display = "none";
<?php } if(!empty( $row['bill_generation_datetime']) && (!empty(Yii::$app->request->get('bill_uid')))){ ?>
document.getElementById("print_div").style.display = "block";
document.getElementById('card_div').style.display = "block";
<?php } ?>



</script>