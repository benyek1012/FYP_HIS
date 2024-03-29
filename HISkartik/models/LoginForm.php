<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app','Username'),
            'password' => Yii::t('app','Password'),
            'rememberMe' => Yii::t('app','Remember Me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
        
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login($model)
    {
        if ($this->validate()) {

            if(isset($model->rememberMe) && $model->rememberMe =="1")
            {
                // Cookie for login
                $newCookie= new \yii\web\Cookie();
                $newCookie->name='cookie_login';
                $newCookie->value = $this->getUserId();
                $newCookie->expire = time() + 60 * 60 * 24 * 180;
                Yii::$app->getResponse()->getCookies()->add($newCookie); 
            }

            if(!$this->validateRetire()) return false;
            else return Yii::$app->user->login($this->getUser(), isset($newCookie) ? time() + 60 * 60 * 24 * 180 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = (new New_user()) -> findByUsername($this->username);
        }

        return $this->_user;
    }

        /**
     * Finds user by [[userId]]
     *
     * @return User|null
     */
    public function getUserId()
    {
        if ($this->_user === false) {
            $this->_user = (new New_user()) -> findByUsername($this->username);
        }

        return $this->_user->user_uid;
    }

    public function hashPassword($password) {// Function to create password hash
        $salt = "stev37f";
        return md5($password.$salt);
    }

    public function validateRetire(){
        $user = New_user::findOne([$this->getUserId()]);
        if($user->retire == 1)
            return true;
        else return false;
    }
}