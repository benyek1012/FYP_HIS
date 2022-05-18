<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "new_user".
 *
 * @property string $user_uid
 * @property string $username
 * @property string $user_password
 * @property string $role
 * @property int|null $retire
 * @property string|null $authKey
 */
class Newuser extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'new_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_uid', 'username', 'user_password', 'role'], 'required'],
            [['retire'], 'boolean', 'strict'=> false],
            [['user_uid'], 'string', 'max' => 64],
            [['username'], 'string', 'max' => 100],
            [['password', 'role'], 'string', 'max' => 20],
            [['authKey'], 'string', 'max' => 45],
            [['user_uid'], 'unique'],
        ];
    }

    public function getAuthKey() {
        return  $this->authKey;
    }

    public function getId() {
        return $this->user_uid;
    }

    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    public function validateAuthKey ($authKey) {
        return $this->authKey === $authKey;
    }

    public static function findIdentity($user_uid) {
        return self::findOne($user_uid);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new \yii\base\NotSupportedException();
    }

    public static function findByUsername($username){
        return self::findOne(['username'=>$username]);
    }

    public function validatePassword($password){
        return $this->password === $password;
    }

   

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uid' => Yii::t('app','User Uid'),
            'username' => Yii::t('app','Username'),
            'password' => Yii::t('app','User Password'),
            'role' => Yii::t('app','Role'),
            'retire' => Yii::t('app','Retire'),
            'authKey' => Yii::t('app','Auth Key'),
        ];
    }
}
