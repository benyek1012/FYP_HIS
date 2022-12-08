<?php

use yii\helpers\Html;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Bill;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Receipt;
use yii\helpers\Url;
use app\models\Cancellation;


/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */

$url = Url::toRoute(['receipt/refresh']);

if($cancellation == true){
    $urlReceipt = Url::toRoute(['receipt/cancellation', 'rn' => Yii::$app->request->get('rn')]);
}
else{
    $urlReceipt = Url::toRoute(['receipt/create', 'rn' => Yii::$app->request->get('rn')]);
    $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn'), 'deleted' => 0]);
}
?>

<div class="receipt-form">

    <?php  
         // Once bill is generated, can't pay deposit. Only allow bill or refund
        // if(!empty($model_bill)  && (new Bill()) -> isGenerated($model_bill->rn))

        // Once bill is printed, can't pay deposit. Only allow bill or refund
        if(!empty($model_bill)  && (new Bill()) -> isPrinted($model_bill->rn))
        {
           // check receipt is refundable
            if(Receipt::checkRefunfable())
            {
                $receipt = array(
                    'bill'=> Yii::t('app','Bill'),
                    'refund'=> Yii::t('app','Refund'),
                    'exception' =>Yii::t('app','Exception')
                );
            }
            else  $receipt = array(
                'bill'=> Yii::t('app','Bill'),
                'exception' =>Yii::t('app','Exception')
            );
        }
        else
            $receipt = array(
                'deposit'=> Yii::t('app','Deposit'),
                'refund'=>  Yii::t('app','Refund'),
                'exception' =>Yii::t('app','Exception')
            );
        
        $rows_payment = (new \yii\db\Query())
        ->select('*')
        ->from('lookup_general')
        ->where(['category'=> 'Payment Method'])
        ->orderBy(['lookup_general_uid' => SORT_DESC])
        ->all();

        $method = array();
        foreach ($rows_payment as $row_payment){
            $method[$row_payment['name']] = $row_payment['name'];
        }

        $temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
        $temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

        if($temp2->hasValidIC() == true) $account_code= '018/76303';
        else $account_code = '018/76302';


        $checked_name = "";
        if(!is_null($temp2->name))
            $checked_name = $temp2->name;
        else $checked_name = "Unknown";

        // var_dump($checked_name);
        // exit;
        
        if(!empty($model_bill))
        {
            $rows = (new \yii\db\Query())
            ->select(['`patient_information`.`name`,`bill`.`guarantor_name`'])
            ->from('patient_information,  patient_admission, bill')
            ->where(['patient_information.patient_uid' => $temp->patient_uid])
            ->andWhere(['patient_admission.rn' => Yii::$app->request->get('rn')])
            ->andWhere('`patient_admission`.`patient_uid` = `patient_information`.`patient_uid`')
            ->andWhere('`bill`.`rn` = `patient_admission`.`rn`')
            ->andWhere(['=', '`bill`.`deleted`', 0])
            ->all();
        }
        else
        {
            $rows = (new \yii\db\Query())
            ->select(['`patient_information`.`name`'])
            ->from('patient_information,  patient_admission, bill')
            ->where(['patient_information.patient_uid' => $temp->patient_uid])
            ->andWhere(['patient_admission.rn' => Yii::$app->request->get('rn')])
            ->andWhere('`patient_admission`.`patient_uid` = `patient_information`.`patient_uid`')
            ->all();
        }

        $names = array();
        foreach($rows as $row){
            if($row['name'] == '')
                $row['name'] = "Unknown"; 
            $names[$row['name']] = $row['name'];

            if(!empty($model_bill))
                $names[$row['guarantor_name']] = $row['guarantor_name'];
        }
        // removes duplicate values from an array
        $names = array_unique($names);
        $names = array_filter($names);
    
        $form = kartik\form\ActiveForm::begin([
        'id' => 'receipt-form'.$index,
        'action' => $urlReceipt,
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);      ?>

    <?php 
    if($cancellation == false)
    {
    ?>
        <div class="row">
            <div class="col-lg-12">
                <?php 
                if(!empty(Yii::$app->request->get('rn'))){
            ?>
                <?= \hail812\adminlte\widgets\Callout::widget([
                    'type' => 'info',
                'body' => '<b>'.Yii::t('app','Billable Total').'</b>: '.(new Patient_admission())  -> get_billable_sum(Yii::$app->request->get('rn')).
                '<br/><b>'.Yii::t('app','Amount Due').'</b>: '.Yii::$app->formatter->asCurrency((new Bill()) -> getAmtDued(Yii::$app->request->get('rn'))).
                '<br/><b>'.Yii::t('app','Unclaimed Balance').'</b>: '.Yii::$app->formatter->asCurrency((new Bill()) -> getUnclaimed(Yii::$app->request->get('rn')))
                ]) ?>
                <?php } ?>
            </div>
        </div>
    <?php
    }
    else{ 
    ?>
        <div class="col-sm-6">
            <h2 class="m-0"><?php echo Yii::t('app','Receipt Cancellation Form') ?></h1>
        </div>
    <?php
    }
    ?>

    <?= $form->field($model, 'receipt_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Base64UID::generate(32)])->label(false); ?>

    <?= $form->field($model, 'kod_akaun')->textInput(['autocomplete' =>'off', 'readonly' => true, 'maxlength' => true, 'value' => $account_code]); ?>

    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false); ?>

    <?= $form->field($model, 'receipt_responsible')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="row">
        <div class="col-sm-6">
            <?php if($cancellation == false){ ?>
            <?php  if(!empty($model_bill)){ ?>
            <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=> Yii::t('app','Please select receipt'),
            'maxlength' => true, 'id' => 'receipt-receipt_type'.$index, 'onchange' => "myfunctionforType(this.value, '{$index}', '{$cancellation}')"]) ?>

            <!-- <?= $form->field($model, 'receipt_type')->widget(kartik\select2\Select2::classname(), [
                'data' => $receipt,
                'options' => ['placeholder' => Yii::t('app','Please select receipt'), 
                    'id' => 'receipt_type', 
                    'onchange' => 'myfunctionforType(this.value)',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumResultsForSearch' => 'Infinity',
                ],
            ]); ?> -->
            <?php }else{ ?>
            <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=> Yii::t('app','Please select receipt'),
            'maxlength' => true, 'id' => 'receipt-receipt_type'.$index, 'onchange' => "myfunctionforType(this.value, '{$index}', '{$cancellation}')"]) ?>

            <!-- <?= $form->field($model, 'receipt_type')->widget(kartik\select2\Select2::classname(), [
                'data' => $receipt,
                'options' => ['placeholder' => Yii::t('app','Please select receipt'), 
                    'id' => 'receipt_type', 
                    'onchange' => 'myfunctionforType(this.value)',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumResultsForSearch' => 'Infinity',
                ],
            ]); ?> -->
            <?php }
            } else{ ?>
                <?= $form->field($model, 'receipt_type')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'readonly' => true, 'id' => 'receipt-receipt_type'.$index]) ?>
            <?php } ?>
        </div>

        <div class="col-sm-6" id="bill_div<?php echo $index ?>">
        <?php if(!empty($model_bill)){ ?>
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'value' => $model_bill->bill_print_id, 'readonly' =>true]) ?>
            <?php }else{ ?>
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
            <?php } ?>
        </div>

        <div class="col-sm-6">
            <!-- <?php  if(!empty($model_bill)){
                    if((new Bill()) -> calculateFinalFee($model_bill->bill_uid) >= 0){
        ?>
            <?= $form->field($model, 'receipt_content_sum')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'value' => (new Bill()) -> calculateFinalFee($model_bill->bill_uid)]) ?>
            <?php }else{ ?>
            <?= $form->field($model, 'receipt_content_sum')->textInput(['autocomplete' =>'off', 'maxlength' => true,  'value' => (new Bill()) -> getUnclaimed(Yii::$app->request->get('rn'))]) ?>
            <?php }
            }else{ ?>
            <?= $form->field($model, 'receipt_content_sum')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'id' => 'receipt_sum']) ?>
            <?php } ?> -->
            <?= $form->field($model, 'receipt_content_sum')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'id' => 'receipt_sum']) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_description')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'receipt_content_payer_name')->dropDownList($names, 
                        ['prompt'=> Yii::t('app','Please select payer name'),'maxlength' => true]) ?> -->
            <?php if($cancellation == false) { ?>
                <?= $form->field($model, 'receipt_content_payer_name')->radioList($names, ['value' => $checked_name, 'custom' => true, 'inline' => true]); ?>
            <?php } else { ?>
                <?= $form->field($model, 'receipt_content_payer_name')->radioList($names, ['value' => $model->receipt_content_payer_name, 'id' => 'payer_name'.$index ,'custom' => true, 'inline' => true]); ?>
            <?php } ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_serial_number', 
                ['labelOptions' => [ 'id' => 'receipt_label'.$index]])->textInput(['autocomplete' =>'off', 'maxlength' => true, 
                    'readonly' => true, 'id' => 'serial_number'.$index]) ?>
        </div>

        <div class="col-sm-6">
            <?php if($cancellation == false){ ?>
            <?= $form->field($model, 'receipt_content_payment_method')->radioList($method, 
                    ['maxlength' => true, 'id' => 'radio', 'custom' => true, 'inline' => true, 'value' => 'cash']) ?>
            <?php } else { ?>
                <?= $form->field($model, 'receipt_content_payment_method')->radioList($method, 
                    ['maxlength' => true, 'id' => 'radio', 'custom' => true, 'inline' => true, 'id' => 'payment_method'.$index ,'value' => $model->receipt_content_payment_method]) ?>
            <?php } ?>
        
            <?= $form->field($model, 'payment_method_number')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>

        <!-- <div class="col-sm-6">
            <?= $form->field($model, 'receipt_serial_number', 
                ['labelOptions' => [ 'id' => 'receipt_label'.$index]])->textInput(['autocomplete' =>'off', 'maxlength' => true, 
                    'readonly' => true, 'id' => 'serial_number'.$index]) ?>
        </div> -->

        <?php
        if($cancellation == true)
        {
        ?>
            <?php $model_cancellation = new Cancellation();?>

            <div class="col-sm-6"></div>
            
            <div class="col-sm-6">
                <?= $form->field($model_cancellation, 'cancellation_uid')->hiddenInput(['maxlength' => true, 'value' => $model->receipt_uid])->label(false); ?>
                <?= $form->field($model_cancellation, 'table')->hiddenInput(['maxlength' => true, 'value' => 'receipt'])->label(false); //'value' => 'receipt - ' . $model->receipt_type ?>
                <!-- <?= $form->field($model_cancellation, 'replacement_uid')->hiddenInput(['maxlength' => true, 'value' => $model->receipt_uid])->label(false); ?> -->
                <?= $form->field($model_cancellation, 'reason')->textarea(['rows' => '6']) ?>
            </div>

            <div class="col-sm-6">
                <br>
                <label>Without Replacement</label>
                <?= $form->field($model_cancellation, 'checkbox_replacement')->checkbox(['id' => 'checkbox_replacement'.$index, 'uncheck' => false, 'value' => true])?>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="form-group" id="div_print">
            <!-- <?= Html::submitButton(Yii::t('app', 'Print'), ['class' => 'btn btn-success', 'id' => 'print']) ?> -->

            <?= Html::button(Yii::t('app','Print'), ['id' => 'print', 'name' => 'print', 'value' => 'true', 'class' => 'btn btn-success', 'onclick' => "confirmAction('{$index}');"]) ?>
        <?= Html::button(Yii::t('app', 'Custom serial number'), ['class' => 'btn btn-primary', 
            'onclick' => "(function () {
                 document.getElementById('serial_number'+{$index}).readOnly = false; 
                 document.getElementById('serial_number'+{$index}).value = '';
                 document.getElementById('serial_number'+{$index}).focus();
            })();" ]) ?>
        <?= Html::button(Yii::t('app', 'Refresh'), ['class' => 'btn btn-secondary', 'id' => 'refresh', 'onclick' => "refreshButton('{$url}', '{$index}')"]) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>

<script>
// hide bill receipt ID
document.getElementById("bill_div<?php echo $index ?>").style.display = "block";

if(document.getElementById('receipt-receipt_type<?php echo $index?>').value == 'refund' || document.getElementById('receipt-receipt_type<?php echo $index?>').value == 'exception'){
    document.getElementById("receipt_label<?php echo $index?>").innerHTML = '<?php echo Yii::t('app','Document Number');?>';
    document.getElementById("serial_number<?php echo $index?>").value = '';
    document.getElementById("serial_number<?php echo $index?>").readOnly = false;
}

function myfunctionforType(val, index, cancellation) {

    if (val == "refund" || val == "exception") {
        document.getElementById("receipt_label"+index).innerHTML = '<?php echo Yii::t('app','Document Number');?>';
        document.getElementById("serial_number"+index).value = '';
        document.getElementById("serial_number"+index).readOnly = false;
    } else {
        document.getElementById("receipt_label"+index).innerHTML = '<?php echo Yii::t('app','Receipt Serial Number')?>';
        refreshButton('<?php echo $url?>', index);
    }

    if (val == "bill") {
        if(cancellation == false){
            document.getElementById("receipt_sum").value =
                '<?php echo (new Bill()) -> getAmtDued(Yii::$app->request->get('rn'))?>';
        }
        // show bill receipt ID 
        document.getElementById("bill_div"+index).style.display = "block";
        document.getElementById("print").innerHTML = '<?php echo Yii::t('app','Print');?>';
    } else if (val == "refund") {
        if(cancellation == false){
            document.getElementById("receipt_sum").value =
                '<?php echo (new Bill()) -> getUnclaimed(Yii::$app->request->get('rn'))?>';
        }
        // hide bill receipt ID 
        document.getElementById("bill_div"+index).style.display = "none";
        document.getElementById("print").innerHTML = '<?php echo Yii::t('app','Print');?>';
    } else if (val == "deposit") {
        if(cancellation == false){
            document.getElementById("receipt_sum").value =
                '<?php echo (new Bill()) -> getAmtDued(Yii::$app->request->get('rn'))?>';
        }
        // hide bill receipt ID 
        document.getElementById("bill_div"+index).style.display = "none";
        document.getElementById("print").innerHTML = '<?php echo Yii::t('app','Print');?>';
    } else {
        if(cancellation == false){
            document.getElementById("receipt_sum").value = 0;
        }
        // hide bill receipt ID 
        document.getElementById("bill_div"+index).style.display = "none";
        document.getElementById("print").innerHTML = '<?php echo Yii::t('app','Save');?>';
    }
}

function refreshButton(url, index) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            document.getElementById("serial_number"+index).value = this.responseText;
            document.getElementById("serial_number"+index).readOnly = true;
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}

