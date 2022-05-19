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
            [['first_reg_date'], 'required'],
            [['name'], 'string', 'max' => 200],
            ['name', 'match', 'pattern' => '/^[a-z,.\s-]+$/i', 'message' => 'Name can only contain word characters'],
            [['first_reg_date'], 'safe'],
          //  [['nric'], 'integer'],
            [['phone_number'], 'integer'],
            [['email'], 'email'],
            [['nric', 'nationality', 'sex', 'job', 'race'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 200],
            [['phone_number', 'email', 'address1', 'address2', 'address3'], 'string', 'max' => 100],
            [['patient_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'patient_uid' => Yii::t('app','Patient Uid'),
            'first_reg_date' => Yii::t('app','First Reg Date'),
            'nric' => 'NRIC',
            'nationality' => Yii::t('app','Nationality'),
            'name' => Yii::t('app','Name'),
            'sex' => Yii::t('app','Sex'),
            'race' => Yii::t('app','Race'),
            'phone_number' => Yii::t('app','Phone Number'),
            'email' => Yii::t('app','Email'),
            'address1' => Yii::t('app','Address'),
            'address2' => 'Address 2',
            'address3' => 'Address 3',
            'job' => Yii::t('app','Job'),
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
        return $this->hasMany(Patient_Next_Of_Kin::class, ['patient_uid' => 'patient_uid']);
    }

    public static function getBalance($patient_uid)
    { 
        $info = Patient_admission::findAll(['patient_uid' => $patient_uid]);

        $adm = array();
        foreach($info as $x)
        {
            $adm[] = $x->rn; 
        }

        $billable_sum = 0.0;
        foreach($adm as $rn)
        {
            $billable_sum += Bill::getAmtDued($rn);
        }

        if($billable_sum < 0)
        {
            $billable_sum = 0.0;           
        }

        return Yii::t('app','Amount Due')." : ". Yii::$app->formatter->asCurrency($billable_sum);                
    }

    public static function getUnclaimedBalance($patient_uid)
    { 
        $info = Patient_admission::findAll(['patient_uid' => $patient_uid]);
     
        $adm = array();
        foreach($info as $x)
        {
            $adm[] = $x->rn; 
        }

        $unclaimed_sum = 0.0;
        foreach($adm as $rn)
        {
            $unclaimed_sum += Bill::getUnclaimed($rn);
        }

        return Yii::t('app','Unclaimed Balance')." : ". Yii::$app->formatter->asCurrency($unclaimed_sum);                
    }
}
