<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "variable".
 *
 * @property int $read_only
 */
class Variable extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'variable';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['read_only'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'read_only' => 'Read Only',
        ];
    }
}
