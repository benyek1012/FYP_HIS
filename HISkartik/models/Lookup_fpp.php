<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lookup_fpp".
 *
 * @property string $kod
 * @property string $name
 * @property float $min_cost_per_unit
 * @property float $max_cost_per_unit
 */
class Lookup_fpp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookup_fpp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kod', 'name', 'min_cost_per_unit', 'max_cost_per_unit'], 'required'],
            [['min_cost_per_unit', 'max_cost_per_unit'], 'number'],
            [['kod', 'name'], 'string', 'max' => 64],
            [['kod'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kod' => 'Kod',
            'name' => Yii::t('app', 'Name'),
            'min_cost_per_unit' => Yii::t('app', 'Min Cost Per Unit'),
            'max_cost_per_unit' => Yii::t('app', 'Max Cost Per Unit'),
        ];
    }
}
