<?php

namespace app\models;

use yii\db\Transaction;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;

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

  

    public function getReminderBatchSelect($MIN_DATE, $MAX_DATE, $responsible_uid_)
    {
        $result = \yii::$app->db->createCommand("CALL reminder_batch_select(:MIN_DATE, :MAX_DATE, :responsible_uid_)")
        ->bindValue(':MIN_DATE' , $MIN_DATE)
        ->bindValue(':MAX_DATE' , $MAX_DATE)
        ->bindValue(':responsible_uid_' , $responsible_uid_)
        ->queryAll();

        if(!empty($result))
        return $result;

    }

    // create batch function
    // functions require
    // -update patient_admission and reminder_letter
  
    public function batchCreate($responsible_uid)
    {
    
        $currentdate = date("Y-m-d");
        $placeholder = date("Y-m-d", '9999-12-31');
        
        //transaction used to prevent any of the update goes wrong
        $transaction = Patient_admission::getDb()->beginTransaction();
        try {
            // -update reminder 1,2,3 date to current date in patient_admission where date = placeholder 
            Patient_admission::updateAll(['reminder1' => $currentdate], ['=', 'reminder1', $placeholder]);
            Patient_admission::updateAll(['reminder2' => $currentdate], ['=', 'reminder2', $placeholder]);
            Patient_admission::updateAll(['reminder3' => $currentdate], ['=', 'reminder3', $placeholder]);
            // -update reminder_letter table batch_date where date = placeholder to current date
            Reminder::updateAll(['batch_date' => $currentdate, 'responsible_uid' => $responsible_uid],['=','batch_date',$placeholder]);


            // -create new row with placeholder set reminder1,2,3 value as 0, and batch_date = placeholder
            $batchcreate = new Reminder();
            $batchcreate->reminder1 = 0;
            $batchcreate->reminder2 = 0;
            $batchcreate->reminder3 = 0;
            $batchcreate->batch_date = $placeholder;
            $batchcreate->save();

            //.... other SQL executions
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


   
}
