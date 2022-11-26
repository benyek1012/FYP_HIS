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
->orderBy('length(status_code) ASC, status_code ASC')
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
->orderBy('length(treatment_code) ASC, treatment_code ASC')
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


<a name="inpatient-treatment">
    <?php $form = kartik\form\ActiveForm::begin([
        'id' => 'inpatient-treatment-form',
        'type' => 'vertical',
        // 'action' =>  Url::toRoute(['/bill/generate', 'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' =>Yii::$app->request->get('rn'), '#' => 'treatment']),
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]);
    ?>
        <table id="inpatient-treatment-table">
            <tr>
                <td>
                    <div id="inpatient_treatment-inpatient_treatment_cost_rm">
                        <?php 
                            echo Yii::t('app','Inpatient Treatment Cost (RM)')." : 0.00"; 
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <!-- <td>
                    <?= $form->field($modelInpatient, "inpatient_treatment_cost_rm")->textInput(['autocomplete' =>'off', 'tabindex' => '-1',  'disabled' => true,])->label(false) ?>
                </td> -->
            </tr>
        </table>
        <br>
    <?php kartik\form\ActiveForm::end(); ?>
</a>