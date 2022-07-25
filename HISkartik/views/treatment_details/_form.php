<?php
use yii\helpers\Html;
use app\models\Patient_admission;
use app\models\Bill;
use app\models\Ward;
use yii\helpers\Url;
use yii\widgets\Pjax;

$admission_model = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$modelWardDate = Ward::find()->where(['bill_uid' => Yii::$app->request->get('bill_uid')])->orderby(['ward_start_datetime' => SORT_ASC])->all(); 
         
// if($modelWardDate != null){
//     $modelDate = Ward::find()->where(['between', 'ward_start_datetime', $modelWard[0]->ward_start_datetime, $modelWard[0]->ward_end_datetime])->all();

// }

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

if(!empty($row_bill))
{
   $isGenerated = (new Bill()) -> isGenerated($row_bill['rn']);
   $isFree =  (new Bill()) -> isFree($row_bill['rn']);
   $isPrinted =  (new Bill()) -> isPrinted($row_bill['rn']);
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

$rows = (new \yii\db\Query())
->select('*')
->from('lookup_treatment')
->all();

$treatment_code = array();
$unit_cost = "";
foreach($rows as $row){
    $treatment_code[$row['treatment_code']] = $row['treatment_code'] . ' - ' . $row['treatment_name'];
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

if(empty($print_readonly)) $print_readonly = false;

if($print_readonly)
{
    $this->registerJs(
        "$('#bill_details').CardWidget('collapse');
        $('#ward_div').CardWidget('collapse');
        $('#treatment_div').CardWidget('collapse');"
        
    );
}

$url = Url::toRoute(['/bill/treatment']);
$urlTreatment = Url::toRoute(['/bill/treatment']);
?>

<a name="treatment">
    <?php Pjax::begin(); ?>
    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'treatment-form',
        'options' => ['data-pjax' => true],
        'type' => 'vertical',
        'action' =>  Url::toRoute(['/bill/generate', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn'), '#' => 'treatment']),
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]);
    ?>

        <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
        <?php }else{ ?>
        <!-- <?= Html::submitButton('+', ['id' => 'addTreatmentRow', 'name' => 'addTreatmentRow', 'value' => 'true', 'class' => 'btn btn-info btn-sm']) ?> -->
        <!-- <?= Html::submitButton('-', ['id' => 'removeTreatmentRow', 'name' => 'removeTreatmentRow', 'value' => 'true', 'class' => 'btn btn-danger btn-sm']) ?> -->
        <?php } ?>
        <input type="hidden" id="countTreatment" name="countTreatment" value="<?php echo count($modelTreatment); ?>">
        <table id="treatment-table">
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
                <td>
                    <!-- <?= $form->field($modelTreatment, "[$index]treatment_code")->dropDownList($treatment_code,['class' => 'treatmentCode',
                        'prompt'=> Yii::t('app','Select treatment code'),'maxlength' => true, 'disabled' => $print_readonly])->label(false) ?> -->
                    
                    <?= $form->field($modelTreatment, "[$index]treatment_code")->widget(kartik\select2\Select2::classname(), [
                        'data' => $treatment_code,
                        'disabled' => $print_readonly,
                        'options' => [
                            'placeholder' => Yii::t('app','Select treatment code'), 
                            'class' => 'treatmentCode',
                            'onchange' => "treatmentCode('{$url}');"
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '220px',
                        ],
                    ])->label(false); ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?= $form->field($modelTreatment ,"[$index]treatment_name")->textInput(['maxlength' => true, 'class' => 'treatmentName',
                        'readonly' => true, 'disabled' => $print_readonly, 'style' => 'width: 280px'])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                <div class="col-sm-6">
                    <td>
                        <?php 
                            if(empty( Yii::$app->request->get('bill_uid'))){ 
                                if($initial_ward_class == "1a"){?>
                                    <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '1_unit_cost'])->label(false) ?>
                                <?php 
                                }
                
                                else if($initial_ward_class == "1b"){?>
                                    <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '1_unit_cost'])->label(false) ?>
                                <?php 
                                }

                                else if($initial_ward_class == "1c"){?>
                                    <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                         'class' => '1_unit_cost'])->label(false) ?>
                                <?php 
                                }

                                else if($initial_ward_class == "2"){?>
                                    <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '2_unit_cost'])->label(false) ?>
                                <?php 
                                }

                                else if($initial_ward_class == "3"){?>
                                    <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput([ 'disabled' => true,
                                        'class' => '3_unit_cost'])->label(false) ?>
                                <?php 
                                }
                            }
                            else{ ?>
                                <?= $form->field($modelTreatment, "[$index]item_per_unit_cost_rm")->textInput(
                                    [ 'readonly' => true, 'disabled' => $print_readonly])->label(false) ?>
                                <?php    
                            }
                        ?>
                    </td>
                </div>

                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?= $form->field($modelTreatment, "[$index]item_count")->textInput(['class' => 'item_num',
                        'disabled' => $print_readonly, 'onchange' => 'calculateItemTotalCost();'])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?= $form->field($modelTreatment, "[$index]item_total_unit_cost_rm")->textInput([ 'readonly' => true,
                        'disabled' => $print_readonly, 'class' => 'item_total_cost'])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
                    <?php }else{ 
                        if(!empty($modelTreatment->treatment_details_uid)){
                        ?>
                            <?= Html::a("x", ["/treatment_details/delete", "treatment_details_uid" => $modelTreatment->treatment_details_uid,
                                'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-xs", "id"=>"treatmentDelete"]) ?>
                    <?php }
                    } ?>
                </td>
            </tr>
            <?php } ?>
        </table>

        <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
        <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveTreatment', 'name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "calculateItemCost('{$url}');"]) ?>
        <?= Html::submitButton('+', ['id' => 'addTreatmentRow', 'name' => 'addTreatmentRow', 'value' => 'true', 'class' => 'btn btn-success']) ?>
        <?php }else{ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveTreatment', 'name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "calculateItemCost('{$url}');"]) ?>
        <?= Html::submitButton('+', ['id' => 'addTreatmentRow', 'name' => 'addTreatmentRow', 'value' => 'true', 'class' => 'btn btn-success']) ?>
        <?php } ?>
    <?php kartik\form\ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</a>

