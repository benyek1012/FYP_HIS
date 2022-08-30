<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_letter".
 *
 * @property string $batch_date
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
            [['batch_date', 'responsible'], 'required'],
            [['batch_date', 'reminder1', 'reminder2', 'reminder3'], 'safe'],
            [['responsible'], 'string', 'max' => 64],
            [['batch_date'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'batch_date' => 'Batch Date',
            'reminder1' => 'Reminder 1',
            'reminder2' => 'Reminder 2',
            'reminder3' => 'Reminder 3',
            'responsible' => 'Responsible',
        ];
    }

    public function getReminderBatchSelect($MIN_DATE, $MAX_DATE, $responsible_uid)
    {
        $result = \yii::$app->db->createCommand("CALL reminder_batch_select(:MIN_DATE, :MAX_DATE, :responsible_uid)")
        ->bindValue(':MIN_DATE' , $MIN_DATE)
        ->bindValue(':MAX_DATE' , $MAX_DATE)
        ->bindValue(':responsible_uid' , $responsible_uid)
        ->queryAll();

        if(!empty($result))
        return $result;

    }
}
