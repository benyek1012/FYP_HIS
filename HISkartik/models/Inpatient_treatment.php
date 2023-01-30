<?php

namespace app\models;
use app\models\Ward;
use app\models\Lookup_inpatient_treatment_cost;

use Yii;

/**
 * This is the model class for table "inpatient_treatment".
 *
 * @property string $inpatient_treatment_uid
 * @property string $bill_uid
 * @property float $inpatient_treatment_cost_rm
 */
class Inpatient_treatment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpatient_treatment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpatient_treatment_uid', 'bill_uid', 'inpatient_treatment_cost_rm'], 'required'],
            [['inpatient_treatment_cost_rm'], 'number'],
            [['inpatient_treatment_uid', 'bill_uid'], 'string', 'max' => 64],
            [['inpatient_treatment_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inpatient_treatment_uid' => 'Inpatient Treatment Uid',
            'bill_uid' => 'Bill Uid',
            'inpatient_treatment_cost_rm' => 'Inpatient Treatment Cost Rm',
        ];
    }
	
	public function recalculateCost(){
		$bill_model = Bill::find()->where(['bill_uid'=>$this->bill_uid])->one();
		if(!empty($bill_model->status_code)){
            if($bill_model->status_code == 'PDOA'){
				$sum_days = Ward::find()->where(['bill_uid'=>$this->bill_uid])->sum('ward_number_of_days');
				$cost_daily = Lookup_inpatient_treatment_cost::findOne(['kod'=>'Inpatient Treatment'])->cost_rm;
				$this->inpatient_treatment_cost_rm = (float)$sum_days * (float)$cost_daily;
				return $this->inpatient_treatment_cost_rm;
            }

        }
		return $this->inpatient_treatment_cost_rm = 0;
		 
	}
}
