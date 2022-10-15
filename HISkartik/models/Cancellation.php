<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cancellation".
 *
 * @property string $cancellation_uid
 * @property string $table
 * @property string $reason
 * @property string|null $replacement_uid
 * @property string|null $deleted_datetime
 */
class Cancellation extends \yii\db\ActiveRecord
{

    public $checkbox_replacement;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cancellation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cancellation_uid', 'table', 'reason', 'responsible_uid', 'deleted_datetime'], 'required'],
            [['cancellation_uid', 'table', 'replacement_uid' ,'responsible_uid'], 'string', 'max' => 64],
            [['reason'], 'string', 'max' => 100],
            [['cancellation_uid'], 'unique'],
            [['deleted_datetime',], 'safe'],
            [['checkbox_replacement'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cancellation_uid' => Yii::t('app', 'Cancellation Uid'),
            'table' => Yii::t('app', 'Table'),
            'reason' => Yii::t('app', 'Reason'),
            'replacement_uid' => Yii::t('app', 'Replacement Uid'),
            'responsible_uid' => Yii::t('app', 'Responsible Uid'),
            'deleted_datetime' => Yii::t('app','Deleted Date'),
            'checkbox_replacement' => Yii::t('app', 'Without Replacement')
        ];
    }

    
    public function getReceipt() 
    {
        return $this->hasMany(Receipt::className(), ['receipt_uid' => 'cancellation_uid']);
    }
}
