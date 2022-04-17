<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bill_content_receipt".
 *
 * @property string $bill_content_receipt_uid
 * @property string $bill_uid
 * @property string $receipt_uid
 *
 * @property Bill $billU
 * @property Receipt $receiptU
 */
class Bill_content_receipt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bill_content_receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_content_receipt_uid', 'bill_uid', 'receipt_uid'], 'required'],
            [['bill_content_receipt_uid', 'bill_uid', 'receipt_uid'], 'string', 'max' => 64],
            [['bill_content_receipt_uid'], 'unique'],
            [['bill_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Bill::className(), 'targetAttribute' => ['bill_uid' => 'bill_uid']],
            [['receipt_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Receipt::className(), 'targetAttribute' => ['receipt_uid' => 'receipt_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_content_receipt_uid' => 'Bill Content Receipt Uid',
            'bill_uid' => 'Bill Uid',
            'receipt_uid' => 'Receipt Uid',
        ];
    }

    /**
     * Gets query for [[BillU]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBillU()
    {
        return $this->hasOne(Bill::className(), ['bill_uid' => 'bill_uid']);
    }

    /**
     * Gets query for [[ReceiptU]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceiptU()
    {
        return $this->hasOne(Receipt::className(), ['receipt_uid' => 'receipt_uid']);
    }
}