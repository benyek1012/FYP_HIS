<?php
use yii\helpers\Html;
use app\models\Patient_admission;
use app\models\Cancellation;
use app\models\Bill;
use app\models\Ward;
use app\models\Fpp;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\Treatment_details;

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

$lockedTreatmentCode = '';
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
$treatment_code[''] = '';
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

$lockedTreatmentCode = array();
$rows_treatment = (new \yii\db\Query())
->from('treatment_details')
->where(['bill_uid' => Yii::$app->request->get('bill_uid')])
->all();

foreach($rows_treatment as $row_treatment){
    $lockedTreatmentCode[$row_treatment['treatment_code']] = $row_treatment['treatment_code'] . ' - ' . $row_treatment['treatment_name'];
}

if(empty($print_readonly)) $print_readonly = false;

if($print_readonly)
{
    $this->registerJs(
        "$('#bill_details').CardWidget('collapse');
        $('#ward_div').CardWidget('collapse');
        $('#treatment_div').CardWidget('collapse');
        $('#fpp_div').CardWidget('collapse');"
    );
}

$checkFPP = Fpp::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);
if(empty($checkFPP)){
    $this->registerJs(
        "$('#fpp_div').CardWidget('collapse');"
    );
}

$url = Url::toRoute(['/bill/treatment']);
$urlTreatment = Url::toRoute(['/treatment_details/treatment']);
$urlSubmit = Url::toRoute(['/treatment_details/update', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn'), '#' => 'treatment']);
$urlTreatmentRow = Url::toRoute(['/treatment_details/treatmentrow', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn')]);

$cancellation = Cancellation::findAll(['cancellation_uid' => Yii::$app->request->get('rn')]);
if(!empty($cancellation)){
    $disabled = true;
    $linkDisabled = 'disabled-link';
}
else{
    $disabled = false;
    $linkDisabled = '';
}

$dbTreatment = Treatment_details::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);

?>


<a name="treatment">
    <?php Pjax::begin(['id' => 'pjax-treatment-form']); ?>
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
        <input type="hidden" id="TreatmentRowURL" name="TreatmentRowURL" value="<?php echo $urlTreatmentRow ?>">
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
            <?php foreach ($modelTreatment as $index => $model) { ?>
            <tr id='treatment-<?php echo $index ?>'>
                <td>
                    <!-- <?= $form->field($model, "[$index]treatment_code")->dropDownList($treatment_code,['class' => 'treatmentCode',
                        'prompt'=> Yii::t('app','Select treatment code'),'maxlength' => true, 'disabled' => $print_readonly])->label(false) ?> -->
                    
                    <!-- <?= $form->field($model, "[$index]treatment_code")->widget(kartik\select2\Select2::classname(), [
                        'data' => empty($isGenerated) ? $treatment_code : $lockedTreatmentCode,
                        'disabled' => empty($isGenerated) ? false : true,
                        'options' => [
                            'placeholder' => Yii::t('app','Select treatment code'), 
                            'class' => 'treatmentCode',
                            'onchange' => "treatmentCode('{$url}');",
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '220px',
                        ],
                    ])->label(false); ?> -->
                    
                    <?= $form->field($model, "[$index]treatment_code")->dropDownList(empty($isGenerated) ? $treatment_code : $lockedTreatmentCode,[
                        'class' => 'treatmentCode',
                        'maxlength' => true, 
                        'disabled' => empty($isGenerated) ? false : true, 
                        'onchange' => "treatmentCode('{$url}');",
                        'placeholder' => Yii::t('app','Select treatment code'), 
                    ])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?= $form->field($model ,"[$index]treatment_name")->textInput(['tabindex' => '-1', 'maxlength' => true, 'class' => 'treatmentName',
                        'readonly' => true, 'disabled' => empty($isGenerated) ? false : true, 'style' => 'width: 280px'])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                <div class="col-sm-6">
                    <td>
                        <?php 
                            if(empty( Yii::$app->request->get('bill_uid'))){ 
                                if($initial_ward_class == "1a"){?>
                                    <?= $form->field($model, "[$index]item_per_unit_cost_rm")->textInput(['tabindex' => '-1',  'disabled' => true,
                                        'class' => '1_unit_cost'])->label(false) ?>
                                <?php 
                                }
                
                                else if($initial_ward_class == "1b"){?>
                                    <?= $form->field($model, "[$index]item_per_unit_cost_rm")->textInput(['tabindex' => '-1', 'disabled' => true,
                                        'class' => '1_unit_cost'])->label(false) ?>
                                <?php 
                                }

                                else if($initial_ward_class == "1c"){?>
                                    <?= $form->field($model, "[$index]item_per_unit_cost_rm")->textInput(['tabindex' => '-1', 'disabled' => true,
                                         'class' => '1_unit_cost'])->label(false) ?>
                                <?php 
                                }

                                else if($initial_ward_class == "2"){?>
                                    <?= $form->field($model, "[$index]item_per_unit_cost_rm")->textInput(['tabindex' => '-1', 'disabled' => true,
                                        'class' => '2_unit_cost'])->label(false) ?>
                                <?php 
                                }

                                else if($initial_ward_class == "3"){?>
                                    <?= $form->field($model, "[$index]item_per_unit_cost_rm")->textInput(['tabindex' => '-1', 'disabled' => true,
                                        'class' => '3_unit_cost'])->label(false) ?>
                                <?php 
                                }
                            }
                            else{ ?>
                                <?= $form->field($model, "[$index]item_per_unit_cost_rm")->textInput(
                                    ['tabindex' => '-1', 'readonly' => true, 'disabled' => empty($isGenerated) || empty($isPrinted) ? false : true,])->label(false) ?>
                                <?php    
                            }
                        ?>
                    </td>
                </div>

                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($model, "[$index]item_count")->textInput(['class' => 'item_num',
                        'disabled' => empty($isGenerated) ? false : true, 'onchange' => 'calculateItemTotalCost();'])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?= $form->field($model, "[$index]item_total_unit_cost_rm")->textInput(['tabindex' => '-1', 'readonly' => true,
                        'disabled' => empty($isGenerated) ? false : true, 'class' => 'item_total_cost'])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="vertical-align: top;">
                    <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
                    <?php }else{ 
                        if(!empty($model->treatment_details_uid)){
                        ?>
                            <?= Html::a("x", ["/treatment_details/delete", "treatment_details_uid" => $model->treatment_details_uid,
                                'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-sm", "id"=>"treatmentDelete", 'tabindex' => '-1']) ?>
                    <?php }
                    } ?>
                </td>
            </tr>
            <?php } ?>
        </table>

        <!-- <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
        <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveTreatment', 'name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "calculateItemCost('{$url}');", 'disabled' => $disabled]) ?>
        <?= Html::submitButton('+', ['id' => 'addTreatmentRow', 'name' => 'addTreatmentRow', 'value' => 'true', 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?php }else{ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveTreatment', 'name' => 'saveTreatment', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "calculateItemCost('{$url}');", 'disabled' => $disabled]) ?>
        <?= Html::submitButton('+', ['id' => 'addTreatmentRow', 'name' => 'addTreatmentRow', 'value' => 'true', 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?php } ?> -->
    <?php kartik\form\ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</a>

<script>
    var focusID = '';

    function calculateItemTotalCost() {
        $('.treatmentCode', document).each(function(index, item) {
            var treatmentCode = this.value;
            var cost = $('#treatment_details-' + index + '-item_total_unit_cost_rm').val();

            var itemPerUnit = $('#treatment_details-' + index + '-item_per_unit_cost_rm').val();
            var itemCount = $('#treatment_details-' + index + '-item_count').val();

            if (itemCount != '' && itemPerUnit != "") {
                var totalCost = parseFloat(itemPerUnit) * parseFloat(itemCount);
                $('#treatment_details-' + index + '-item_total_unit_cost_rm').val(totalCost);
            }

            if(cost != totalCost && cost != ''){
                // document.getElementById('treatment_details-' + index + '-item_total_unit_cost_rm').style.backgroundColor = '#ffc107';
                document.getElementById('treatment-'+index).style.backgroundColor = '#ffc107';
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

                    // document.getElementById('treatment_details-'+index+'-treatment_name').style.backgroundColor = '#ffc107';
                    // document.getElementById('treatment_details-'+index+'-item_per_unit_cost_rm').style.backgroundColor = '#ffc107';

                    document.getElementById('treatment-'+index).style.backgroundColor = '#ffc107';
                });
            });
        });
    }

    function submitTreatmentForm(url, urlTreatment, type){
        var form = $('#treatment-form');
        var formData = form.serialize();
        var countTreatment = document.getElementById('countTreatment').value;

        $.ajax({
            url: url,
            type: form.attr("method"),
            data: formData,

            success: function (data) {
                flag = 0;

                if(type == 'insert'){
                    $.get(urlTreatment, {bill_uid : '<?php echo Yii::$app->request->get('bill_uid') ?>'}, function(data){
                        var data = $.parseJSON(data);                 
                        document.getElementById('treatmentTotal').innerHTML = '<?php echo Yii::t('app','Total') ?>' + ' : ' + data.treatmentTotal + '&nbsp&nbsp&nbsp&nbsp&nbsp';
                        document.getElementById('bill-bill_generation_billable_sum_rm').value = data.billAble;
                        document.getElementById('bill-bill_generation_final_fee_rm').value = data.finalFee;
                    });
                    if(data == 'success'){
                        addTreatmentRow('');
                    }
                }
                
                if(type == 'update'){
                    $.get(urlTreatment, {bill_uid : '<?php echo Yii::$app->request->get('bill_uid') ?>'}, function(data){
                        var data = $.parseJSON(data);                 
                        document.getElementById('treatmentTotal').innerHTML = '<?php echo Yii::t('app','Total') ?>' + ' : ' + data.treatmentTotal + '&nbsp&nbsp&nbsp&nbsp&nbsp';
                        document.getElementById('bill-bill_generation_billable_sum_rm').value = data.billAble;
                        document.getElementById('bill-bill_generation_final_fee_rm').value = data.finalFee;
                    });
                    if(data == 'success'){
                        addTreatmentRow('update');
                    }
                }
            }
        });
    }

    function addTreatmentRow(type) {
        var addRow = document.getElementById('TreatmentRowURL').value;
        var countTreatment = $('.treatmentCode').length;

        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange  = function() {
            if(xhttp.readyState == 4 && xhttp.status == 200){
                document.getElementById("treatment-div").innerHTML = this.responseText;

                $('.treatmentCode', document).each(function(index, item, event) {
                    $('#treatment_details-'+index+'-treatment_code').select2({
                        placeholder: '<?php echo Yii::t('app', 'Select treatment code'); ?>',
                        width: '220px',
                        matcher: function(params, data) {
                            return matchTreatment(params, data);
                        },
                    });
                });
                
                $(document).on('select2:open', () => {
                    document.querySelector('.select2-search__field').focus();
                });

                // $.pjax.reload({container: '#pjax-treatment-form'});

                document.getElementById(focusID).focus();
            }
        }
        if(type == 'update'){
            xhttp.open("GET", addRow + "&countTreatment=" + countTreatment + "&update=true", true);
        }
        else{
            xhttp.open("GET", addRow + "&countTreatment=" + countTreatment, true);
        }
        xhttp.send();
    }

    document.addEventListener("keypress", function(event) {
        if(event.keyCode == 13){
            var countTreatment = document.getElementById('countTreatment').value;
            focusID = document.activeElement.id;
            
            for(var i = 0; i < countTreatment; i++){
                var treatment_code = document.querySelector('#treatment_details-'+i+'-treatment_code');
                var treatment_name = document.querySelector('#treatment_details-'+i+'-treatment_name');
                var item_cost = document.querySelector('#treatment_details-'+i+'-item_per_unit_cost_rm');
                var item_count = document.querySelector('#treatment_details-'+i+'-item_count');
                var total_cost = document.querySelector('#treatment_details-'+i+'-item_total_unit_cost_rm');

                if(document.activeElement == treatment_code || 
                    document.activeElement == treatment_name || 
                    document.activeElement == item_cost || 
                    document.activeElement == item_count || 
                    document.activeElement == total_cost){

                    calculateItemTotalCost();

                    if(document.getElementById('treatment_details-'+(countTreatment - 1)+'-treatment_code').value == '' || 
                        document.getElementById('treatment_details-'+(countTreatment - 1)+'-item_count').value == ''){
                        submitTreatmentForm('<?php echo "{$urlSubmit}" ?>', '<?php echo "{$urlTreatment}" ?>', '<?php echo "update" ?>');
                    }
                    else{
                        submitTreatmentForm('<?php echo "{$urlSubmit}" ?>', '<?php echo "{$urlTreatment}" ?>', '<?php echo "insert" ?>');
                    }
                }
            }            
        }
    });

    function matchTreatment(params, data) {
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
});

$(document).ready(function() {
    $('.treatmentCode', document).each(function(index, item, event) {
        $('#treatment_details-'+index+'-treatment_code').select2({
            placeholder: 'Select treatment code',
            width: '220px',
            matcher: function(params, data) {
                return matchTreatment(params, data);
            },
        });
    });
});
JS;
$this->registerJS($script);
?>