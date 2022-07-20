<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bill".
 *
 * @property string $bill_uid
 * @property string $rn
 * @property string $status_code
 * @property string $status_description
 * @property string $class
 * @property float $daily_ward_cost
 * @property string|null $department_code
 * @property string|null $department_name
 * @property int $is_free
 * @property string|null $collection_center_code
 * @property string|null $nurse_responsible
 * @property string|null $bill_generation_datetime
 * @property string|null $generation_responsible_uid
 * @property float|null $bill_generation_billable_sum_rm
 * @property float|null $bill_generation_final_fee_rm
 * @property string|null $description
 * @property string|null $bill_print_responsible_uid
 * @property string|null $bill_print_datetime
 * @property string|null $bill_print_id
 * @property int $deleted
 *
 * @property PatientAdmission $rn0
 * @property TreatmentDetails[] $treatmentDetails
 * @property Ward[] $wards
 */
class Bill extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_uid', 'rn', 'status_code', 'status_description', 'class', 'daily_ward_cost'], 'required'],
            [['daily_ward_cost', 'bill_generation_billable_sum_rm', 'bill_generation_final_fee_rm'], 'number'],
            [['is_free', 'deleted'], 'integer'],
            [['bill_generation_datetime', 'bill_print_datetime'], 'safe'],
            [['bill_uid', 'generation_responsible_uid', 'bill_print_responsible_uid'], 'string', 'max' => 64],
            [['rn'], 'string', 'max' => 11],
            [['status_code', 'class', 'department_code', 'collection_center_code', 'nurse_responsible'], 'string', 'max' => 20],
            [['status_description'], 'string', 'max' => 100],
            [['department_name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 200],
           // [['bill_print_id'], 'integer'],
         //   [['bill_print_id'], 'match', 'pattern' => '/^\d{7}$/', 'message' => 'Field must contain exactly 7 digits.'],
            [['bill_print_id'], 'unique'],
            [['rn'], 'exist', 'skipOnError' => true, 'targetClass' => Patient_admission::className(), 'targetAttribute' => ['rn' => 'rn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_uid' => Yii::t('app','Bill Uid'),
            'rn' => 'Rn',
            'status_code' => Yii::t('app','Status Code'),
            'status_description' => Yii::t('app','Status Description'),
            'class' => Yii::t('app','Ward Class'),
            'daily_ward_cost' => Yii::t('app','Daily Ward Cost'),
            'department_code' => Yii::t('app','Department Code'),
            'department_name' => Yii::t('app','Department Name'),
            'is_free' => Yii::t('app','Is Free'),
            'collection_center_code' => Yii::t('app','Collection Center Code'),
            'nurse_responsible' => Yii::t('app','Nurse Responsible'),
            'bill_generation_datetime' => Yii::t('app','Bill Generation Datetime'),
            'generation_responsible_uid' => Yii::t('app','Generation Responsible Uid'),
            'bill_generation_billable_sum_rm' => Yii::t('app','Billable Total')." (RM)", //Bill Generation Billable Sum Rm
            'bill_generation_final_fee_rm' => Yii::t('app','Final Fee (RM)'), //Bill Generation Final Fee Rm
            'description' => Yii::t('app','Bill Description'),
            'bill_print_responsible_uid' => ('Bill Print Responsible Uid'),
            'bill_print_datetime' => Yii::t('app','Bill Print Datetime'),
            'bill_print_id' => Yii::t('app','Bill Print ID'),
            'deleted' => 'Deleted',
        ];
    }

    /**
     * Gets query for [[Rn0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRn0()
    {
        return $this->hasOne(Patient_admission::className(), ['rn' => 'rn']);
    }

    /**
     * Gets query for [[TreatmentDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTreatmentDetails()
    {
        return $this->hasMany(Treatment_details::className(), ['bill_uid' => 'bill_uid']);
    }

    /**
     * Gets query for [[Wards]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWards()
    {
        return $this->hasMany(Ward::className(), ['bill_uid' => 'bill_uid']);
    }

    // Get Ward Total Days Cost
    public function getTotalWardCost($bill_uid) {
        $totalWardDays = 0;
        $dailyWardCost = 0.0;
        $totalWardCost = 0.0;

        $modelBill = Bill::findOne(['bill_uid' => $bill_uid]);
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);

        if($modelBill != ""){
            $dailyWardCost = $modelBill->daily_ward_cost;
        }
        
        foreach ($modelWard as $index => $modelWard){            
            $totalWardDays += $modelWard->ward_number_of_days;
        }

        $totalWardCost = $dailyWardCost * $totalWardDays;

        // return Yii::$app->formatter->asCurrency($totalWardCost);                  
        return number_format((float)$totalWardCost, 2, '.', '');                
    }

    // Get Treatment Total Item Cost
    public function getTotalTreatmentCost($bill_uid) {
        $totalItemCost = 0.0;

        $modelBill = Bill::findOne(['bill_uid' => $bill_uid]);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        
        foreach ($modelTreatment as $index => $modelTreatment){            
            $totalItemCost += $modelTreatment->item_total_unit_cost_rm;
        }

        // return Yii::$app->formatter->asCurrency($totalItemCost);     
        return number_format((float)$totalItemCost, 2, '.', '');            
    }

    // Calculate Billable
    public function calculateBillable($bill_uid) {
        $totalWardDays = 0;
        $dailyWardCost = 0.0;
        $totalTreatmentCost = 0.0;
        $billable = 0.0;

        $modelBill = Bill::findOne(['bill_uid' => $bill_uid]);
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);

        if($modelBill != ""){
            $dailyWardCost = $modelBill->daily_ward_cost;
        }
        
        foreach ($modelWard as $index => $modelWard){            
            $totalWardDays += $modelWard->ward_number_of_days;
        }

        foreach($modelTreatment as $index => $modelTreatment){
            $totalTreatmentCost += $modelTreatment->item_total_unit_cost_rm;
        }
        
        $billable = ($totalWardDays * $dailyWardCost) + $totalTreatmentCost;

        if(!empty($modelBill) && $modelBill->is_free == 1)
            $billable = 0;

        $billable = number_format((float) $billable, 2, '.', '');
        return $billable;
    }

     // Get Unclaimed balance 
     public function getUnclaimed($rn) {
        $model_bill = Bill::findOne(['rn' => $rn, 'deleted' => 0]);
        if(!empty($model_bill)  && Bill::isGenerated($rn))
            return (0 - Bill::calculateFinalFee($model_bill->bill_uid)) < 0 ? 0 : (0 - Bill::calculateFinalFee($model_bill->bill_uid));
        else return (Bill::getDeposit($rn) + Bill::getRefund($rn) + Bill::getPayedAmt($rn)) < 0 ? 0 : (Bill::getDeposit($rn) + Bill::getRefund($rn) + Bill::getPayedAmt($rn)) ;
    }

    // Get Amt Due
    public function getAmtDued($rn) {
        $model_bill = Bill::findOne(['rn' => $rn, 'deleted' => 0]);
        if(!empty($model_bill) && Bill::isGenerated($rn))
            return Bill::calculateFinalFee($model_bill->bill_uid) < 0 ? 0 : Bill::calculateFinalFee($model_bill->bill_uid);
        else return (0 - (Bill::getDeposit($rn) + Bill::getRefund($rn)+ Bill::getPayedAmt($rn)))  < 0 ? 0 : (0 -(Bill::getDeposit($rn) + Bill::getRefund($rn)+ Bill::getPayedAmt($rn))) ;
    }

    // Return Negative values
    public function calculateFinalFee($bill_uid) {
        $billable = 0.0;
        $modelBill = Bill::findOne(['bill_uid' => $bill_uid]);
        if(!empty($modelBill))
        {
            // Billable_sum - sum of deposit - sum of payed - sum of refund
            $billable = Bill::calculateBillable($bill_uid) - Bill::getDeposit($modelBill->rn)
             - Bill::getPayedAmt($modelBill->rn) - Bill::getRefund($modelBill->rn);
        }
        $billable = number_format((float) $billable, 2, '.', '');
        return $billable;
    }

    // All Deposit
    public function getDeposit($rn){
        $sum_deposit = 0.0;
        $model_receipt = Receipt::findAll(['rn' => $rn]);
        foreach($model_receipt as $model)
        {
            if($model->receipt_type == 'deposit')
                $sum_deposit += $model->receipt_content_sum;
        }
        return $sum_deposit  < 0 ?  0.0 : $sum_deposit;
    }

    // Deposit - Refund
    public function getSumDeposit($rn)
    {
        $sum_deposit = 0.0;
      //  $sum_deposit = Bill::getDeposit($rn) + Bill::getRefund($rn);
        $sum_deposit = Bill::getDeposit($rn);
        return $sum_deposit < 0 ?  0.0 : $sum_deposit;
    }

    // Get all Payed Amount
    public function getPayedAmt($rn){
        $payed_amt = 0.0;

        $info_receipt = Receipt::findAll(['rn' => $rn, 'receipt_type' => 'bill']);
        if(!empty($info_receipt))
        {
            foreach($info_receipt as $r)
            {
                $payed_amt += $r->receipt_content_sum;
            }
        }
        return $payed_amt;
    }

    // Get all Refund Amount (Negative) 
    public function getRefund($rn){
        $sum_refund = 0.0;
        $model_receipt = Receipt::findAll(['rn' => $rn]);
        foreach($model_receipt as $model)
        {
            if($model->receipt_type == 'refund')
                $sum_refund += $model->receipt_content_sum;
        }
        // var_dump($sum_refund);
        // exit();

        return 0 - $sum_refund;
    }

    // Check whether bill is generated
    public function isGenerated($rn){
        $row_bill = Bill::findOne(['rn' => $rn, 'deleted' => 0]);
        if(!empty($row_bill))
            return !empty($row_bill['bill_generation_datetime']) ? $row_bill['bill_generation_datetime'] : false;
    }

    // Check whether bill is free
    public function isFree($rn){
        $row_bill = Bill::findOne(['rn' => $rn, 'deleted' => 0]);
        if(!empty($row_bill))
            return !empty($row_bill['is_free']) ? $row_bill['is_free'] : false;
    }

    // Check whether bill is printed
    public function isPrinted($rn){
        $row_bill = Bill::findOne(['rn' => $rn, 'deleted' => 0]);
        if(!empty($row_bill))
            return !empty($row_bill['bill_print_id']) ? $row_bill['bill_print_id'] : false;
    }

    // Call Procedure of receipt and bill 
    public function getProcedureBillReceipt($rn){
        $result = \Yii::$app->db->createCommand("CALL receipt_bill_procedure(:rn)") 
        ->bindValue(':rn' , $rn )
        ->queryAll();
        
        if(!empty($result))
            return $result;
    }

    // Call Procedure of receipt and bill 
    public function getProcedureTransactions($pid){
        $result = \Yii::$app->db->createCommand("CALL transaction_records(:pid)") 
        ->bindValue(':pid' , $pid )
        ->queryAll();
        
        if(!empty($result))
            return $result;
    }

    // Check whether bill is generated
    public function getFinalFee($rn){
        $row_bill = Bill::findOne(['rn' => $rn, 'deleted' => 0]);
        if(!empty($row_bill))
            return !empty($row_bill['bill_generation_final_fee_rm']) ? $row_bill['bill_generation_final_fee_rm'] : number_format((float) 0, 2, '.', '');
    }
    
}