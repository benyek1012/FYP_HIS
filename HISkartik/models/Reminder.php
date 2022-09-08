<?php

namespace app\models;
use app\models\New_user;

use yii\db\Transaction;

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
    const placeholder = '9999-12-31';
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
            [['batch_date'], 'required'],
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

    public static function getReminderCalculate($responsible_uid_)
    {
        //placeholder = date('9999-12-31');
        $MIN_DATE = Reminder::find()->where(['<>','batch_date', date(Reminder::placeholder)])->max('batch_date');
        if(empty($MIN_DATE))
            $MIN_DATE = date('1900-12-31'); 
        $MAX_DATE = date("Y-m-d");
        $result = \yii::$app->db->createCommand("CALL reminder_batch_select(:MIN_DATE, :MAX_DATE, :responsible_uid_)")
        ->bindValue(':MIN_DATE' , $MIN_DATE)
        ->bindValue(':MAX_DATE' , $MAX_DATE)
        ->bindValue(':responsible_uid_' , $responsible_uid_)
        ->execute();

        if(!empty($result))
        return $result;

    }

    // create batch function
    // functions require
    // -update patient_admission and reminder_letter
  
    public static function batchCreate($responsible_uid)
    {
    
        $currentdate = date("Y-m-d");
        //$placeholder = date('9999-12-31');
        

        $transaction = Patient_admission::getDb()->beginTransaction();
        try {
            // -update reminder 1,2,3 date to current date in patient_admission where date = placeholder 
            Patient_admission::updateAll(['reminder1' => $currentdate], ['=', 'reminder1', date(Reminder::placeholder)]);
            Patient_admission::updateAll(['reminder2' => $currentdate], ['=', 'reminder2', date(Reminder::placeholder)]);
            Patient_admission::updateAll(['reminder3' => $currentdate], ['=', 'reminder3', date(Reminder::placeholder)]);
            // -update reminder_letter table batch_date where date = placeholder to current date
            Reminder::updateAll(['batch_date' => $currentdate, 'responsible_uid' => $responsible_uid],['=','batch_date',date(Reminder::placeholder)]);


            // -create new row with placeholder set reminder1,2,3 value as 0, and batch_date = placeholder
            $batchcreate = new Reminder();
            $batchcreate->reminder1count = 0;
            $batchcreate->reminder2count = 0;
            $batchcreate->reminder3count = 0;
            $batchcreate->batch_date = date(Reminder::placeholder);
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

    public static function createPlaceholderIfNotExists(){
        if (0==Reminder::find(['batch_date' => date(Reminder::placeholder)])->count())
        {
            $model = new Reminder();
            $model->batch_date = date(Reminder::placeholder);
            $model->reminder1count = 0;
            $model->reminder2count = 0;
            $model->reminder3count = 0;
            $model->save();
            //echo 'test';
        }
    }

    public function getBills()
    {
        return $this->hasOne(Bill::className(), ['rn' => 'rn', 'deleted' => 0]);
    }

    public function getPatient_admission() 
    {
        return $this->hasMany(Patient_admission::className(), ['reminder1' => 'batch_date']);
    }

/*    public function getId() {
        return $this->username;
    }

    public function checkName($user){
        $user = New_user::findOne([Yii::$app->user->identity->getId($user)]);
        return $user->name;
        //return $user;    
    } */

   
}

