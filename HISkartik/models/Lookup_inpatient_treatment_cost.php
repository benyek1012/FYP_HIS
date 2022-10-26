<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lookup_inpatient_treatment_cost".
 *
 * @property string $inpatient_treatment_uid
 * @property string $kod
 * @property float $cost_rm
 */
class Lookup_inpatient_treatment_cost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookup_inpatient_treatment_cost';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpatient_treatment_uid', 'kod'], 'required'],
            [['cost_rm'], 'number'],
            [['inpatient_treatment_uid', 'kod'], 'string', 'max' => 64],
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
            'kod' => 'Kod',
            'cost_rm' => 'Cost Rm',
        ];
    }
}
