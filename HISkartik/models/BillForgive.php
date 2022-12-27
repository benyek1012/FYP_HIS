<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bill_forgive".
 *
 * @property string $bill_forgive_uid
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
            [['bill_forgive_uid', 'bill_forgive_date', 'comment'], 'required'],
            [['bill_forgive_date'], 'safe'],
            [['bill_forgive_uid'], 'string', 'max' => 64],
            [['comment'], 'string', 'max' => 200],
            [['bill_forgive_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_forgive_uid' => 'Bill Forgive Uid',
            'bill_forgive_date' => Yii::t('app','Date Forgiven'),
            'comment' => Yii::t('app','Comment'),
        ];
    }
}
