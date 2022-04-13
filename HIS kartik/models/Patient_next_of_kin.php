<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_next_of_kin".
 *
 * @property string $nok_uid
 * @property string $patient_uid
 * @property string|null $nok_name
 * @property string|null $nok_relationship
 * @property string|null $nok_phone_number
 * @property string|null $nok_email
 *
 * @property PatientInformation $patientU
 */
class Patient_next_of_kin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'patient_next_of_kin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nok_uid', 'patient_uid','nok_name','nok_relationship','nok_phone_number','nok_email'], 'required'],
            [['nok_uid', 'patient_uid'], 'string', 'max' => 64],
            [['nok_name'], 'string', 'max' => 200],
            ['nok_name', 'match', 'pattern' => '/^[a-z,.\s-]+$/i', 'message' => 'Name can only contain word characters'],
            [['nok_relationship'], 'string', 'max' => 20],
            [['nok_phone_number'], 'string', 'length' => [10], 'max' => 10],
            [['nok_phone_number'], 'integer'],
            [['nok_email'], 'email'],
            [['nok_email'], 'string', 'max' => 100],
            [['nok_uid'], 'unique'],
           // [['patient_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Patient_information::className(), 'targetAttribute' => ['patient_uid' => 'patient_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nok_uid' => 'Nok Uid',
            'patient_uid' => 'Patient Uid',
            'nok_name' => 'Nok Name',
            'nok_relationship' => 'Nok Relationship',
            'nok_phone_number' => 'Nok Phone Number',
            'nok_email' => 'Nok Email',
        ];
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
}
