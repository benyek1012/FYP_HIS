<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_admission".
 *
 * @property string $rn
 * @property string $entry_datetime
 * @property string $patient_uid
 * @property string $initial_ward_code
 * @property string $initial_ward_class
 * @property string|null $reference
 * @property int|null $medical_legal_code
 * @property string $type
 * @property string|null $reminder1
 * @property string|null $reminder2
 * @property string|null $reminder3
 *
 * @property Bill[] $bills
 * @property PatientInformation $patientU
 * @property Receipt[] $receipts
 */
class Patient_admission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'patient_admission';
    }

    /**
     * {@inheritdoc}
     */
    public  $startrn, $endrn;
    public function rules()
    {
        return [
            [['rn', 'entry_datetime', 'patient_uid', 'type'], 'required'],
            // [['entry_datetime'], 'safe'],
            [['entry_datetime'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['medical_legal_code'], 'integer'],
            [['rn','startrn','endrn'], 'string', 'max' => 11, 'min' => 11],
            [['patient_uid'], 'string', 'max' => 64],
            [['initial_ward_code', 'initial_ward_class'], 'string', 'max' => 20],
            [['reference'], 'string', 'max' => 200],
            [['type'], 'string', 'max' => 20],
            [['rn'], 'unique'],
            [['patient_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Patient_information::className(), 'targetAttribute' => ['patient_uid' => 'patient_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rn' =>  Yii::t('app','Registration Number (R/N)'),
            'entry_datetime' => Yii::t('app','Entry Datetime'),
            'patient_uid' => Yii::t('app','Patient Uid'),
            'initial_ward_code' => Yii::t('app','Initial Ward Code'),
            'initial_ward_class' => Yii::t('app','Initial Ward Class'),
            'reference' => Yii::t('app','Reference'),
            'medical_legal_code' => Yii::t('app','Medical Legal Code'),
            'type' => Yii::t('app','Type'),
            'nric' => 'NRIC',
            'name' => Yii::t('app','Name'),
            'sex' => Yii::t('app','Sex'),
            'race' => Yii::t('app','Race'),
        ];
    }


    public function get_bill($rn){
        if( (new Bill())  -> getUnclaimed($rn) < 0 )
            return Yii::t('app','-')." ".Yii::$app->formatter->asCurrency((new Bill())  -> getAmtDued($rn));
        else return Yii::t('app','+')." ".Yii::$app->formatter->asCurrency((new Bill())  -> getUnclaimed($rn));

        return null;
    }

    public function get_billable_sum($rn){
        $billable = 0;
        $model_bill = Bill::findOne(['rn' => $rn, 'deleted' => 0]);
        if(!empty($model_bill) && (new Bill())  -> isGenerated($rn))
        {
            $billable = $model_bill->bill_generation_billable_sum_rm;
            // If billable sum in database in not set, assign to 0
            if(empty($billable)) $billable = 0;
        }
     
        return Yii::$app->formatter->asCurrency($billable);
    }

     /**
     * Gets query for [[PatientU]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(Bill::className(), ['rn' => 'rn'])->where(['deleted' => 0]);
    }

    /**
     * Gets query for [[PatientU]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatientU()
    {
        return $this->hasOne(Patient_information::className(), ['patient_uid' => 'patient_uid']);
    }

    /**
     * Gets query for [[Receipts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceipts()
    {
        return $this->hasMany(Receipt::className(), ['rn' => 'rn']);
    }
    public function getPatient_information() 
    {
        return $this->hasMany(Patient_information::className(), ['patient_uid' => 'patient_uid']);
    }

    public function getBills() 
    {
        return $this->hasMany(Bill::className(), ['rn' => 'rn'])->where(['deleted' => 0]);
    }

    public function getReceipt() 
    {
        return $this->hasMany(Receipt::className(), ['rn' => 'rn']);
    }

    public function getReminder() 
    {
        return $this->hasMany(Reminder::className(), ['batch_date' => 'reminder1']);
    }

    // Check whether deposit is generated
    public function isdeposited($rn){
        $row_receipt = Receipt::findOne(['rn' => $rn, 'receipt_type' => "deposit"]);
        if(!empty($row_receipt))
            return !empty($row_receipt['receipt_serial_number']) ? $row_receipt['receipt_serial_number'] : false;
    }

    public function getFinalwardDate()
    {
        return $this->hasOne(Bill::className(), ['final_ward_datetime' => 'final_ward_datetime']);
    }

    public function checkRnFormat($checkRN){
        $arr = str_split($checkRN, 4);

        $SID = (string)$checkRN;

        //2022/000001, 2022/900001
        $SID = substr($SID,5,11); 
        if($arr[0] ==  date('Y')){
            $SID = str_pad($SID, 6, "0", STR_PAD_LEFT);
        }

        $rn = date('Y')."/".$SID;

        if(preg_match("/^[0-9]{4}\/[0-9]{6}$/", $rn)) {
            return true;
        }
        else{
            return false;
        }
    }
}
