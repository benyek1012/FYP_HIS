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
            [['department_code'], 'string', 'max' => 20,
                    'tooLong'=> Yii::t('app', '{attribute} should contain at most 20 characters')],
            [['department_name'], 'string', 'max' => 200, 
                    'tooLong'=> Yii::t('app','{attribute} should contain at most 200 characters')],
            [['phone_number', 'address1', 'address2', 'address3'], 'string', 'max' => 100, 
                    'tooLong'=>  Yii::t('app','{attribute} should contain at most 100 characters')],
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
            'department_uid' => Yii::t('app','Department Uid'),
            'department_code' => Yii::t('app','Department Code'),
            'department_name' => Yii::t('app','Department Name'),
            'phone_number' => Yii::t('app','Phone Number'),
            'address1' => Yii::t('app','Address 1'),
            'address2' => Yii::t('app','Address 2'),
            'address3' => Yii::t('app','Address 3'),
        ];
    }
	
	static function getNaturalSortArgs()
	{
		return ['length(department_code)' => SORT_ASC,
			'CAST(REGEXP_SUBSTR(department_code,"[0-9]+") AS UNSIGNED)'=>SORT_ASC, 
			'department_code'=>SORT_ASC
			]; 
	}
	
	
}
