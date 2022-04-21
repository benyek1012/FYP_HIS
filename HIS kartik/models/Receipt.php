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
 * @property string $receipt_content_date_paid
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
            [['receipt_uid', 'rn', 'receipt_type', 'receipt_content_date_paid', 'receipt_content_payment_method', 'receipt_responsible', 'receipt_serial_number'], 'required'],
        //    [['receipt_content_sum'], 'number'],
            [['receipt_content_date_paid'], 'safe'],
            [['receipt_uid', 'rn', 'receipt_responsible'], 'string', 'max' => 64],
            [['receipt_type', 'receipt_content_bill_id', 'receipt_content_payment_method', 'card_no', 'cheque_number', 'receipt_serial_number'], 'string', 'max' => 20],
            [['receipt_content_description'], 'string', 'max' => 100],
            [['receipt_content_payer_name'], 'string', 'max' => 200],
            [['receipt_uid'], 'unique'],
            [['rn'], 'exist', 'skipOnError' => true, 'targetClass' => Patient_Admission::className(), 'targetAttribute' => ['rn' => 'rn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'receipt_uid' => 'Receipt Uid',
            'rn' => 'Rn',
            'receipt_type' => 'Receipt Type',
            'receipt_content_sum' => 'Receipt Summary',
            'receipt_content_bill_id' => 'Receipt Bill ID',
            'receipt_content_description' => 'Receipt Description',
            'receipt_content_date_paid' => 'Receipt Content Date Paid',
            'receipt_content_payer_name' => 'Payer Name',
            'receipt_content_payment_method' => 'Payment Method',
            'card_no' => 'Card Number',
            'cheque_number' => 'Cheque Number',
            'receipt_responsible' => 'Receipt Responsible',
            'receipt_serial_number' => 'Receipt Serial Number',
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
