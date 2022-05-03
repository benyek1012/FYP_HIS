<?php

use yii\helpers\Html;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Bill;
use app\models\Patient_admission;
use app\controllers\receiptController;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-form">

    <?php  
    // var_dump($userId);
    // exit();
        $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn')]);
        if(!empty($model_bill))
            // Once bill is generated, can't pay deposit. Only allow bill or refund
            $receipt = array(
                'bill'=>'Bill',
                'refund'=>'Refund',
            );
        else
            $receipt = array(
                'deposit'=>'Deposit',
                'refund'=>'Refund',
            );

        $payment_method = array(
            'cash'=>'Cash',
            'card'=>'Debit/Credit Card',
            'cheque'=>'Cheque Number',
        );

        $temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
        
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
                $row['name'] = "User"; 
            $names[$row['name']] = $row['name'];
            $names[$row['guarantor_name']] = $row['guarantor_name'];
        }
        $names = array_unique($names);
        $names = array_filter($names);
    
        $form = kartik\form\ActiveForm::begin([
        'id' => 'patient-information-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);      ?>

    <?= $form->field($model, 'receipt_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Base64UID::generate(32)])->label(false); ?>

    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false); ?>

    <?= $form->field($model, 'receipt_content_datetime_paid')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'receipt_responsible')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="row">
        <div class="col-sm-6">
        <?php  if(!empty($model_bill)){ ?> 
            <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=>'Please select receipt',
            'maxlength' => true, 'onchange' => 'myfunctionforType(this.value)', 'value' => 'bill']) ?>
         <?php }else{ ?>
            <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=>'Please select receipt',
            'maxlength' => true, 'onchange' => 'myfunctionforType(this.value)']) ?>
        <?php } ?>
        </div>

        <div class="col-sm-6" id="bill_div" 
            <?php if(empty($model_bill)) echo 'style="display:none;"'; ?> >
        <?php  if(!empty($model_bill)){ ?> 
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['maxlength' => true, 'value' => $model_bill->bill_print_id]) ?>
        <?php }else{ ?>
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['maxlength' => true]) ?>
        <?php } ?>
        </div>

        <div class="col-sm-6">
        <?php  if(!empty($model_bill)){ ?> 
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true, 'value' => $model_bill->bill_generation_billable_sum_rm]) ?>
        <?php }else{ ?>
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true]) ?>
        <?php } ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_description')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_payment_method')->dropDownList($payment_method, ['class'=>'payment',
             'prompt'=>'Please select receipt','maxlength' => true, 'onchange' => 'myfunctionforValuecheck(this.value)']) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_payer_name')->dropDownList($names, ['prompt'=>'Please select payer name','maxlength' => true]) ?>
        </div>

        <div class="col-sm-6" id="card_div" style="display:none;">
            <?= $form->field($model, 'card_no')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6" id="cheque_div" style="display:none;">
            <?= $form->field($model, 'cheque_number')->textInput(['maxlength' => true]) ?>
        </div>     

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_serial_number')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>


</div>

<script>
function myfunctionforValuecheck(val) {
    if (val == "cash" || val == ""){
        document.getElementById("cheque_div").style.display = "none";
        document.getElementById('card_div').style.display = "none";
    }
    else if (val == "card"){
        document.getElementById("cheque_div").style.display = "none";
        document.getElementById('card_div').style.display = "block";
    }
    else if (val == "cheque"){
        document.getElementById("cheque_div").style.display = "block";
        document.getElementById('card_div').style.display = "none";
    }
}

function myfunctionforType(val) {
    if (val == "bill")
        document.getElementById("bill_div").style.display = "block";
    else
        document.getElementById("bill_div").style.display = "none";s
}



</script>