document.getElementById("div_no_print").style.display = "none";

function submitReceiptForm(index) {
    var form = $('#receipt-form'+index);
    var formData = form.serialize();
    
    form.submit();

    // $.ajax({
    //     url: form.attr("action"),
    //     type: form.attr("method"),
    //     data: formData,

    //     success: function(data) {
    //         // $.pjax.reload({container: '#pjax-patient-admission-form'});
    //     },
    // });
}

<?php if( Yii::$app->language == "en"){ ?>
// The function below will start the confirmation  dialog
function confirmAction(index) {
    var answer = confirm("Are you sure to print receipt?");
    if (answer) {
        // window.location.href = url + '&confirm=true';
        submitReceiptForm(index);
    }
}
<?php }else{?>
// The function below will start the confirmation  dialog
function confirmAction() {
    var answer = confirm("Adakah anda pasti mencetak resit?");
    if (answer) {
        submitReceiptForm();
    }
}
<?php } ?>


// For onchage hide and show payment method input
// document.querySelectorAll("#radio input[type='radio']").forEach(function(element) {
//     element.addEventListener('click', function() {
//         if (this.value == "cash" || this.value == "") {
//             document.getElementById("cheque_div").style.display = "none";
//             document.getElementById('card_div').style.display = "none";
//         } else if (this.value == "card") {
//             document.getElementById("cheque_div").style.display = "none";
//             document.getElementById('card_div').style.display = "block";
//         } else if (this.value == "cheque") {
//             document.getElementById("cheque_div").style.display = "block";
//             document.getElementById('card_div').style.display = "none";
//         }
//     });
// });
</script>