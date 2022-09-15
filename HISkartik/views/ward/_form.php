<?php
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use app\models\Patient_admission;
use app\models\Cancellation;
use app\models\Bill;
use app\models\Ward;
use app\models\Fpp;
use yii\helpers\Url;
use yii\widgets\Pjax;

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
->from('patient_admission')
->where(['rn'=> Yii::$app->request->get('rn')])
->all();

$ward_code = "";
foreach($rows as $row){
    $ward_code = $row['initial_ward_code'];
} 

$rows_ward = (new \yii\db\Query())
->select('*')
->from('lookup_ward')
->all();

$wardcode = array();
$wardcode[''] = '';
foreach($rows_ward as $row_ward){
  $wardcode[$row_ward['ward_code']] = $row_ward['ward_code'] . ' - ' . $row_ward['ward_name'];
} 

$lockedWardCode = array();
$rows_ward = (new \yii\db\Query())
->from('ward')
->where(['bill_uid' => Yii::$app->request->get('bill_uid')])
->all();

foreach($rows_ward as $row_ward){
    $lockedWardCode[$row_ward['ward_code']] = $row_ward['ward_code'] . ' - ' . $row_ward['ward_name'];
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

$url = Url::toRoute(['/bill/ward']);
$urlDate = Url::toRoute(['/bill/date']);
$urlSubmit = Url::toRoute(['/ward/update', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn'), '#' => 'ward']);
$urlWardRow = Url::toRoute(['/ward/wardrow', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn')]);
$urlWard = Url::toRoute(['/ward/ward']);

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

<a name="ward">
    <?php Pjax::begin(['id' => 'pjax-ward-form']); ?>
    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'ward-form',
        'options' => ['data-pjax' => true],
        'type' => 'vertical',
        'action' =>  Url::toRoute(['/bill/generate', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn'), '#' => 'ward']),
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    ?>

        <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
        <?php }else{ ?>
        <!-- <?= Html::submitButton('+', ['id' => 'addWardRow', 'name' => 'addWardRow', 'value' => 'true', 'class' => 'btn btn-info btn-sm']) ?> -->
        <!-- <?= Html::submitButton('-', ['name' => 'removeWardRow', 'value' => 'true', 'class' => 'btn btn-danger btn-sm']) ?> -->
        <?php } ?>
        <input type="hidden" id="countWard" name="countWard" value="<?php echo count($modelWard); ?>">
        <input type="hidden" id="ward-bill-uid" name="ward-bill-uid" value="<?php echo Yii::$app->request->get('bill_uid') ?>">
        <input type="hidden" id="wardURL" name="wardURL" value="<?php echo $url ?>">
        <input type="hidden" id="dateURL" name="dateURL" value="<?php echo $urlDate ?>">
        <input type="hidden" id="WardRowURL" name="WardRowURL" value="<?php echo $urlWardRow ?>">
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
                    <?php 
                        if(!empty($admission_model->initial_ward_code && empty($modelWard->ward_code) && $index == 0)){
                        ?>
                            <?= $form->field($modelWard, "[$index]ward_code")->dropDownList(empty($isGenerated) ? $wardcode : $lockedWardCode,[
                                'class' => 'wardCode',
                                'maxlength' => true, 
                                'disabled' => empty($isGenerated) ? false : true, 
                                'onchange' => "wardCode('{$url}');",
                                'value' => $admission_model->initial_ward_code,
                                'placeholder' => Yii::t('app','Select ward code'), 
                            ])->label(false) ?>
                        
                        <!-- <?= $form->field($modelWard, "[$index]ward_code")->widget(kartik\select2\Select2::classname(), [
                            'data' => empty($isGenerated) ? $wardcode : $lockedWardCode,
                            'disabled' => empty($isGenerated) ? false : true,
                            'options' => [
                                'placeholder' => Yii::t('app','Select ward code'), 
                                'class' => 'wardCode',
                                'onchange' => "wardCode('{$url}');",
                                'value' => $admission_model->initial_ward_code,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'width' => '220px',
                            ],
                        ])->label(false); ?> -->
                    <?php 
                    }
                    else{
                    ?>
                        <?= $form->field($modelWard, "[$index]ward_code")->dropDownList(empty($isGenerated) ? $wardcode : $lockedWardCode,[
                                'class' => 'wardCode',
                                'maxlength' => true, 
                                'disabled' => empty($isGenerated) ? false : true, 
                                'onchange' => "wardCode('{$url}');",
                                'placeholder' => Yii::t('app','Select ward code'), 
                                'value' => $modelWard->ward_code,
                            ])->label(false) ?>

                        <!-- <?= $form->field($modelWard, "[$index]ward_code")->widget(kartik\select2\Select2::classname(), [
                            'data' => empty($isGenerated) ? $wardcode : $lockedWardCode,
                            'disabled' => empty($isGenerated) ? false : true,
                            'options' => [
                                'placeholder' => Yii::t('app','Select ward code'), 
                                'class' => 'wardCode',
                                'value' => $modelWard->ward_code,
                                'onchange' => "wardCode('{$url}');",
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'width' => '220px',
                            ],
                        ])->label(false); ?> -->
                    <?php
                    }
                    ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?= $form->field($modelWard, "[$index]ward_name")->textInput(['tabindex' => '-1', 'maxlength' => true, 'class' => 'wardName',
                    'value'=>$modelWard->ward_name,  'readonly' => true, 'disabled' => empty($isGenerated) ? false : true, 'style' => 'width: 180px'])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <?php 
                    if($admission_model->initial_ward_code != "UNKNOWN" && empty($modelWard->ward_code) && $index == 0){
                    ?>
                        <?= $form->field($modelWard, "[{$index}]ward_start_datetime")->widget(DateTimePicker::classname(),[
                        'options' => ['class' => 'start_date', 'disabled' => empty($isGenerated) ? false : true, 'value' => $admission_model->entry_datetime],
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],
                        // 'type' => DateTimePicker::TYPE_INPUT,
                        'pluginEvents' => [
                            'change' => "function () {
                                calculateDays();
                            }",
                        ],])->label(false)?>
                    <?php 
                    }
                    else{
                    ?>
                        <?= $form->field($modelWard, "[{$index}]ward_start_datetime")->widget(DateTimePicker::classname(),[
                        'options' => ['class' => 'start_date', 'disabled' => empty($isGenerated) ? false : true,],
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],
                        // 'type' => DateTimePicker::TYPE_INPUT,
                        'pluginEvents' => [
                            'change' => "function () {
                                calculateDays();
                            }",
                        ],])->label(false)?>
                    <?php
                    }
                    ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><?= $form->field($modelWard, "[{$index}]ward_end_datetime")->widget(DateTimePicker::classname(),[
                        'options' => ['class' => 'end_date', 'disabled' => empty($isGenerated) ? false : true,], 
                        'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii'],   
                        // 'type' => DateTimePicker::TYPE_INPUT,
                        'pluginEvents' => [
                            'change' => "function () {
                                calculateDays();
                            }",
                        ],])->label(false)?></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                <td><?= $form->field($modelWard, "[$index]ward_number_of_days")->textInput(['tabindex' => '-1','maxlength' => true,
                        'class' => 'day', 'readonly' => true, 'disabled' => empty($isGenerated) ? false : true,])->label(false) ?>
                </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style="vertical-align: top;">
                    <?php if($isGenerated && Yii::$app->request->get('bill_uid')){ ?>
                    <?php }else{ 
                        if(!empty($modelWard->ward_uid)){ ?>
                            <?= Html::a("x", ["/ward/delete", "ward_uid" => $modelWard->ward_uid, 'bill_uid' => Yii::$app->request->get('bill_uid'),
                                'rn' => Yii::$app->request->get('rn')], ["class"=>"btn btn-danger btn-sm", "id"=>"wardDelete"]) ?>
                    <?php }
                    } ?>
                </td>
            </tr>
            <?php } ?>
        </table>

        <!-- <?php if( $isGenerated && Yii::$app->request->get('bill_uid')){ ?>
        <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveWard', 'name' => 'saveWard', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'calculateDays();', 'disabled' => $disabled]) ?>
        <?= Html::submitButton('+', ['id' => 'addWardRow', 'name' => 'addWardRow', 'value' => 'true', 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?php }else{ ?>
        <?= Html::submitButton(Yii::t('app','Update'), ['id' => 'saveWard', 'name' => 'saveWard', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => 'calculateDays();', 'disabled' => $disabled]) ?>
        <?= Html::submitButton('+', ['id' => 'addWardRow', 'name' => 'addWardRow', 'value' => 'true', 'class' => 'btn btn-success', 'disabled' => $disabled]) ?>
        <?php } ?> -->
    <?php kartik\form\ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</a>

<script>
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

                if (date1.getDate() == date2.getDate() && date1.getMonth() == date2.getMonth())
                    days = 1;

                $("#ward-" + i + "-ward_number_of_days").val(days);

            }
        }
    }

    function wardCode(url){
        $('.wardCode', document).each(function(index, item){
            $(document).on('change', '#ward-'+index+'-ward_code', function() {
                var wardCode = this.value;
                $.get(url, {ward : wardCode}, function(data){
                    var data = $.parseJSON(data);
                    $('#ward-'+index+'-ward_name').attr('value', data.ward_name);
                });
            });
        });
    }

    function submitWardForm(url, urlWard, type){
        var form = $('#ward-form');
        var formData = form.serialize();
        var countWard = document.getElementById('countWard').value;

        $.ajax({
            url: url,
            type: form.attr("method"),
            data: formData,

            success: function (data) {
                flag = 0;

                if(type == 'insert'){
                    $.get(urlWard, {bill_uid : '<?php echo Yii::$app->request->get('bill_uid') ?>'}, function(data){
                        var data = $.parseJSON(data);                 
                        document.getElementById('wardTotal').innerHTML = '<?php echo Yii::t('app','Total') ?>' + ' : ' + data.wardTotal + '&nbsp&nbsp&nbsp&nbsp&nbsp';
                        document.getElementById('bill-bill_generation_billable_sum_rm').value = data.billAble;
                        document.getElementById('bill-bill_generation_final_fee_rm').value = data.finalFee;
                    });
                    addWardRow('');
                }
                
                if(type == 'update'){
                    $.get(urlWard, {bill_uid : '<?php echo Yii::$app->request->get('bill_uid') ?>'}, function(data){
                        var data = $.parseJSON(data);                 
                        document.getElementById('wardTotal').innerHTML = '<?php echo Yii::t('app','Total') ?>' + ' : ' + data.wardTotal + '&nbsp&nbsp&nbsp&nbsp&nbsp';
                        document.getElementById('bill-bill_generation_billable_sum_rm').value = data.billAble;
                        document.getElementById('bill-bill_generation_final_fee_rm').value = data.finalFee;
                    });
                    addWardRow('update');
                }
            },
        });
    }

    function addWardRow(type) {
        var addRow = document.getElementById('WardRowURL').value;
        var countWard = $('.wardCode').length;

        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange  = function() {
            if(xhttp.readyState == 4 && xhttp.status == 200){
                document.getElementById("ward-div").innerHTML = this.responseText;

                $('.wardCode', document).each(function(index, item, event) {
                    $('#ward-'+index+'-ward_code').select2({
                        placeholder: '<?php echo Yii::t('app', 'Select ward code'); ?>',
                        width: '220px',
                    });
                });

                // $('.start_date', document).each(function(index, item, event) {
                //     $('#ward-'+index+'-ward_start_datetime').datetimepicker(
                //         { showOn: "focus"}
                //     )
                // });

                // $('.end_date', document).each(function(index, item, event) {
                //     $('#ward-'+index+'-ward_end_datetime').datetimepicker(
                //         { showOn: "focus"}
                //     )
                // });

                $.pjax.reload({container: '#pjax-ward-form'});

                document.getElementById(focusID).focus();
            }
        }
        if(type == 'update'){
            xhttp.open("GET", addRow + "&countWard=" + countWard + "&update=true", true);
        }
        else{
            xhttp.open("GET", addRow + "&countWard=" + countWard, true);
        }
        xhttp.send();
    }

    document.addEventListener("keypress", function(event) {
        if(event.keyCode == 13){
            var countWard = document.getElementById('countWard').value;
            focusID = document.activeElement.id;
            
            for(var i = 0; i < countWard; i++){
                var ward_code = document.querySelector('#ward-'+i+'-ward_code');
                var ward_name = document.querySelector('#ward-'+i+'-ward_name');
                var start_date = document.querySelector('#ward-'+i+'-ward_start_datetime');
                var end_date = document.querySelector('#ward-'+i+'-ward_end_datetime');
                var days = document.querySelector('#ward-'+i+'-ward_number_of_days');

                if(document.activeElement == ward_code || 
                    document.activeElement == ward_name || 
                    document.activeElement == start_date || 
                    document.activeElement == end_date || 
                    document.activeElement == days){

                    calculateDays();

                    if(document.getElementById('ward-'+(countWard - 1)+'-ward_code').value == '' || 
                        document.getElementById('ward-'+(countWard - 1)+'-ward_start_datetime').value == '' || 
                        document.getElementById('ward-'+(countWard - 1)+'-ward_end_datetime').value == ''){
                        submitWardForm('<?php echo "{$urlSubmit}" ?>', '<?php echo "{$urlWard}" ?>', '<?php echo "update" ?>');
                    }
                    else{
                        submitWardForm('<?php echo "{$urlSubmit}" ?>', '<?php echo "{$urlWard}" ?>', '<?php echo "insert" ?>');
                    }
                }
            }            
        }
    });
