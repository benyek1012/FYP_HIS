<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lookup_status".
 *
 * @property string $status_uid
 * @property string $status_code
 * @property string $status_description
 * @property float $class_1a_ward_cost
 * @property float $class_1b_ward_cost
 * @property float $class_1c_ward_cost
 * @property float $class_2_ward_cost
 * @property float $class_3_ward_cost
 */
class Lookup_status extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookup_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_uid', 'status_code', 'status_description', 'class_1a_ward_cost', 'class_1b_ward_cost', 'class_1c_ward_cost', 'class_2_ward_cost', 'class_3_ward_cost'], 'required'],
            [['class_1a_ward_cost', 'class_1b_ward_cost', 'class_1c_ward_cost', 'class_2_ward_cost', 'class_3_ward_cost'], 'number'],
            [['status_uid'], 'string', 'max' => 64],
            [['status_code'], 'string', 'max' => 20],
            [['status_description'], 'string', 'max' => 100],
            [['status_code'], 'unique'],
            [['status_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'status_uid' => Yii::t('app','Status Uid'),
            'status_code' => Yii::t('app','Status Code'),
            'status_description' => Yii::t('app','Status Description'),
            'class_1a_ward_cost' => Yii::t('app','Class  1a Ward Cost'),
            'class_1b_ward_cost' => Yii::t('app','Class  1b Ward Cost'),
            'class_1c_ward_cost' => Yii::t('app','Class  1c Ward Cost'),
            'class_2_ward_cost' => Yii::t('app','Class  2 Ward Cost'),
            'class_3_ward_cost' => Yii::t('app','Class  3 Ward Cost'),
        ];
    }
}
