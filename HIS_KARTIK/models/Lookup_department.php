<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lookup_department".
 *
 * @property string $department_uid
 * @property string $department_code
 * @property string $department_name
 * @property string|null $phone_number
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $address3
 */
class Lookup_department extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookup_department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['department_uid', 'department_code', 'department_name'], 'required'],
            [['department_uid'], 'string', 'max' => 64],
            [['department_code'], 'string', 'max' => 20],
            [['department_name'], 'string', 'max' => 50],
            [['phone_number', 'address1', 'address2', 'address3'], 'string', 'max' => 100],
            [['department_code'], 'unique'],
            [['department_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'department_uid' => 'Department Uid',
            'department_code' => 'Department Code',
            'department_name' => 'Department Name',
            'phone_number' => 'Phone Number',
            'address1' => 'Address 1',
            'address2' => 'Address 2',
            'address3' => 'Address 3',
        ];
    }
}
