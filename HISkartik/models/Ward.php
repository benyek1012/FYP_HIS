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
    public $ward_end_date;
    public $ward_end_time;

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
            // [['ward_code', 'ward_name', 'ward_start_datetime', 'ward_end_datetime', 'ward_number_of_days'], 'required'],
            // [['ward_start_datetime', 'ward_end_datetime', 'ward_number_of_days'], 'safe'],
            [['ward_number_of_days'], 'safe'],
            // [['ward_start_datetime', 'ward_end_datetime'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['ward_start_datetime', 'ward_end_datetime'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['ward_end_date'], 'datetime', 'format' => 'php:Y-m-d'],
            [['ward_end_time'], 'datetime', 'format' => 'php:H:i'],
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
            'ward_uid' => Yii::t('app','Ward Uid'),
            'bill_uid' => Yii::t('app','Bill Uid'),
            'ward_code' => Yii::t('app','Ward Code'),
            'ward_name' => Yii::t('app','Ward Name'),
            'ward_start_datetime' => Yii::t('app','Ward Start Datetime'),
            'ward_end_datetime' => Yii::t('app','Ward End Datetime'),
            'ward_number_of_days' => Yii::t('app','Ward Number Of Days'),
            'ward_end_date' => Yii::t('app', 'Ward End Date'),
            'ward_end_time' => Yii::t('app', 'Ward End Time'),
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
