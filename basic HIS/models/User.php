<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $user_uid
 * @property string $user_name
 * @property string $user_password
 * @property string $role
 * @property int|null $retire
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_uid', 'user_name', 'user_password', 'role'], 'required'],
            [['retire'], 'integer'],
            [['user_uid'], 'string', 'max' => 64],
            [['user_name'], 'string', 'max' => 100],
            [['user_password', 'role'], 'string', 'max' => 20],
            [['user_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uid' => 'User Uid',
            'user_name' => 'User Name',
            'user_password' => 'User Password',
            'role' => 'Role',
            'retire' => 'Retire',
        ];
    }
}