</script>

<?php 
$script = <<< JS
$(document).on('click', '#saveWard', function(e){
    var wardForm = $(this);
    $.ajax({
        url: wardForm.attr("action"),
        type: wardForm.attr("method"),

        success: function (data) {
            location.reload();
        },

        error: function () {
            alert("Something went wrong");
        }
    });
})

$(document).on('click', '#wardDelete', function(e){
    var wardForm = $(this);
    $.ajax({
        url: wardForm.attr("action"),
        type: wardForm.attr("method"),

        success: function (data) {
            location.reload();
        },

        error: function () {
            alert("Something went wrong");
        }
    });
})

$(document).on('click', '#addWardRow', function(e){
    var wardForm = $(this);
    $.ajax({
        url: wardForm.attr("action"),
        type: wardForm.attr("method"),

        success: function (data) {
            $('.wardCode', document).each(function(index, item){
                var wardCode = this.value;
                var url = document.getElementById("wardURL").value;

                $.get(url, {ward : wardCode}, function(data){
                    var data = $.parseJSON(data);
                    $('#ward-'+index+'-ward_name').attr('value', data.ward_name);
                });
            });
        },

        error: function () {
            alert("Something went wrong");
        }
    });
})

// Date Clashing
var billUid = $('#ward-bill-uid').val();
var url = document.getElementById("dateURL").value;

$.get(url, {bill_uid : billUid}, function(data){
    var data = $.parseJSON(data);

    for(var i = 0; i < data.length; i++){
        var start = new Date(data[i].ward_start_datetime).getTime();
        var end = new Date(data[i].ward_end_datetime).getTime();

        for(var j = 0; j < data.length; j++){
            var from = new Date(data[j].ward_start_datetime).getTime();
            var to = new Date(data[j].ward_end_datetime).getTime()
            if(j != i){
                if(start >= from && start <= to || end >= from && end <= to){
                    $('#ward-'+i+'-ward_start_datetime').addClass('textColor');
                    $('#ward-'+i+'-ward_end_datetime').addClass('textColor');
                }
            }
        }
    }
});

$(document).ready(function() {
    $('.wardCode', document).each(function(index, item, event) {
        $('#ward-'+index+'-ward_code').select2({
            placeholder: 'Select ward code',
            width: '220px',
        });
    });
});
JS;
$this->registerJS($script);
?>
