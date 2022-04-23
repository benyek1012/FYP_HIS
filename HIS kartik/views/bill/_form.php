<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\Patient_admission;
use app\models\Treatment_details;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */

$admission_model = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);

$row = (new \yii\db\Query())
->select(['bill_generation_datetime'])
->from('bill')
->where(['bill_uid' => Yii::$app->request->get('bill_uid')])
->one();


$billuid = Base64UID::generate(32);
$generationresponsibleuid = Base64UID::generate(32);
$billprintresponsibleuid = Base64UID::generate(32);

if(empty( Yii::$app->request->get('bill_uid')))
$initial_ward_class = $admission_model->initial_ward_class;


$free = array(
    0 =>'No', //false
    1 =>'Yes',    //true
);

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
                    <?= $form->field($model, 'status_code')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'status_description')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?php if(empty( Yii::$app->request->get('bill_uid'))){ ?>
                    <?= $form->field($model, 'class')->textInput(['maxlength' => true,'value' => $initial_ward_class]) ?>
                    <?php }else{ ?>
                    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>
                    <?php } ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'department_code')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'department_name')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'is_free')->dropDownList($free) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'nurse_responsilbe')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Ward Details');?></h3>
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
                    <td>Ward Code</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Ward Name</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Ward Start Datetime</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Ward End Datetime</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Ward Number of Days</td>
                </tr>
                <?php foreach ($modelWard as $index => $modelWard) { ?>
                <tr>
                    <td><?= $form->field($modelWard, "[$index]ward_code")->textInput(['maxlength' => true])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelWard, "[$index]ward_name")->textInput()->label(false) ?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelWard, "[{$index}]ward_start_datetime")->widget(DateTimePicker::classname(),['options' => ['class' => 'start_date'],
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii']])->label(false)?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelWard, "[{$index}]ward_end_datetime")->widget(DateTimePicker::classname(),['options' => ['class' => 'end_date'], 
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],   
                         'pluginEvents' => [
                            'change' => 'function () {
                                var date1 = new Date($(".start_date").val());
                                var date2 = new Date($(".end_date").val());
                            
                                var timeDifference = date2.getTime() - date1.getTime();
                                var milliSecondsOneSecond = 1000;
                                var secondInOneHour = 3600;
                                var hoursInOneDay = 24;

                                var daysDiff = timeDifference  / ( milliSecondsOneSecond * secondInOneHour *  hoursInOneDay);
                                var days = Math.ceil(daysDiff);
                                
                                $(".day").val(days);

                              
                             }',
                        ],])->label(false)?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>  
                    
                    <td><?= $form->field($modelWard, "[$index]ward_number_of_days")->textInput(['maxlength' => true, 'class' => 'day'])->label(false) ?></td> 
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
                    <td>Treatment Code</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Treatment Name</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Item Per Unit Cost (RM)</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Item Count</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Item Total Unit Cost (RM)</td>
                </tr>
                <?php foreach ($modelTreatment as $index => $modelTreatment) { ?>
                <tr>
                    <td><?= $form->field($modelTreatment, "[$index]treatment_code")->textInput(['maxlength' => true])->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]treatment_name")->textInput()->label(false) ?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput()->label(false) ?>
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]item_count")->textInput()->label(false) ?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><?= $form->field($modelTreatment, "[$index]item_total_unit_cost_rm")->textInput()->label(false) ?>
                    </td>
                </tr>
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
                <?= $form->field($model, 'generation_responsible_uid')->hiddenInput([
                'readonly' => true, 'maxlength' => true,'value' => $generationresponsibleuid])->label(false) ?>

                <div class="col-sm-6">
                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'bill_generation_billable_sum_rm')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(['maxlength' => true]) ?>
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
                <?= $form->field($model, 'bill_print_responsible_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $billprintresponsibleuid])->label(false) ?>
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

    <?php if(!empty( $row['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
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
<?php } if(!empty( $row['bill_generation_datetime'] && Yii::$app->request->get('bill_uid'))){ ?>
document.getElementById("print_div").style.display = "block";
document.getElementById('card_div').style.display = "block";
<?php } ?>
</script>