<?php

use yii\helpers\Html;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Patient_information;
use app\models\Patient_admission;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-form">

    <?php  
        $receipt = array(
            'deposit'=>'Deposit',
            'bill'=>'Bill',
            'refund'=>'Refund',
        );

        $payment_method = array(
            'cash'=>'Cash',
            'card'=>'Debit/Credit Card',
            'cheque'=>'Cheque Number',
        );

        $temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);

        $names = Patient_information::find()
        ->joinWith('patientNextOfKins')
        ->select('nok_name, `patient_information`.`name`')
        ->where(['`patient_information`.`patient_uid`'=> $temp->patient_uid])
        ->asArray()
        ->all();
        
        // $names = Patient_information::find()->where(['`patient_information`.`patient_uid`'=> $temp->patient_uid])
        //         ->joinWith('patientNextOfKins')
        //         ->select('nok_name, name')
        //         ->asArray()->all(); 

        
//         $result = mysqli_query($conn, "SELECT receipt_uid, pa.rn, receipt_type, receipt_content_sum, receipt_content_bill_id, receipt_content_description, receipt_content_date_paid, receipt_content_payer_name, receipt_content_payment_method, card_no, cheque_number, receipt_responsible, receipt_serial_number, receipt_time,
//  name, nric, guarantor_name FROM receipt r1 INNER JOIN patient_admission pa on r1.rn=pa.rn INNER JOIN patient_information pii on pa.patient_uid=pii.patient_uid ");

        // echo "<pre>";
        // var_dump($names);
        // echo "</pre>";
        // exit();
        
        // $rows = (new \yii\db\Query())
        // ->select(['*'])
        // ->from('patient_information, patient_next_of_kin')
        // ->where(['patient_information.patient_uid' => $temp->patient_uid])
        // ->andWhere('patient_next_of_kin.patient_uid = patient_information.patient_uid')
        // ->all();

        // foreach ($rows as $row) {
        //     echo "1";
        // }
        // exit();
        
        

        $namesList = ArrayHelper::map($names, 'patient_uid', 'name'); 
                
        // foreach($namesList as $n){
        //     if($n == '')
        //         $namesList['name'] = "User";
        // }
    
        $form = kartik\form\ActiveForm::begin([
        'id' => 'patient-information-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback']],
    ]);      ?>

    <?= $form->field($model, 'receipt_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Base64UID::generate(32)])->label(false); ?>

    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false); ?>

    <?= $form->field($model, 'receipt_content_date_paid')->hiddenInput()->label(false) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_type')->dropDownList($receipt, ['prompt'=>'Please select receipt',
            'maxlength' => true, 'onchange' => 'myfunctionforType(this.value)']) ?>
        </div>

        <div class="col-sm-6" id="bill_div" style="display:none;">
            <?= $form->field($model, 'receipt_content_bill_id')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_sum')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_description')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_payment_method')->dropDownList($payment_method, ['class'=>'payment',
             'prompt'=>'Please select receipt','maxlength' => true, 'onchange' => 'myfunctionforValuecheck(this.value)']) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_content_payer_name')->dropDownList(array_filter($namesList), ['prompt'=>'Please select payer name','maxlength' => true]) ?>
        </div>

        <div class="col-sm-6" id="card_div" style="display:none;">
            <?= $form->field($model, 'card_no')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6" id="cheque_div" style="display:none;">
            <?= $form->field($model, 'cheque_number')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'receipt_responsible')->textInput(['maxlength' => true]) ?>
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