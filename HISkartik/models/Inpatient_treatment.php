<?php

namespace app\models;

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
}
