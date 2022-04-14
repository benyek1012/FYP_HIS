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
 * @property int|null $medigal_legal_code
 * @property int $reminder_given
 * @property string|null $guarantor_name
 * @property string|null $guarantor_nric
 * @property string|null $guarantor_phone_number
 * @property string|null $guarantor_email
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
    public function rules()
    {
        return [
            [['rn', 'entry_datetime', 'patient_uid', 'initial_ward_code', 'initial_ward_class', 'reminder_given'], 'required'],
            [['entry_datetime'], 'safe'],
            [['medigal_legal_code', 'reminder_given'], 'integer'],
            [['rn'], 'string', 'max' => 11],
            [['patient_uid'], 'string', 'max' => 64],
            [['initial_ward_code', 'initial_ward_class'], 'string', 'max' => 20],
            [['reference', 'guarantor_name'], 'string', 'max' => 200],
            [['guarantor_phone_number'], 'integer'],
            [['guarantor_nric'], 'integer'],
            [['guarantor_email'], 'email'],
            [['guarantor_email'], 'string', 'max' => 100],
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
            'rn' => 'Rn',
            'entry_datetime' => 'Entry Datetime',
            'patient_uid' => 'Patient Uid',
            'initial_ward_code' => 'Initial Ward Code',
            'initial_ward_class' => 'Initial Ward Class',
            'reference' => 'Reference',
            'medigal_legal_code' => 'Medigal Legal Code',
            'reminder_given' => 'Reminder Given',
            'guarantor_name' => 'Guarantor Name',
            'guarantor_nric' => 'Guarantor Nric',
            'guarantor_phone_number' => 'Guarantor Phone Number',
            'guarantor_email' => 'Guarantor Email',
            'type' => 'Type'
        ];
    }

    /**
     * Gets query for [[Bills]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(Bill::className(), ['rn' => 'rn']);
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
}
