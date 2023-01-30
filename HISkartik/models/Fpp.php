<?php

namespace app\models;

use Yii;
use app\models\Bill;
use app\models\Lookup_fpp;

/**
 * This is the model class for table "fpp".
 *
 * @property string $kod
 * @property string $name
 * @property string $additional_details
 * @property float $min_cost_per_unit
 * @property float $max_cost_per_unit
 * @property int $number_of_units
 * @property float $total_cost
 */
class Fpp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fpp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['kod', 'name', 'additional_details', 'min_cost_per_unit', 'max_cost_per_unit', 'number_of_units', 'total_cost', 'cost_per_unit'], 'required'],
            [['min_cost_per_unit', 'max_cost_per_unit', 'total_cost', 'cost_per_unit'], 'number'],
            [['number_of_units'], 'integer'],
            [['kod', 'bill_uid', 'fpp_uid'], 'string', 'max' => 64,
                    'tooLong'=> Yii::t('app', '{attribute} should contain at most 64 characters')],
            [['additional_details', 'name'], 'string', 'max' => 200,
                    'tooLong'=> Yii::t('app', '{attribute} should contain at most 200 characters')],
            [['fpp_uid'], 'unique'],
            [['bill_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Bill::className(), 'targetAttribute' => ['bill_uid' => 'bill_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fpp_uid' => Yii::t('app', 'FPP Uid'),
            'kod' => 'Kod',
            'bill_uid' => Yii::t('app','Bill Uid'),
            'name' => Yii::t('app', 'Name'),
            'additional_details' => Yii::t('app', 'Additional Details'),
            'min_cost_per_unit' => Yii::t('app', 'Min Cost Per Unit'),
            'cost_per_unit' => Yii::t('app', 'Cost Per Unit'),
            'max_cost_per_unit' => Yii::t('app', 'Max Cost Per Unit'),
            'number_of_units' => Yii::t('app', 'Number Of Units'),
            'total_cost' => Yii::t('app', 'Total Cost'),
        ];
    }
	
	public function refreshInfoAndRecalculate(){
			
		$bill_model = Bill::find()->where(['bill_uid'=>$this->bill_uid])->one();
	
		$lookup_fpp_model = Lookup_fpp::find()->where(['kod'=>$this->kod])->one();
		
		if(empty($lookup_fpp_model))
			throw new Exception("Fpp code does not exist");
			
		$this->name = $lookup_fpp_model->name;
		$this->min_cost_per_unit = $lookup_fpp_model->min_cost_per_unit;
		$this->max_cost_per_unit = $lookup_fpp_model->max_cost_per_unit;
		
		$this->total_cost = ((float)$this->cost_per_unit) * ((float)$this->number_of_units);
		
		return $this->total_cost;
	}
	
	
	static function getNaturalSortArgs()
	{
		return ['CAST(REGEXP_SUBSTR(kod,"[0-9]+") AS UNSIGNED)'=>SORT_ASC, 
			'length(kod)' => SORT_ASC,
			'kod'=>SORT_ASC]; 
	}
	
}
