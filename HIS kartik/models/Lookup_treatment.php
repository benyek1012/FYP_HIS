<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lookup_treatment".
 *
 * @property string $treatment_uid
 * @property string $treatment_code
 * @property string $treatment_name
 * @property float $class_1_cost_per_unit
 * @property float $class_2_cost_per_unit
 * @property float $class_3_cost_per_unit
 */
class Lookup_treatment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookup_treatment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['treatment_uid', 'treatment_code', 'treatment_name', 'class_1_cost_per_unit', 'class_2_cost_per_unit', 'class_3_cost_per_unit'], 'required'],
            [['class_1_cost_per_unit', 'class_2_cost_per_unit', 'class_3_cost_per_unit'], 'number'],
            [['treatment_uid'], 'string', 'max' => 64],
            [['treatment_code'], 'string', 'max' => 20],
            [['treatment_name'], 'string', 'max' => 50],
            [['treatment_code'], 'unique'],
            [['treatment_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'treatment_uid' => Yii::t('app','Treatment Uid'),
            'treatment_code' => Yii::t('app','Treatment Code'),
            'treatment_name' => Yii::t('app','Treatment Name'),
            'class_1_cost_per_unit' => Yii::t('app','Class  1 Cost Per Unit'),
            'class_2_cost_per_unit' => Yii::t('app','Class  2 Cost Per Unit'),
            'class_3_cost_per_unit' => Yii::t('app','Class  3 Cost Per Unit'),
        ];
    }
}
