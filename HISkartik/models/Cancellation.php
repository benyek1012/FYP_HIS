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
 */
class Cancellation extends \yii\db\ActiveRecord
{
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
            [['cancellation_uid', 'table', 'reason'], 'required'],
            [['cancellation_uid', 'table', 'replacement_uid'], 'string', 'max' => 64],
            [['reason'], 'string', 'max' => 100],
            [['cancellation_uid'], 'unique'],
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
        ];
    }
}