<script>
    function calculateItemTotalCost() {
        $('.treatmentCode', document).each(function(index, item) {
            var treatmentCode = this.value;

            var itemPerUnit = $('#treatment_details-' + index + '-item_per_unit_cost_rm').val();
            var itemCount = $('#treatment_details-' + index + '-item_count').val();

            if (itemCount != '' && itemPerUnit != "") {
                var totalCost = parseFloat(itemPerUnit) * parseFloat(itemCount);
                $('#treatment_details-' + index + '-item_total_unit_cost_rm').val(totalCost);
            }
        });
    }

    function calculateItemCost(url) {
        $('.treatmentCode', document).each(function(index, item){
            var billClass = $('#wardClass').val();
            var treatmentCode = this.value;
            $.get(url, {treatment : treatmentCode}, function(data){
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

                var itemPerUnit = $('#treatment_details-' + index + '-item_per_unit_cost_rm').val();
                var itemCount = $('#treatment_details-' + index + '-item_count').val();

                if (itemCount != '' && itemPerUnit != "") {
                    var totalCost = parseFloat(itemPerUnit) * parseFloat(itemCount);
                    $('#treatment_details-' + index + '-item_total_unit_cost_rm').val(totalCost);
                }
            });
        });
    }

    function treatmentCode(url){
        $('.treatmentCode', document).each(function(index, item){
            var billClass = $('#wardClass').val();
            $(document).on('change', '#treatment_details-'+index+'-treatment_code', function() {
                var treatmentCode = this.value;
                $.get(url, {treatment : treatmentCode}, function(data){
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
    }
</script>

<?php 
$script = <<< JS
$(document).on('click', '#saveTreatment', function(e){
    var form = $(this);
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),

        success: function (data) {
            location.reload();
        },

        error: function () {
            alert("Something went wrong");
        }
    });
})

$(document).on('click', '#treatmentDelete', function(e){
    var form = $(this);
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),

        success: function (data) {
            location.reload();
        },

        error: function () {
            alert("Something went wrong");
        }
    });
})
JS;
$this->registerJS($script);
?>