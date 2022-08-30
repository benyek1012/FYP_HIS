<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder_letter".
 *
 * @property string $batch_date
 * @property string|null $reminder1count
 * @property string|null $reminder2count
 * @property string|null $reminder3count
 * @property string $responsible_uid
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
            [['batch_date', 'responsible_uid'], 'required'],
            [['batch_date', 'reminder1count', 'reminder2count', 'reminder3count'], 'safe'],
            [['responsible_uid'], 'string', 'max' => 64],
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
            'reminder1count' => 'Reminder 1',
            'reminder2count' => 'Reminder 2',
            'reminder3count' => 'Reminder 3',
            'responsible_uid' => 'Responsible',
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
