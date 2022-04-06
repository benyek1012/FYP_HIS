<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ward".
 *
 * @property string $ward_uid
 * @property string $bill_uid
 * @property string $ward_code
 * @property string $ward_name
 * @property string $ward_start_datetime
 * @property string $ward_end_datetime
 * @property string $ward_number_of_days
 *
 * @property Bill $billU
 */
class Ward extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ward';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ward_uid', 'bill_uid', 'ward_code', 'ward_name', 'ward_start_datetime', 'ward_end_datetime', 'ward_number_of_days'], 'required'],
            [['ward_start_datetime', 'ward_end_datetime', 'ward_number_of_days'], 'safe'],
            [['ward_uid', 'bill_uid'], 'string', 'max' => 64],
            [['ward_code'], 'string', 'max' => 20],
            [['ward_name'], 'string', 'max' => 50],
            [['ward_uid'], 'unique'],
            [['bill_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Bill::className(), 'targetAttribute' => ['bill_uid' => 'bill_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ward_uid' => 'Ward Uid',
            'bill_uid' => 'Bill Uid',
            'ward_code' => 'Ward Code',
            'ward_name' => 'Ward Name',
            'ward_start_datetime' => 'Ward Start Datetime',
            'ward_end_datetime' => 'Ward End Datetime',
            'ward_number_of_days' => 'Ward Number Of Days',
        ];
    }

    /**
     * Gets query for [[BillU]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBillU()
    {
        return $this->hasOne(Bill::className(), ['bill_uid' => 'bill_uid']);
    }
}
