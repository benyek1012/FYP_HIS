<?php

namespace app\models;

use DateTime;
use Yii;
use yii\helpers\ArrayHelper;
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
 * @property string|null $DOB
 * @property string|null $age
 *
 * @property PatientAdmission[] $patientAdmissions
 * @property PatientNextOfKin[] $patientNextOfKins
 */
class Patient_information extends \yii\db\ActiveRecord
{
    public $age;
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
            [['nric'], 'unique'],
            [['name'], 'string', 'max' => 200],
            ['name', 'match', 'pattern' => '/^[a-z\s\-\/\.\'@]+$/i', 'message' => 'Name can only contain word characters'],
            // ['address1', 'match', 'pattern' => '/^[a-z,.\s]+$/i', 'message' => 'Address cannot contain special symbol, only can contain "." and ","'],
            // ['address2', 'match', 'pattern' => '/^[a-z,.\s]+$/i', 'message' => 'Address cannot contain special symbol, only can contain "." and ","'],
            // ['address3', 'match', 'pattern' => '/^[a-z,.\s]+$/i', 'message' => 'Address cannot contain special symbol, only can contain "." and ","'],
            // [['first_reg_date', 'DOB'], 'safe'],
            [['first_reg_date'], 'safe'],
            [['DOB'], 'date', 'format' => 'php:Y-m-d'],
          //  [['nric'], 'integer'],
            // [['phone_number'], 'integer'],
            [['phone_number'], 'string', 'max' => 100],
            ['phone_number', 'match', 'pattern' => '/^[0-9\/\-\,\s]+$/i', 'message' => Yii::t('app', 'Phone Number can only contain digit and "/", "-", ",", and " " character')],
            [['email'], 'email'],
            // [['nric', 'nationality', 'sex', 'job', 'race'], 'string', 'max' => 20],
            [['nric', 'job'], 'string', 'max' => 20],
            [['nationality', 'sex', 'race'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['phone_number', 'email', 'address1', 'address2', 'address3'], 'string', 'max' => 100],
            [['patient_uid'], 'unique'],
            ['race', 'match', 'pattern' => '/^[a-z\s]+$/i', 'message' => 'Race can only contain word characters'],
            ['nationality', 'match', 'pattern' => '/^[a-z\s]+$/i', 'message' => 'Nationality can only contain word characters'],
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
            'nric' => Yii::t('app','NRIC/Passport'),
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
            'DOB' => 'DOB',
        ];
    }

    // validate IC xxxxxx-xx-xxxx
 	public function hasValidIC()
	{
		if(is_null($this->nric))
			return false;
		return preg_match("/^\d{6}-\d{2}-\d{4}$/", $this->nric);
	}

    // validate IC date is existed in calender
    public function Date_validate($input_date, $format = 'Y/m/d')
    {
        $date_obj = DateTime::createFromFormat($format, $input_date);
        return $date_obj && $date_obj->format($format) == $input_date;
    }

    // return DOB yy/mm/dd
	public function getDOB()
	{
		if($this->hasValidIC())
		{
			$targetString = (explode('-', $this->nric))[0];
			$targetString = substr_replace ( $targetString , "/" , 4 , 0 );
			$targetString = substr_replace ( $targetString , "/" , 2 , 0 );
			return $targetString;
		}
		else
			return "N/A";
	}

    // get date for database yyyy-mm-dd
    public function getDateForDatabase()
    {
        if($this->hasValidIC())
		{
            if($this->getStartDate() && $this->Date_validate($this->getStartDate()))
            {
                $timestamp = strtotime($this->getStartDate());
                $date_formated = date('Y-m-d', $timestamp);
                return $date_formated;
            }
            else return null;
        }
        else return null;
    }

    // get date yyyy/mm/dd (validate is 0, 5, 6, 7)
    public function getStartDate()
	{
		if($this->hasValidIC())
		{
			$targetString = $this->getDOB();
          
            if($targetString != "N/A")
            {
                $centuryIdentifierInt = (int)(explode('-', $this->nric))[2][0];
                {
                    //at time of writing, wiki says for final 4 digits, 1st digit will be 0 if 2000+, 5-7 if 1900+
                    if($centuryIdentifierInt == 0)
                        return $targetString = "20".$targetString;
                    else if($centuryIdentifierInt >=5 && $centuryIdentifierInt<=7)
                        return $targetString = "19".$targetString;
                    else
                        return false;
                }
            }
		}
        else return false;
	}
	
    // return age from IC 
	public function getAge($format)
	{
		if($this->hasValidIC())
		{
            if($this->getStartDate() && $this->Date_validate($this->getStartDate()))
            {
                $startDate = new \DateTime($this->getStartDate());
                $endDate = new \DateTime();
                $difference = $endDate->diff($startDate);
                return $difference->format($format);
            }
            else return "N/A";
		}
		else
			return "N/A";
	}

      // caluculate age from paramter DOB
      public function calculateDob($dob)
      {
            $startDate = new \DateTime($dob);
            $endDate = new \DateTime();
            $difference = $endDate->diff($startDate);
            return $difference->format("%yyrs%mmth%dday");
      }

    // get age from datapicker yyyy-mm-dd
    public function getAgeFromDatePicker()
    {
        $model = Patient_information::findOne(Yii::$app->request->get('id'));

        if(is_null($model->DOB))
            return "N/A";
        else
            return $this->calculateDob($model->DOB);
    }
	
	public function getLatestNOK()
	{
		return Patient_next_of_kin::find()->where(['patient_uid' => $this->patient_uid])->orderBy('nok_datetime_updated ASC')->one();
	}


    /**
     * Gets query for [[PatientAdmissions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatientAdmissions()
    {
        return $this->hasMany(Patient_admission::className(), ['patient_uid' => 'patient_uid']);
    }

    /**
     * Gets query for [[PatientNextOfKins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatientNextOfKins()
    {
        return $this->hasMany(Patient_next_of_kin::class, ['patient_uid' => 'patient_uid']);
    }

    public function getBalance($patient_uid)
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
            $billable_sum += (new Bill())  -> getAmtDued($rn);
        }

        if($billable_sum < 0)
        {
            $billable_sum = 0.0;           
        }

        return Yii::t('app','Amount Due')." : ". Yii::$app->formatter->asCurrency($billable_sum);                
    }

    public function getBalanceRM($patient_uid)
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
            $billable_sum += (new Bill())  -> getAmtDued($rn);
        }

        if($billable_sum < 0)
        {
            $billable_sum = 0.0;           
        }

        return  Yii::$app->formatter->asCurrency($billable_sum);                
    }

    public function getUnclaimedBalance($patient_uid)
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
            $unclaimed_sum += (new Bill())  -> getUnclaimed($rn);
        }

        return Yii::t('app','Unclaimed Balance')." : ". Yii::$app->formatter->asCurrency($unclaimed_sum);                
    }
    public function getUnclaimedBalanceRM($patient_uid)
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
            $unclaimed_sum += (new Bill())  -> getUnclaimed($rn);
        }

        return  Yii::$app->formatter->asCurrency($unclaimed_sum);                
    }

    public function getPatient_admission() 
    {
        return $this->hasMany(Patient_admission::className(), ['patient_uid' => 'patient_uid']);
    }

    public function RemoveUnvalidPatients()
    {
     //$noIcPatients = Patient_information::find()->innerJoin('patient_admission')->where(['nric'=> NULL],['rn' => NULL ])->all();
     $emptyIcPatient = Patient_information::find()->select('patient_uid')->asArray()->where(['nric' => NULL])->all();
     $emptyIcPatientID = ArrayHelper::getColumn($emptyIcPatient, 'patient_uid');

     $RemovingUnvalidPatients = Patient_admission::deleteAll(['patient_uid' => $emptyIcPatientID],['rn'=>NULL]);
      
    
    }
}
