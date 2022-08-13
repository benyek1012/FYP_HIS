<?php

namespace app\models;

use app\controllers\New_userController;
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

    public $new_password;
    public $original_password;
    public $confirm_new_password;

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
            [['Case_Note'], 'string', 'max' => 64,],
            [['Registration'], 'string', 'max' => 64,],
            [['Charge_Sheet'], 'string', 'max' => 64,],
            [['Sticker_Label'], 'string', 'max' => 64,],
            [['user_password'], 'string', 'max' => 40],
            [['authKey'], 'string', 'max' => 45],
            [['user_uid'], 'unique'],
            [['username'], 'unique'],    
            
            [['new_password', 'original_password', 'confirm_new_password'], 'safe'],
            [['original_password'] ,'validateOriginalPassword'],
            [['new_password', 'confirm_new_password', 'original_password'], 'string', 'max' => 40],
            [['new_password', 'confirm_new_password'], 'filter', 'filter' => 'trim'],
            [['confirm_new_password'], 'compare', 'compareAttribute' => 'new_password', 'message' => Yii::t('app', 'Passwords does not match with new password')],
        ];
    }

    public function validateOriginalPassword() {
        if(!$this->verifyPassword($this->original_password)) {
            $this->addError("original_password", Yii::t('app' ,'Original Password Incorrect'));
        }
    }

    public function verifyPassword($password) {
        if($this->hashPassword($password) == $this->user_password){
            return true;
        }
        else{
            return false;
        }
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
            'role_cashier' => Yii::t('app','Cashier Role'),
            'role_clerk' => Yii::t('app','Clerk Role'),
            'role_admin' => Yii::t('app','Admin Role'),
            'role_guest_print' => Yii::t('app','Guest Print Role'),
            'Case_Note' => Yii::t('app', 'Case Note'),
            'Registration' => Yii::t('app', 'Registration'),
            'Charge_Sheet' => Yii::t('app', 'Charge Sheet'),
            'Sticker_Label' => Yii::t('app', 'Sticker Label'),
            'retire' => Yii::t('app','Active'),
            'authKey' => Yii::t('app','Auth Key'),
            'original_password' => Yii::t('app', 'Original Password'),
            'new_password' => Yii::t('app', 'New Password'),
            'confirm_new_password' => Yii::t('app', 'Confirm New Password'),
        ];
    }
}
