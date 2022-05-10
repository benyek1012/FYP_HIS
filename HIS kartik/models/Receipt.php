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
 * @property string|null $card_no
 * @property string|null $cheque_number
 * @property string $receipt_responsible
 * @property string|null $receipt_serial_number
 *
 * @property BillContentReceipt[] $billContentReceipts
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
            [['receipt_uid', 'rn', 'receipt_type', 'receipt_content_datetime_paid','receipt_content_sum', 'receipt_content_payment_method', 'receipt_responsible', 'receipt_serial_number'], 'required'],
            [['receipt_content_sum'], 'double'],
            [['receipt_content_datetime_paid'], 'safe'],
            [['receipt_uid', 'rn', 'receipt_responsible'], 'string', 'max' => 64],
            [['receipt_type', 'receipt_content_bill_id', 'receipt_content_payment_method', 'card_no', 'cheque_number', 'receipt_serial_number'], 'string', 'max' => 20],
            [['receipt_content_description'], 'string', 'max' => 100],
            [['receipt_content_payer_name'], 'string', 'max' => 200],
            [['receipt_uid'], 'unique'],
           // [['receipt_serial_number'], 'integer'],
          //  [['receipt_serial_number'], 'match', 'pattern' => '/^\d{7}$/', 'message' => 'Field must contain exactly 7 digits.'],
            [['receipt_serial_number'], 'unique'],
           // [['receipt_serial_number'], 'exist', 'skipOnError' => true, 'targetClass' => Receipt::className(), 'targetAttribute' => ['receipt_serial_number' => 'receipt_serial_number']],
         //    [['receipt_serial_number', 'unique', 'targetClass' => Receipt::className(), 'targetAttribute' => ['receipt_serial_number' => 'receipt_serial_number'],
           //      'message' => 'This receipt serial number is use.']],
            [['rn'], 'exist', 'skipOnError' => true, 'targetClass' => Patient_Admission::className(), 'targetAttribute' => ['rn' => 'rn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'receipt_uid' => Yii::t('app','Receipt Uid'),
            'rn' => 'Rn',
            'receipt_type' => Yii::t('app','Receipt Type'),
            'receipt_content_sum' => Yii::t('app','Receipt Summary (RM)'),
            'receipt_content_bill_id' => Yii::t('app','Receipt Bill ID'),
            'receipt_content_description' => Yii::t('app','Receipt Description'),
            'receipt_content_datetime_paid' => Yii::t('app','Payment Date'),
            'receipt_content_payer_name' => Yii::t('app','Payer Name'),
            'receipt_content_payment_method' => Yii::t('app','Payment Method'),
            'card_no' => Yii::t('app','Card Number'),
            'cheque_number' => Yii::t('app','Cheque Number'),
            'receipt_responsible' => Yii::t('app','Receipt Responsible'),
            'receipt_serial_number' => Yii::t('app','Receipt Serial Number'),
        ];
    }

    /**
     * Gets query for [[BillContentReceipts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBillContentReceipts()
    {
        return $this->hasMany(Bill_Content_Receipt::className(), ['receipt_uid' => 'receipt_uid']);
    }

    /**
     * Gets query for [[Rn0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRn0()
    {
        return $this->hasOne(Patient_Admission::className(), ['rn' => 'rn']);
    }
}
