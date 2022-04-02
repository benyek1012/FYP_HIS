<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_information".
 *
 * @property string $patient_uid
 * @property string $first_reg_date
 * @property string|null $nric
 * @property string|null $nationality
 * @property string|null $name
 * @property string|null $sex
 * @property string|null $phone_number
 * @property string|null $email
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $address3
 * @property string|null $job
 *
 * @property PatientAdmission[] $patientAdmissions
 * @property PatientNextOfKin[] $patientNextOfKins
 */
class Patient_information extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'patient_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_uid', 'first_reg_date'], 'required'],
            [['first_reg_date'], 'safe'],
            [['patient_uid'], 'string', 'max' => 64],
            [['nric', 'nationality', 'sex', 'job'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 200],
            [['phone_number', 'email', 'address1', 'address2', 'address3'], 'string', 'max' => 100],
            [['nric'], 'unique'],
            [['patient_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'patient_uid' => 'Patient Uid',
            'first_reg_date' => 'First Reg Date',
            'nric' => 'Nric',
            'nationality' => 'Nationality',
            'name' => 'Name',
            'sex' => 'Sex',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'address1' => 'Address 1',
            'address2' => 'Address 2',
            'address3' => 'Address 3',
            'job' => 'Job',
        ];
    }

    /**
     * Gets query for [[PatientAdmissions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatientAdmissions()
    {
        return $this->hasMany(Patient_Admission::className(), ['patient_uid' => 'patient_uid']);
    }

    /**
     * Gets query for [[PatientNextOfKins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatientNextOfKins()
    {
        return $this->hasMany(Patient_Next_Of_Kin::className(), ['patient_uid' => 'patient_uid']);
    }
}
