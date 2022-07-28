<?php

use yii\helpers\Html;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Bill;
use app\models\Patient_admission;
use app\models\Patient_information;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */

$url = Url::toRoute(['receipt/refresh']);
?>

<div class="receipt-form">

    <?php  
    // var_dump($userId);
    // exit();
        $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn')]);
        if(!empty($model_bill)  && (new Bill()) -> isGenerated($model_bill->rn))
            // Once bill is generated, can't pay deposit. Only allow bill or refund
            $receipt = array(
                'bill'=> Yii::t('app','Bill'),
                'refund'=> Yii::t('app','Refund'),
                'exception' =>Yii::t('app','Exception')
            );
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
        ->all();

        $method = array();
        foreach ($rows_payment as $row_payment){
            $method[$row_payment['name']] = $row_payment['name'];
        }

        //$payment_method = array(
        //    'cash'=> Yii::t('app','Cash'),
        //    'card'=> Yii::t('app','Debit/Credit Card'),
        //    'cheque'=> Yii::t('app','Cheque Numbers'),
        //);

        $temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
        $temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

        $checked_name = "";
        if($temp2->name != "")
            $checked_name = $temp2->name;
        else $checked_name = "Unknown";
        
        $rows = (new \yii\db\Query())
        ->select(['`patient_information`.`name`,`patient_admission`.`guarantor_name`'])
        ->from('patient_information,  patient_admission')
        ->where(['patient_information.patient_uid' => $temp->patient_uid])
        ->andWhere(['patient_admission.rn' => Yii::$app->request->get('rn')])
        ->andWhere('`patient_admission`.`patient_uid` = `patient_information`.`patient_uid`')
        ->all();
        
        $names = array();
        foreach($rows as $row){
            if($row['name'] == '')
                $row['name'] = "Unknown"; 
            $names[$row['name']] = $row['name'];
            $names[$row['guarantor_name']] = $row['guarantor_name'];
        }
        // removes duplicate values from an array
        $names = array_unique($names);
        $names = array_filter($names);
    
        $form = kartik\form\ActiveForm::begin([
        'id' => 'patient-information-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);      ?>

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


    <?= $form->field($model, 'receipt_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Base64UID::generate(32)])->label(false); ?>

    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false); ?>

    <?= $form->field($model, 'receipt_responsible')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="row">
        <div class="col-sm-6">
            <?php  if(!empty($model_bill)){ ?>
            <!-- <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=> Yii::t('app','Please select receipt'),
            'maxlength' => true, 'onchange' => 'myfunctionforType(this.value)']) ?> -->

            <?= $form->field($model, 'receipt_type')->widget(kartik\select2\Select2::classname(), [
                'data' => $receipt,
                'options' => ['placeholder' => Yii::t('app','Please select receipt'), 
                    'id' => 'receipt_type', 
                    'onchange' => 'myfunctionforType(this.value)',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumResultsForSearch' => 'Infinity',
                ],
            ]); ?>
            <?php }else{ ?>
            <!-- <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=> Yii::t('app','Please select receipt'),
            'maxlength' => true, 'onchange' => 'myfunctionforType(this.value)']) ?> -->

            <?= $form->field($model, 'receipt_type')->widget(kartik\select2\Select2::classname(), [
                'data' => $receipt,
                'options' => ['placeholder' => Yii::t('app','Please select receipt'), 
                    'id' => 'receipt_type', 
                    'onchange' => 'myfunctionforType(this.value)',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumResultsForSearch' => 'Infinity',
                ],
            ]); ?>
            <?php } ?>
        </div>

        <div class="col-sm-6" id="bill_div" <?php if(empty($model_bill)) echo 'style="display:none;"'; ?>>
            <?php  if(!empty($model_bill)){ ?>
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['maxlength' => true, 'value' => $model_bill->bill_print_id, 'readonly' =>true]) ?>
            <?php }else{ ?>
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['maxlength' => true]) ?>
            <?php } ?>
        </div>

        <div class="col-sm-6">
            <!-- <?php  if(!empty($model_bill)){
                    if((new Bill()) -> calculateFinalFee($model_bill->bill_uid) >= 0){
        ?>
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true,  'value' => (new Bill()) -> calculateFinalFee($model_bill->bill_uid)]) ?>
            <?php }else{ ?>
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true,  'value' => (new Bill()) -> getUnclaimed($model_bill->bill_uid)]) ?>

            <?php }
            }else{ ?>
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true]) ?>
            <?php } ?> -->
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_description')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_payment_method')->dropDownList($method, ['prompt'=>'Please select payment method','maxlength' => true])/*['class'=>'payment',
             'prompt'=> Yii::t('app','Please select payment method'),'maxlength' => true, 'onchange' => 'myfunctionforValuecheck(this.value)']) ?> -->

            <?= $form->field($model, 'receipt_content_payment_method')->widget(kartik\select2\Select2::classname(), [
                'data' => $rows_payment,
                'options' => ['placeholder' => Yii::t('app','Please select payment method'), 
                    'id' => 'receipt_content_payment_method', 
                    'class'=>'payment',
                    'onchange' => 'myfunctionforValuecheck(this.value)',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumResultsForSearch' => 'Infinity',
                ],
            ]);*/ ?>
        </div>

        <div class="col-sm-6">
            <!-- <?= $form->field($model, 'receipt_content_payer_name')->dropDownList($names, 
                        ['prompt'=> Yii::t('app','Please select payer name'),'maxlength' => true]) ?> -->
            <?= $form->field($model, 'receipt_content_payer_name')->radioList($names, ['value' => $checked_name, 'custom' => true, 'inline' => true]); ?>
        </div>

        <div class="col-sm-6" id="card_div" style="display:none;">
            <?= $form->field($model, 'card_no')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6" id="cheque_div" style="display:none;">
            <?= $form->field($model, 'cheque_number')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_serial_number', 
                ['labelOptions' => [ 'id' => 'receipt_label' ]])->textInput(['maxlength' => true, 
                    'readonly' => true, 'id' => 'serial_number']) ?>
        </div>
    </div>

    <div class="form-group" id="div_print">
        <?= Html::submitButton(Yii::t('app', 'Print'), ['class' => 'btn btn-success', 'id' => 'print']) ?>
        <?= Html::button(Yii::t('app', 'Reset'), ['class' => 'btn btn-primary', 
            'onclick' => '(function ( $event ) {
                 document.getElementById("serial_number").readOnly = false; 
                 document.getElementById("serial_number").value = "";
                 document.getElementById("serial_number").focus();
            })();' ]) ?>
        <?= Html::button(Yii::t('app', 'Refresh'), ['class' => 'btn btn-secondary', 'id' => 'refresh', 'onclick' => "refreshButton('{$url}')"]) ?>
    </div>

    
    <div class="form-group" id="div_no_print">
        <?= Html::button(Yii::t('app', 'Reset'), ['class' => 'btn btn-primary', 
            'onclick' => '(function ( $event ) {
                 document.getElementById("serial_number").readOnly = false; 
                 document.getElementById("serial_number").value = "";
                 document.getElementById("serial_number").focus();
            })();' ]) ?>
        <?= Html::button(Yii::t('app', 'Refresh'), ['class' => 'btn btn-secondary', 'id' => 'refresh', 'onclick' => "refreshButton('{$url}')"]) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>


