<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bill_forgive".
 *
 * @property string $bill_forgive_date
 * @property string $comment
 */
class BillForgive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bill_forgive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_forgive_date', 'comment'], 'required'],
            [['bill_forgive_date'], 'safe'],
            [['comment'], 'string', 'max' => 200],
            [['bill_forgive_date'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_forgive_date' => 'Bill Forgive Date',
            'comment' => 'Comment',
        ];
    }
}
