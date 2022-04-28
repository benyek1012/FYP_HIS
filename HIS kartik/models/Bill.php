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
 *
 * @property BillContentReceipt[] $billContentReceipts
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
            [['is_free'], 'integer'],
            [['bill_generation_datetime', 'bill_print_datetime'], 'safe'],
            [['bill_uid', 'generation_responsible_uid', 'bill_print_responsible_uid'], 'string', 'max' => 64],
            [['rn'], 'string', 'max' => 11],
            [['status_code', 'class', 'department_code', 'collection_center_code', 'nurse_responsible'], 'string', 'max' => 20],
            [['status_description'], 'string', 'max' => 100],
            [['department_name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 200],
            [['bill_print_id'], 'integer'],
            [['bill_print_id'], 'match', 'pattern' => '/^\d{7}$/', 'message' => 'Field must contain exactly 7 digits.'],
            [['bill_print_id'], 'unique'],
            // [['bill_print_id'], 'number'],
            // [['bill_print_id'], 'string', 'length' => 7],
            // [['bill_print_id'], 'unique'],
            // [['bill_uid'], 'unique'],
            [['rn'], 'exist', 'skipOnError' => true, 'targetClass' => Patient_Admission::className(), 'targetAttribute' => ['rn' => 'rn']],
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
            'bill_generation_billable_sum_rm' => Yii::t('app','Billable Total (RM)'), //Bill Generation Billable Sum Rm
            'bill_generation_final_fee_rm' => Yii::t('app','Final Fee (Rm)'), //Bill Generation Final Fee Rm
            'description' => Yii::t('app','Bill Description'),
            'bill_print_responsible_uid' => ('Bill Print Responsible Uid'),
            'bill_print_datetime' => Yii::t('app','Bill Print Datetime'),
            'bill_print_id' => Yii::t('app','Bill Print ID'),
        ];
    }

    /**
     * Gets query for [[BillContentReceipts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBillContentReceipts()
    {
        return $this->hasMany(Bill_Content_Receipt::className(), ['bill_uid' => 'bill_uid']);
    }

    /**
     * Gets query for [[Rn0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRn0()
    {
        return $this->hasOne(Patient_Admission::className(), ['rn' => 'rn']);
    }

    /**
     * Gets query for [[TreatmentDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTreatmentDetails()
    {
        return $this->hasMany(Treatment_Details::className(), ['bill_uid' => 'bill_uid']);
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

    public static function calculateTreatmentCost($bill_uid) {
        $treatment = Treatment_details::findOne(['bill_uid' => $bill_uid]);
        // $aa = $treatment->load(Yii::$app->request->post());
        // $aa = Yii::$app->request->queryParams;

        // var_dump($aa[]);
        // exit();
    }

    public static function calculateBillable($bill_uid) {
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

        return $billable;
    }

    public static function calculateFinalFee($bill_uid) {
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

        return $billable;
    }
}
