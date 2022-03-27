<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $Tables = array(
            "CREATE TABLE IF NOT EXISTS `user` (
                `user_uid` VARCHAR(64) NOT NULL,
                `user_name` VARCHAR(100) NOT NULL,
                `user_password` VARCHAR(20) NOT NULL,
                `role` VARCHAR(20) NOT NULL,
                `retire` BOOLEAN DEFAULT false,
                PRIMARY KEY (`user_uid`)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `patient_information` (
                `patient_uid` VARCHAR(64) NOT NULL,
                `first_reg_date` DATE NOT NULL,
                `nric` VARCHAR(20) UNIQUE,
                `nationality` VARCHAR(20),
                `name` VARCHAR(200),
                `sex` VARCHAR(20),
                `phone_number` VARCHAR(100),
                `email` VARCHAR(100),
                `address1` VARCHAR(100),
                `address2` VARCHAR(100),
                `address3` VARCHAR(100),
                `job` VARCHAR(20),
                PRIMARY KEY (`patient_uid`)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `patient_next_of_kin` (
                `nok_uid` VARCHAR(64) NOT NULL,
                `patient_uid` VARCHAR(64) NOT NULL,
                `nok_name` VARCHAR(200),
                `nok_relationship` VARCHAR(20),
                `nok_phone_number` VARCHAR(100),
                `nok_email` VARCHAR(100),
                PRIMARY KEY (`nok_uid`),
                FOREIGN KEY (patient_uid) REFERENCES patient_information(patient_uid)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `patient_admission` (
                `rn` VARCHAR(11) NOT NULL,
                `entry_datetime` DATETIME NOT NULL,
                `patient_uid` VARCHAR(64) NOT NULL,
                `initial_ward_code` VARCHAR(20) NOT NULL,
                `initial_ward_class` VARCHAR(20) NOT NULL,
                `reference` VARCHAR(200),
                `medigal_legal_code` BOOLEAN DEFAULT false,
                `reminder_given` INT NOT NULL,
                `guarantor_name` VARCHAR(200),
                `guarantor_nric` VARCHAR(20),
                `guarantor_phone_number` VARCHAR(100),
                `guarantor_email` VARCHAR(100),
                PRIMARY KEY (`rn`),
                FOREIGN KEY (patient_uid) REFERENCES patient_information(patient_uid)
            );");
            
        for($i=0; $i < count($Tables); $i++)
        {
            $sqlCommand = Yii::$app->db->createCommand($Tables[$i]);
            $sqlCommand->execute();    
        }
          
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}