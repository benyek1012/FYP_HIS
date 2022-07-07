<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "serial".
 *
 * @property int $bill_serial
 * @property int|null $receipt_serial
 */
class Serial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'serial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_serial', 'receipt_serial'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_serial' => 'Bill Serial',
            'receipt_serial' => 'Receipt Serial',
        ];
    }

    public function getReceiptSerialNumber()
    {
        $count = Serial::find()->select('receipt_serial')
        ->andWhere(['not', ['receipt_serial' => 0]])
        ->count();
        return $count +1;
    }

    public function getBillSerialNumber()
    {
        $count = Serial::find()->select('bill_serial')
        ->andWhere(['not', ['bill_serial' => 0]])
        ->count();
        return $count +1;
    }
}
