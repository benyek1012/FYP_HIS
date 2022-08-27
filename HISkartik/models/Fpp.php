<?php

namespace app\models;

use Yii;

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
            [['kod', 'name', 'additional_details', 'min_cost_per_unit', 'max_cost_per_unit', 'number_of_units', 'total_cost'], 'required'],
            [['min_cost_per_unit', 'max_cost_per_unit', 'total_cost'], 'number'],
            [['number_of_units'], 'integer'],
            [['kod', 'name'], 'string', 'max' => 64],
            [['additional_details'], 'string', 'max' => 200],
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
            'name' => 'Name',
            'additional_details' => 'Additional Details',
            'min_cost_per_unit' => 'Min Cost Per Unit',
            'max_cost_per_unit' => 'Max Cost Per Unit',
            'number_of_units' => 'Number Of Units',
            'total_cost' => 'Total Cost',
        ];
    }
}
