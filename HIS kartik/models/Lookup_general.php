<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lookup_general".
 *
 * @property string $lookup_general_uid
 * @property string $code
 * @property string $category
 * @property string $name
 * @property string $long_description
 * @property int $recommend
 */
class Lookup_general extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookup_general';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lookup_general_uid', 'code', 'category', 'name'], 'required'],
            [['recommend'], 'boolean'],
            [['lookup_general_uid'], 'string', 'max' => 64],
            [['code', 'category'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 50],
            [['long_description'], 'string', 'max' => 100],
         //   [['code'], 'unique'],
            [['lookup_general_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lookup_general_uid' => Yii::t('app','Lookup General Uid'),
            'code' => Yii::t('app','Code'),
            'category' => Yii::t('app','Category'),
            'name' => Yii::t('app','Name'),
            'long_description' => Yii::t('app','Long Description'),
            'recommend' => Yii::t('app','Recommend'),
        ];
    }

}

