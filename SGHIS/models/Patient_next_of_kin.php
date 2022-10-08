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
 *  * @property string|null $nok_datetime_updated
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
            [['nok_uid', 'patient_uid'], 'required'],
            [['nok_uid', 'patient_uid'], 'string', 'max' => 64],
            [['nok_datetime_updated'], 'safe'],
            [['nok_name'], 'string', 'max' => 200],
            ['nok_name', 'match', 'pattern' => '/^[a-z\s]+$/i', 'message' => Yii::t('app','Name can only contain word characters')],
            // ['nok_address1', 'match', 'pattern' => '/^[A-Za-z0-9.-\s,]+$/i', 'message' => 'Address cannot contain special symbol, only can contain "." and ","'],
            // ['nok_address2', 'match', 'pattern' => '/^[A-Za-z0-9.-\s,]+$/i', 'message' => 'Address cannot contain special symbol, only can contain "." and ","'],
            // ['nok_address3', 'match', 'pattern' => '/^[A-Za-z0-9.-\s,]+$/i', 'message' => 'Address cannot contain special symbol, only can contain "." and ","'],
            [['nok_relationship'], 'string', 'max' => 20],
          //  [['nok_phone_number'], 'string', 'length' => [10], 'max' => 10],
            [['nok_phone_number'], 'string'],
            ['nok_phone_number', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => Yii::t('app','Phone Number can only contain digit number')],
            [['nok_email'], 'email'],
            [['nok_email','nok_address1', 'nok_address2', 'nok_address3'], 'string', 'max' => 100],
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
            'patient_uid' => Yii::t('app','Patient Uid'),
            'nok_name' => Yii::t('app','Nok Name'),
            'nok_relationship' => Yii::t('app','Nok Relationship'),
            'nok_phone_number' => Yii::t('app','Nok Phone Number'),
            'nok_email' => Yii::t('app','Nok Email'),
            'nok_address1' => Yii::t('app','Address 1'),
            'nok_address2' => Yii::t('app','Address 2'),
            'nok_address3' => Yii::t('app','Address 3'),
            'nok_datetime_updated' => 'Nok Date Time Updated'
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
