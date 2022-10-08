<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "serial_number".
 *
 * @property string $serial_name
 * @property string $prepend
 * @property int $digit_length
 * @property int $running_value
 */
class SerialNumber extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'serial_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['serial_name', 'prepend', 'digit_length', 'running_value'], 'required'],
            [['digit_length', 'running_value'], 'integer'],
            [['serial_name', 'prepend'], 'string', 'max' => 11],
            [['serial_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'serial_name' => 'Serial Name',
            'prepend' => 'Prepend',
            'digit_length' => 'Digit Length',
            'running_value' => 'Running Value',
        ];
    }

    public static function getSerialNumber($type)
    {
        $model = SerialNumber::findOne(['serial_name' => $type]);

        $digit_length = $model->digit_length;
        $running_value = sprintf('%0'.$digit_length.'d', ($model->running_value + 1));

        return $model->prepend.$running_value;
    }

}
