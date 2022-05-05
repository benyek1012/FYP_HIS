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

$row_bill = (new \yii\db\Query())
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
    $('.1_unit_cost').attr('value', data.class_1_cost_per_unit);
    $('.2_unit_cost').attr('value', data.class_2_cost_per_unit);
    $('.3_unit_cost').attr('value', data.class_3_cost_per_unit);
    });
    });",
);

$this->registerJs(
    "var countTeatment = $('#countTreatment').val();
    for(i = 0; i < countTreatment; i++) {
        $('#treatment_details-'+i+'-item_count').on('change', function() { 
            calculateItemCost();
        });
    }"
);

$this->registerJs(
    "var countTeatment = $('#countTreatment').val();
    for(i = 0; i < countTreatment; i++) {
        $('#'+i+'_unit_cost').on('change', function() { 
            calculateItemCost();
        });
    }"
);

$this->registerJs(
    "$('#addWardRow').on('click', function() { 
        var countWard = $('#countWard').val();    
       
        $.get('/bill/ward', {ward : countWard}, function(data){
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
            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                    <?= Html::submitButton(Yii::t('app','Update'), ['name' => 'updateBill', 'value' => 'true', 'class' => 'btn btn-success']) ?>
                <?php }else{ ?>
                <?= Html::submitButton(Yii::t('app','Save'), ['name' => 'saveBill', 'value' => 'true', 'class' => 'btn btn-success']) ?>
            <?php } ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <?php kartik\form\ActiveForm::end(); ?>

    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'ward-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

    <div class="card">
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
                                calculateDays();
                             }',
                        ],])->label(false)?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelWard, "[{$index}]ward_end_datetime")->widget(DateTimePicker::classname(),['options' => ['class' => 'end_date'], 
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],   
                        'pluginEvents' => [
                            'change' => 'function () {
                                calculateDays();
                             }',
                        ],])->label(false)?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                    <td><?= $form->field($modelWard, "[$index]ward_number_of_days")->textInput(['maxlength' => true, 'class' => 'day'])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>
                        <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                            <?php }else{ ?>
                                <?= Html::a("x", ["/ward/delete", "ward_uid" => $modelWard->ward_uid, 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-xs"]) ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>

            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                    <?= Html::submitButton('Save Ward', ['name' => 'saveWard', 'value' => 'true', 'class' => 'btn btn-success']) ?>
                    <?= Html::submitButton(Yii::t('app','Update'), ['name' => 'updateWard', 'value' => 'true','class' => 'btn btn-success']) ?>
                <?php }else{ ?>
                    <?= Html::submitButton('Save Ward', ['name' => 'saveWard', 'value' => 'true', 'class' => 'btn btn-success']) ?>
            <?php } ?>

        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <?php kartik\form\ActiveForm::end(); ?>

    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'treatment-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

    <div class="card">
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
            <input type="hidden" id="countTreatment" name="countTreatment" value="<?php echo count($modelTreatment); ?>">
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
                    <td><?= $form->field($modelTreatment, "[$index]treatment_code")->dropDownList($treatment_code,['id'=>'treatmentCode',
                    'prompt'=>'Select reatment code','maxlength' => true])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment ,"[$index]treatment_name")->textInput(['maxlength' => true, 'id'=>'treatmentName'])->label(false) ?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>



                    <div class="col-sm-6">
                        <td>
                    <?php 
                      if($initial_ward_class == "1a"){?>
                        <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(['class' => '1_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                        <?php 
                      }
                      
                      else if($initial_ward_class == "1b"){?>
                        <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(['class' => '1_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                         <?php 
                      }

                      else if($initial_ward_class == "1c"){?>
                       <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(['class' => '1_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                        <?php 
                      }

                      else if($initial_ward_class == "2"){?>
                       <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(['class' => '2_unit_cost', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                         <?php 
                      }

                      else if($initial_ward_class == "3"){?>
                        <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(['class' => '3_unit_cos','onchange' => 'calculateItemCost();'])->label(false) ?>
                         <?php 
                      }
                      ?>
                        </td>
                </div>

                    
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]item_count")->textInput(['class' => 'item_num', 'onchange' => 'calculateItemCost();'])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]item_total_unit_cost_rm")->textInput(['class' => 'item_total_cost'])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>
                        <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                            <?php }else{ ?>
                                <?= Html::a("x", ["/treatment_details/delete", "treatment_details_uid" => $modelTreatment->treatment_details_uid, 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-xs"]) ?>
                        <?php } ?>
                    </td>
                </tr>
                    <?php } ?>
            </table>

            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                    <?= Html::submitButton('Save Treatment', ['name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success']) ?>
                    <?= Html::submitButton(Yii::t('app','Update'), ['name' => 'updateTreatment', 'value' => 'true','class' => 'btn btn-success']) ?>
                <?php }else{ ?>
                    <?= Html::submitButton('Save Treatment', ['name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success']) ?>
            <?php } ?>

        </div>
    </div>
    <!-- /.card-body -->
</div>
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
                            'readonly' => true,
                            'maxlength' => true, 
                            'class' => 'billalbe', 
                            'value' => Bill::calculateBillable(Yii::$app->request->get('bill_uid'))
                        ]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(
                        [
                            'readonly' => true,
                            'maxlength' => true, 
                            'class' => 'finalFee', 
                            'value' => Bill::calculateFinalFee(Yii::$app->request->get('bill_uid'))
                        ]) ?>
                </div>

            </div>
            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
                <?= Html::submitButton(Yii::t('app','Generate'), ['name' => 'generate', 'value' => 'true', 'class' => 'btn btn-success']) ?>
                <?= Html::a('Delete', ['/bill/delete', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn'), '#' => 'b'], ['class'=>'btn btn-success']) ?>
            <?php }?>
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
            <?php if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
                <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
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
document.getElementById('print_div').style.display = "none";
<?php } if(!empty( $row_bill['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
document.getElementById("print_div").style.display = "block";
document.getElementById('card_div').style.display = "block";
<?php } ?>

function calculateDays() {
    var countWard = $("#countWard").val();

    for(i = 0; i < countWard; i++){
        var date1 = new Date($("#ward-"+i+"-ward_start_datetime").val());
        var date2 = new Date($("#ward-"+i+"-ward_end_datetime").val());

        var SDT =  date1.getDate() + "/" + (date1.getMonth()+1) + "/" + date1.getFullYear();
        var EDT =  date2.getDate() + "/" + (date2.getMonth()+1) + "/" + date2.getFullYear();

        function parseDate(str) {
            var mdy = str.split("/");
            return new Date(mdy[2], mdy[1]-1, mdy[0]);
        }
        
        function datediff(first, second) {
            // Take the difference between the dates and divide by milliseconds per day.
            // Round to nearest whole number to deal with DST.
            return Math.round((second-first)/(1000*60*60*24));
        }
        
        if(date1 != "Invalid Date" && date2 != "Invalid Date"){
            var timeDifference = datediff(parseDate(SDT), parseDate(EDT));
            var daysDiff = timeDifference;
            var days = Math.round(daysDiff);

            if(date2.getHours() >=12)
                days += 1;

            if(date1.getDate() == date2.getDate())
                days = 1;

            $("#ward-"+i+"-ward_number_of_days").val(days);
        }
    }
}

function calculateItemCost() {
    var countTeatment = $("#countTreatment").val();

    for(i = 0; i < countTeatment; i++){
        var itemPerUnit = $('#treatment_details-'+i+'-item_per_unit_cost_rm').val();
        var itemCount = $('#treatment_details-'+i+'-item_count').val();

        if(itemCount != '' && itemPerUnit != ""){
            var totalCost = parseFloat(itemPerUnit) * parseFloat(itemCount);
        }
        
        $('#treatment_details-'+i+'-item_total_unit_cost_rm').val(totalCost); 
    }
}
</script>