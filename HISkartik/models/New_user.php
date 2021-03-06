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
class New_user extends \yii\db\ActiveRecord implements IdentityInterface
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
            [['user_uid', 'username', 'user_password'], 'required'],
            [['retire'], 'boolean', 'strict'=> false],
            [['user_uid'], 'string', 'max' => 64],
            [['username'], 'string', 'max' => 100],
            [['role_cashier','role_clerk', 'role_admin', 'role_guest_print'], 'boolean', 'strict'=> false],
            [['user_password', 'password_repeat'], 'string', 'max' => 40],
            [['authKey'], 'string', 'max' => 45],
            [['user_uid'], 'unique'],
            [['username'], 'unique'],            
        ];
    }

    public function getAuthKey() {
        return  $this->authKey;
    }

    public function getId() {
        return $this->user_uid;
    }

    public function getName() {
        return $this->username;
    }

    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    public function validateAuthKey ($authKey) {
        return $this->authKey === $authKey;
    }

    public static function findIdentity($user_uid) {
        return static::findOne(['user_uid' => $user_uid]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new \yii\base\NotSupportedException();
    }

    public function findByUsername($username){
        return self::findOne(['username'=>$username]);
    }

    public function isCashier(){
        $user = New_user::findOne([Yii::$app->user->identity->getId()]);
        if($user->role_cashier == '1')
            return true;
        else return false;
    }

    public function isClerk(){
        $user = New_user::findOne([Yii::$app->user->identity->getId()]);
        if($user->role_clerk == '1')
            return true;
        else return false;
    }

    public function isAdmin(){
        $user = New_user::findOne([Yii::$app->user->identity->getId()]);
        if($user->role_admin == '1')
            return true;
        else return false;
    }

    public function isClerkorAdmin(){
        $user = New_user::findOne([Yii::$app->user->identity->getId()]);
        if($user->role_clerk == '1' || $user->role_admin == '1')
            return true;
        else return false;
    }

    public function isCashierorClerk(){
        $user = New_user::findOne([Yii::$app->user->identity->getId()]);
        if($user->role_cashier == '1' || $user->role_clerk == '1')
            return true;
        else return false;
    }

    public function isCashierorAdmin(){
        $user = New_user::findOne([Yii::$app->user->identity->getId()]);
        if($user->role_cashier == '1' || $user->role_admin == '1')
            return true;
        else return false;
    }

    public function isCashierorAdminorClerk(){
        $user = New_user::findOne([Yii::$app->user->identity->getId()]);
        if($user->role_cashier == '1' || $user->role_admin == '1' || $user->role_clerk == '1')
            return true;
        else return false;
    }

    public function validatePassword($password){
        return $this->user_password ===  (new LoginForm())  -> hashPassword($password);
    }

    public static function hashPassword($password) {// Function to create password hash
        $salt = "stev37f";
        return md5($password.$salt);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_uid' => Yii::t('app','User Uid'),
            'username' => Yii::t('app','Username'),
            'user_password' => Yii::t('app','User Password'),
            'password_repeat' => Yii::t('app','Password Repeat'),
            'role_cashier' => Yii::t('app','Cashier Role'),
            'role_clerk' => Yii::t('app','Clerk Role'),
            'role_admin' => Yii::t('app','Admin Role'),
            'role_guest_print' => Yii::t('app','Guest Print Role'),
            'retire' => Yii::t('app','Active'),
            'authKey' => Yii::t('app','Auth Key'),
        ];
    }
}
