<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ward".
 *
 * @property string $ward_uid
 * @property string $bill_uid
 * @property string $ward_code
 * @property string $ward_name
 * @property string $ward_start_datetime
 * @property string $ward_end_datetime
 * @property string $ward_number_of_days
 *
 * @property Bill $billU
 */
class Ward extends \yii\db\ActiveRecord
{
    public $ward_end_date;
    public $ward_end_time;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ward';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ward_code', 'ward_name', 'ward_start_datetime', 'ward_end_datetime', 'ward_number_of_days','ward_end_date','ward_end_time'], 'required'],
            // [['ward_start_datetime', 'ward_end_datetime', 'ward_number_of_days'], 'safe'],
            [['ward_number_of_days'], 'safe'],
            // [['ward_start_datetime', 'ward_end_datetime'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['ward_start_datetime', 'ward_end_datetime'], 'match', 'pattern' => '/^(\d{4})\-(0[1-9]|1[0-2])\-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[0-1][0-9])\:[0-5][0-9](|\:[0-5][0-9])$/i'],
            //[['ward_end_date'], 'date', 'format' => 'Y-m-d'],
            //[['ward_end_time'], 'time', 'format' => 'H:i'],
            ['ward_end_date', 'match', 'pattern' => '/^(\d{4})\-(0[1-9]|1[0-2])\-(0[1-9]|[1-2][0-9]|3[0-1])$/i'],
            ['ward_end_time', 'match', 'pattern' => '/^(2[0-3]|[0-1][0-9])\:[0-5][0-9]$/i'],
			[['ward_uid', 'bill_uid'], 'string', 'max' => 64],
            [['ward_code'], 'string', 'max' => 20],
            [['ward_name'], 'string', 'max' => 50],
            [['ward_uid'], 'unique'],
            [['bill_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Bill::className(), 'targetAttribute' => ['bill_uid' => 'bill_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ward_uid' => Yii::t('app','Ward Uid'),
            'bill_uid' => Yii::t('app','Bill Uid'),
            'ward_code' => Yii::t('app','Ward Code'),
            'ward_name' => Yii::t('app','Ward Name'),
            'ward_start_datetime' => Yii::t('app','Ward Start Datetime'),
            'ward_end_datetime' => Yii::t('app','Ward End Datetime'),
            'ward_number_of_days' => Yii::t('app','Ward Number Of Days'),
            'ward_end_date' => Yii::t('app', 'Ward End Date'),
            'ward_end_time' => Yii::t('app', 'Ward End Time'),
        ];
    }

    /**
     * Gets query for [[BillU]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBillU()
    {
        return $this->hasOne(Bill::className(), ['bill_uid' => 'bill_uid']);
    }
	
	public function calculateDays() //there is a javascript version in view->ward->form. results may be different
	{
		$start_date = date_create($this->ward_start_datetime);
		$end_date = date_create($this->ward_end_datetime);
		$end_datetime = new \DateTime($this->ward_end_datetime);
		
		//if same day, set to minimum of 1
		//elseif not same day, change to date and calculate day difference
			//then if enddatetime's time is past 12 noon, add 1 day to calculation
		if($start_date == $end_date)
			return 1;
		
		
		$datediff = date_diff($start_date,$end_date);
		$numdays = (int)$datediff->format("%R%a");
		if(((int)$end_datetime->format('H')) >= 12)
			$numdays += 1;
		return $numdays;
	}
	
	public function getWardEndDatetime(){
		return $this->ward_end_date . ' ' . $this->ward_end_time;
	}
	
	public function dirtyUpdateWardEndDateAndTime(){
		$this->ward_end_date = explode(" ",$this->ward_end_datetime)[0];
		$this->ward_end_time = substr(explode(" ",$this->ward_end_datetime)[1], 0, 5);
	
	}
	
	
	static function getNaturalSortArgs()
	{
		return ['ward_end_datetime'=>SORT_ASC,
			'CAST(REGEXP_SUBSTR(ward_code,"[0-9]+") AS UNSIGNED)'=>SORT_ASC, 
			'length(ward_code)' => SORT_ASC,
			'ward_code'=>SORT_ASC]; 
	}
	
	
	
	
}