</div>

<script>
function myfunctionforValuecheck(val) {
    if (val == "cash" || val == "") {
        document.getElementById("cheque_div").style.display = "none";
        document.getElementById('card_div').style.display = "none";
    } else if (val == "card") {
        document.getElementById("cheque_div").style.display = "none";
        document.getElementById('card_div').style.display = "block";
    } else if (val == "cheque") {
        document.getElementById("cheque_div").style.display = "block";
        document.getElementById('card_div').style.display = "none";
    }
}

function myfunctionforType(val) {
    if (val == "bill")
        document.getElementById("bill_div").style.display = "block";
    else
        document.getElementById("bill_div").style.display = "none";

    if (val == "refund" || val == "exception")
    {
        document.getElementById("receipt_label").innerHTML =  '<?php echo Yii::t('app','Document Number');?>'; 
        document.getElementById("serial_number").value = '';
        document.getElementById("serial_number").readOnly = false; 
    }
    else{
        document.getElementById("receipt_label").innerHTML = '<?php echo Yii::t('app','Receipt Serial Number')?>'; 
        refreshButton('<?php echo $url?>');
    }  

    if (val == "exception"){
        document.getElementById("div_print").style.display = "none";
        document.getElementById("div_no_print").style.display = "block";
    }
    else{
        document.getElementById("div_no_print").style.display = "none";
        document.getElementById("div_print").style.display = "block";
    } 


}

function refreshButton(url) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange  = function() {
        if(xhttp.readyState == 4 && xhttp.status == 200){
            document.getElementById("serial_number").value = this.responseText;
            document.getElementById("serial_number").readOnly = true; 
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}

document.getElementById("div_no_print").style.display = "none";
</script>