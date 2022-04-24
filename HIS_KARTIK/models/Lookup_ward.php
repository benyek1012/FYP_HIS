<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lookup_ward".
 *
 * @property string $ward_uid
 * @property string $ward_code
 * @property string $ward_name
 * @property string|null $sex
 * @property int|null $min_age
 * @property int|null $max_age
 */
class Lookup_ward extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lookup_ward';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ward_uid', 'ward_code', 'ward_name'], 'required'],
            [['min_age', 'max_age'], 'integer'],
            [['ward_uid'], 'string', 'max' => 64],
            [['ward_code', 'sex'], 'string', 'max' => 20],
            [['ward_name'], 'string', 'max' => 50],
            [['ward_code'], 'unique'],
            [['ward_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ward_uid' => 'Ward Uid',
            'ward_code' => 'Ward Code',
            'ward_name' => 'Ward Name',
            'sex' => 'Sex',
            'min_age' => 'Min Age',
            'max_age' => 'Max Age',
        ];
    }
}
