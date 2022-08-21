<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_letter".
 *
 * @property string $batch_uid
 * @property string $batch_datetime
 * @property string|null $reminder1
 * @property string|null $reminder2
 * @property string|null $reminder3
 * @property string $responsible
 */
class Reminder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder_letter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch_uid', 'batch_datetime', 'responsible'], 'required'],
            [['batch_datetime', 'reminder1', 'reminder2', 'reminder3'], 'safe'],
            [['batch_uid', 'responsible'], 'string', 'max' => 64],
            [['batch_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'batch_uid' => 'Batch Uid',
            'batch_datetime' => 'Batch Datetime',
            'reminder1' => 'Reminder 1',
            'reminder2' => 'Reminder 2',
            'reminder3' => 'Reminder 3',
            'responsible' => 'Responsible',
        ];
    }
}
