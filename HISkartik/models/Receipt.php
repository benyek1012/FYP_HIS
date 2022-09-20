<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receipt".
 *
 * @property string $receipt_uid
 * @property string $rn
 * @property string $receipt_type
 * @property float $receipt_content_sum
 * @property string|null $receipt_content_bill_id
 * @property string|null $receipt_content_description
 * @property string $receipt_content_datetime_paid
 * @property string $receipt_content_payer_name
 * @property string $receipt_content_payment_method
 * @property string|null $payment_method_number
 * @property string $receipt_responsible
 * @property string|null $receipt_serial_number
 * @property string $kod_akaun
 *
 * @property PatientAdmission $rn0
 */
class Receipt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_uid', 'rn', 'receipt_type', 'receipt_content_sum', 'receipt_content_datetime_paid', 'receipt_content_payer_name', 'receipt_content_payment_method', 'receipt_responsible', 'kod_akaun'], 'required'],
            [['receipt_content_sum'], 'number'],
            [['receipt_content_datetime_paid'], 'safe'],
            [['receipt_uid', 'rn', 'receipt_responsible'], 'string', 'max' => 64],
            [['receipt_type', 'receipt_content_bill_id', 'receipt_content_payment_method', 'receipt_serial_number', 'kod_akaun'], 'string', 'max' => 20],
            [['receipt_content_description'], 'string', 'max' => 100],
            [['receipt_content_payer_name'], 'string', 'max' => 200],
            [['payment_method_number'], 'string', 'max' => 30],
            [['receipt_uid', 'receipt_serial_number'], 'unique'],
            [['rn'], 'exist', 'skipOnError' => true, 'targetClass' => Patient_admission::className(), 'targetAttribute' => ['rn' => 'rn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'receipt_uid' => Yii::t('app','Receipt Uid'),
            'rn' =>  Yii::t('app','Registration Number (R/N)'),
            'receipt_type' => Yii::t('app','Receipt Type'),
            'receipt_content_sum' => Yii::t('app','Receipt Summary (RM)'),
            'receipt_content_bill_id' => Yii::t('app','Receipt Bill ID'),
            'receipt_content_description' => Yii::t('app','Receipt Description'),
            'receipt_content_datetime_paid' => Yii::t('app','Payment Date'),
            'receipt_content_payer_name' => Yii::t('app','Payer Name'),
            'receipt_content_payment_method' => Yii::t('app','Payment Method'),
            'payment_method_number' => Yii::t('app','Payment Method Number'),
            'receipt_responsible' => Yii::t('app','Receipt Responsible'),
            'receipt_serial_number' => Yii::t('app','Receipt Serial Number'),
            'kod_akaun' => Yii::t('app','Account Code'),	
        ];
    }

    /**
     * Gets query for [[Rn0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRn0()
    {
        return $this->hasOne(Patient_admission::className(), ['rn' => 'rn']);
    }

    public function getPatient_admission() 
    {
        return $this->hasMany(Patient_admission::className(), ['rn' => 'rn']);
    }


    // return true if Bill doesn't exist || If admission is cancelled || Bill final fee is negative
    public static function checkRefunfable()
    {
        $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn'), 'deleted' => 0]);
        if(!empty($model_bill))
        {
            $finalFee = (new Bill()) -> calculateFinalFee($model_bill->bill_uid);
            if($finalFee < 0){
                return true;
            }
            else return false;
        }
        return true;
    }
    // Check whether deposite is generated
    public function isGenerated($rn){
            $row_receipt = Receipt::findOne(['rn' => $rn, 'receipt_type' => 'deposit']);
            if(!empty($row_receipt))
                return !empty($row_receipt['receipt_content_datetime_paid']) ? $row_receipt['receipt_content_datetime_paid'] : false;
    }
}
