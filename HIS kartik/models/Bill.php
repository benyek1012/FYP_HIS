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
 * @property string|null $nurse_responsilbe
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
            [['status_code', 'class', 'department_code', 'collection_center_code', 'nurse_responsilbe', 'bill_print_id'], 'string', 'max' => 20],
            [['status_description'], 'string', 'max' => 100],
            [['department_name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 200],
            // [['bill_print_id'], 'unique'], //temporary comment
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
            'bill_uid' => 'Bill Uid',
            'rn' => 'Rn',
            'status_code' => 'Status Code',
            'status_description' => 'Status Description',
            'class' => 'Ward Class',
            'daily_ward_cost' => 'Daily Ward Cost',
            'department_code' => 'Department Code',
            'department_name' => 'Department Name',
            'is_free' => 'Is Free',
            'collection_center_code' => 'Collection Center Code',
            'nurse_responsilbe' => 'Nurse Responsilbe',
            'bill_generation_datetime' => 'Bill Generation Datetime',
            'generation_responsible_uid' => 'Generation Responsible Uid',
            'bill_generation_billable_sum_rm' => 'Billable Total (RM)', //Bill Generation Billable Sum Rm
            'bill_generation_final_fee_rm' => 'Final Fee (Rm)', //Bill Generation Final Fee Rm
            'description' => 'Bill Description',
            'bill_print_responsible_uid' => 'Bill Print Responsible Uid',
            'bill_print_datetime' => 'Bill Print Datetime',
            'bill_print_id' => 'Bill Print ID',
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
}
