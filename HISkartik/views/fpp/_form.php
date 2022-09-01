<?php
use yii\helpers\Html;
use app\models\Patient_admission;
use app\models\Cancellation;
use app\models\Bill;
use app\models\Ward;
use app\models\Fpp;
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
->from('lookup_fpp')
->all();

$fpp_kod = array();
foreach($rows as $row){
    $fpp_kod[$row['kod']] = $row['kod'] . ' - ' . $row['name'];
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

$url = Url::toRoute(['/bill/fpp']);
$urlCost = Url::toRoute(['/bill/cost']);
$urlSubmit = Url::toRoute(['/fpp/update', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn'), '#' => 'fpp']);

$cancellation = Cancellation::findAll(['cancellation_uid' => Yii::$app->request->get('rn')]);
if(!empty($cancellation)){
    $disabled = true;
    $linkDisabled = 'disabled-link';
}
else{
    $disabled = false;
    $linkDisabled = '';
}
?>

<a name="fpp">
    <?php Pjax::begin(); ?>
    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'fpp-form',
        'options' => ['data-pjax' => true],
        'type' => 'vertical',
        'action' =>  Url::toRoute(['/bill/generate', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn'), '#' => 'fpp']),
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]);
    ?>
        <input type="hidden" id="ward-bill-uid" name="ward-bill-uid" value="<?php echo Yii::$app->request->get('bill_uid') ?>">
        <input type="hidden" id="costURL" name="dateUcostURLRL" value="<?php echo $urlCost ?>">
        <table id="fpp-table">
            <tr>
                <td><?php echo Yii::t('app','Kod');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo Yii::t('app','Name');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo Yii::t('app','Additional Details');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo Yii::t('app','Min Cost Per Unit (RM)');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo Yii::t('app','Cost Per Unit (RM)');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo Yii::t('app','Max Cost Per Unit (RM)');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo Yii::t('app','Number of Units');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?php echo Yii::t('app','Total Cost (RM)');?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td> </td>
            </tr>
            <?php foreach ($modelFPP as $index => $modelFPP) { ?>
            <tr>
                <td>                    
                    <?= $form->field($modelFPP, "[$index]kod")->widget(kartik\select2\Select2::classname(), [
                        'data' => $fpp_kod,
                        // 'disabled' => $print_readonly == false? $disabled : $print_readonly,
                        'disabled' => empty($isGenerated) ? false : true,
                        'options' => [
                            'placeholder' => Yii::t('app','Select FPP Kod'), 
                            'class' => 'fppKod',
                            'onchange' => "fppKod('{$url}'); calculateFPPTotalCost()",
                            'onfocusout' => "submitFPPForm('{$index}', '{$urlSubmit}')",
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '220px',
                        ],
                    ])->label(false); ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($modelFPP ,"[$index]name")->textInput(['maxlength' => true, 'readonly' => true, 'disabled' => empty($isGenerated) ? false : true,])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($modelFPP, "[$index]additional_details")->textInput(['disabled' => empty($isGenerated) ? false : true, 'onchange' => "calculateFPPTotalCost()"])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($modelFPP, "[$index]min_cost_per_unit")->textInput(['class' => 'minCostPerUnit', 'readonly' => true, 'disabled' => empty($isGenerated) ? false : true,])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($modelFPP, "[$index]cost_per_unit")->textInput(['class' => 'costPerUnit', 'disabled' => empty($isGenerated) ? false : true, 'onchange' => "checkCostRange('{$url}'); calculateFPPTotalCost()", 'onfocusout' => "submitFPPForm('{$index}', '{$urlSubmit}')",])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($modelFPP, "[$index]max_cost_per_unit")->textInput(['class' => 'maxCostPerUnit', 'readonly' => true, 'disabled' => empty($isGenerated) ? false : true,])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($modelFPP, "[$index]number_of_units")->textInput(['class' => 'numberOfUnits', 'disabled' => empty($isGenerated) ? false : true, 'onchange' => 'calculateFPPTotalCost();', 'onfocusout' => "submitFPPForm('{$index}', '{$urlSubmit}')",])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?= $form->field($modelFPP, "[$index]total_cost")->textInput(['readonly' => true, 'disabled' => empty($isGenerated) ? false : true,])->label(false) ?>
                </td>
                <td>
                    <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
                    <?php }else{ 
                        if(!empty($modelFPP->kod)){
                        ?>
                            <?= Html::a("x", ["/fpp/delete", "kod" => $modelFPP->kod,
                                'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-xs", "id"=>"fppDelete"]) ?>
                    <?php }
                    } ?>
                </td>
            </tr>
            <?php } ?>
        </table>

        <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
        <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveFpp', 'name' => 'saveFpp', 'value' => 'true', 'onclick' => "checkMinMaxTotalCost('{$url}')", 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?= Html::submitButton('+', ['id' => 'addFppRow', 'name' => 'addFppRow', 'value' => 'true', 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?php }else{ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveFpp', 'name' => 'saveFpp', 'value' => 'true', 'onclick' => "checkMinMaxTotalCost('{$url}')", 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?= Html::submitButton('+', ['id' => 'addFppRow', 'name' => 'addFppRow', 'value' => 'true', 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?php } ?>
    <?php kartik\form\ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</a>

<script>
    function calculateFPPTotalCost() {
        $('.fppKod', document).each(function(index, item) {
            var kod = this.value;

            var costPerUnit = $('#fpp-' + index + '-cost_per_unit').val();
            var numberOfUnit = $('#fpp-' + index + '-number_of_units').val();

            if (costPerUnit != '' && numberOfUnit != "") {
                var totalCost = parseFloat(costPerUnit) * parseFloat(numberOfUnit);
                $('#fpp-' + index + '-total_cost').val(totalCost);
            }
        });
    }

    function checkMinMaxTotalCost(url) {
        $('.fppKod', document).each(function(index, item){
            var kod = this.value;
            $.get(url, {fpp : kod}, function(data){
                var data = $.parseJSON(data);
                $('#fpp-'+index+'-name').attr('value', data.name);
                $('#fpp-'+index+'-min_cost_per_unit').attr('value', data.min_cost_per_unit);
                $('#fpp-'+index+'-max_cost_per_unit').attr('value', data.max_cost_per_unit);

                var costPerUnit = $('#fpp-' + index + '-cost_per_unit').val();
                var numberOfUnit = $('#fpp-' + index + '-number_of_units').val();

                if (costPerUnit != '' && numberOfUnit != "") {
                    var totalCost = parseFloat(costPerUnit) * parseFloat(numberOfUnit);
                    $('#fpp-' + index + '-total_cost').val(totalCost);
                }
            });
        });
    }

    function fppKod(url){
        $('.fppKod', document).each(function(index, item){
            $(document).on('change', '#fpp-'+index+'-kod', function() {
                var kod = this.value;
                $.get(url, {fpp : kod}, function(data){
                    var data = $.parseJSON(data);
                    $('#fpp-'+index+'-name').attr('value', data.name);
                    $('#fpp-'+index+'-min_cost_per_unit').attr('value', data.min_cost_per_unit);
                    $('#fpp-'+index+'-max_cost_per_unit').attr('value', data.max_cost_per_unit);
                });
            });
        });
    }

    function checkCostRange(url){
        $('.costPerUnit', document).each(function(index, item){
            $(document).on('change', '#fpp-'+index+'-cost_per_unit', function() {
                var costPerUnit = this.value;
                var kod = document.getElementById('fpp-'+index+'-kod').value;

                $.get(url, {fpp : kod}, function(data){
                    var data = $.parseJSON(data);
                    $('#fpp-'+index+'-min_cost_per_unit').attr('value', data.min_cost_per_unit);
                    $('#fpp-'+index+'-max_cost_per_unit').attr('value', data.max_cost_per_unit);

                    if(costPerUnit <= data.min_cost_per_unit || costPerUnit > data.max_cost_per_unit){
                        $('#fpp-'+index+'-min_cost_per_unit').addClass('textColor');
                        $('#fpp-'+index+'-max_cost_per_unit').addClass('textColor');
                    }
                    else{
                        $('#fpp-'+index+'-min_cost_per_unit').removeClass('textColor');
                        $('#fpp-'+index+'-max_cost_per_unit').removeClass('textColor');
                    }
                });
            });
        });
    }

    function submitFPPForm(count, url){
        var form = $('#fpp-form');
        var formData = form.serialize();

        $.ajax({
            url: url,
            type: form.attr("method"),
            data: formData,

            success: function (data) {
                counted = parseInt(count) + 1;

                if(document.getElementById('fpp-'+count+'-kod').value == '' || 
                    document.getElementById('fpp-'+count+'-cost_per_unit').value == '' || 
                    document.getElementById('fpp-'+count+'-number_of_units').value == ''){
                    return false;
                }

                // if($('.end_date').length == document.getElementById('countWard').value){
                //     location.reload();
                // }
                if(counted == $('.fppKod').length || counted == $('.costPerUnit').length || counted == $('.numberOfUnits').length){
                    location.reload();  
                }
            },
        });
    }
</script>

<?php 
$script = <<< JS
$(document).on('click', '#saveFpp', function(e){
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

$(document).on('click', '#fppDelete', function(e){
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

var billUid = $('#ward-bill-uid').val();
var url = document.getElementById("costURL").value;

$.get(url, {bill_uid : billUid}, function(data){
    var data = $.parseJSON(data);

    for(var i = 0; i < data.length; i++){
        if(data[i].cost_per_unit <= data[i].min_cost_per_unit || data[i].cost_per_unit > data[i].max_cost_per_unit){
            $('#fpp-'+i+'-min_cost_per_unit').addClass('textColor');
            $('#fpp-'+i+'-max_cost_per_unit').addClass('textColor');
        }
        else{
            $('#fpp-'+i+'-min_cost_per_unit').removeClass('textColor');
            $('#fpp-'+i+'-max_cost_per_unit').removeClass('textColor');
        }
    }
});
JS;
$this->registerJS($script);
?>