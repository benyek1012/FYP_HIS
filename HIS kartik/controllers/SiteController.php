<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Patient_informationSearch;
use app\controllers\Patient_informationController;
use app\models\Patient_next_of_kin;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;

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
                `nric` VARCHAR(20),
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
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `bill` (
                `bill_uid` VARCHAR(64) NOT NULL,
                `rn` VARCHAR(11) NOT NULL,
                `status_code` VARCHAR(20) NOT NULL,
                `status_description` VARCHAR(100) NOT NULL,
                `class` VARCHAR(20) NOT NULL,
                `daily_ward_cost` DECIMAL(10,2) NOT NULL,
                `department_code` VARCHAR(20),
                `department_name` VARCHAR(50),
                `is_free` BOOLEAN NOT NULL DEFAULT false,
                `collection_center_code` VARCHAR(20),
                `nurse_responsilbe` VARCHAR(20),
                `bill_generation_datetime` DATETIME,
                `generation_responsible_uid` VARCHAR(64),
                `bill_generation_billable_sum_rm` DECIMAL(10,2),
                `bill_generation_final_fee_rm` DECIMAL(10,2),
                `description` VARCHAR(200),
                `bill_print_responsible_uid` VARCHAR(64),
                `bill_print_datetime` DATETIME,
                `bill_print_id` VARCHAR(20) UNIQUE, 
                 PRIMARY KEY (`bill_uid`),
                 FOREIGN KEY (rn) REFERENCES patient_admission(rn)
                /* FOREIGN KEY (generation_responsible_uid) REFERENCES,
                 FOREIGN KEY (bill_print_responsible_uid) REFERENCES,*/
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `ward` (
                `ward_uid` VARCHAR(64) NOT NULL,
                `bill_uid` VARCHAR(64) NOT NULL,
                `ward_code` VARCHAR(20) NOT NULL,
                `ward_name` VARCHAR(50) NOT NULL,
                `ward_start_datetime` DATETIME NOT NULL,
                `ward_end_datetime` DATETIME NOT NULL,
                `ward_number_of_days` DATETIME NOT NULL,
                PRIMARY KEY (`ward_uid`),
                FOREIGN KEY (bill_uid) REFERENCES bill(bill_uid)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `treatment_details` (
                `treatment_details_uid` VARCHAR(64) NOT NULL,
                `bill_uid` VARCHAR(64) NOT NULL,
                `treatment_code` VARCHAR(20) NOT NULL,
                `treatment_name` VARCHAR(50) NOT NULL,
                `item_per_unit_cost_rm` DECIMAL(10,2) NOT NULL,
                `item_count` INT NOT NULL,
                `item_total_unit_cost_rm` DECIMAL(10,2) NOT NULL,
                PRIMARY KEY (`treatment_details_uid`),
                FOREIGN KEY (bill_uid) REFERENCES bill(bill_uid)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `receipt` (
                `receipt_uid` VARCHAR(64) NOT NULL,
                `rn` VARCHAR(64) NOT NULL,
                `receipt_type` VARCHAR(20) NOT NULL,
                `receipt_content_sum` DECIMAL(10,2) NOT NULL,
                `receipt_content_bill_id` VARCHAR(20),
                `receipt_content_description` VARCHAR(100),
                `receipt_content_date_paid` DATE NOT NULL,
                `receipt_content_payer_name` VARCHAR(200) NOT NULL,
                `receipt_content_payment_method` VARCHAR(20) NOT NULL,
                `card_no` VARCHAR(20),
                `cheque_number` VARCHAR(20),
                `receipt_responsible` VARCHAR(64) NOT NULL,
                `receipt_serial_number` VARCHAR(20),
                PRIMARY KEY (`receipt_uid`),
                FOREIGN KEY (rn) REFERENCES patient_admission(rn)
                /*FOREIGN KEY (receipt_responsible) REFERENCES */
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `bill_content_receipt` (
                `bill_content_receipt_uid` VARCHAR(64) NOT NULL,
                `bill_uid` VARCHAR(64) NOT NULL,
                `receipt_uid` VARCHAR(64) NOT NULL,
                PRIMARY KEY (`bill_content_receipt_uid`),
                FOREIGN KEY (bill_uid) REFERENCES bill(bill_uid),
                FOREIGN KEY (receipt_uid) REFERENCES receipt(receipt_uid)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `lookup_department` (
                `department_uid` VARCHAR(64) NOT NULL,
                `department_code` VARCHAR(20) UNIQUE NOT NULL,
                `department_name` VARCHAR(50) NOT NULL,
                `phone_number` VARCHAR(100),
                `address1` VARCHAR(100),
                `address2` VARCHAR(100),
                `address3` VARCHAR(100),
                PRIMARY KEY (`department_uid`)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `lookup_ward` (
                  `ward_uid` VARCHAR(64) NOT NULL,
                  `ward_code` VARCHAR(20) UNIQUE NOT NULL,
                  `ward_name` VARCHAR(50) NOT NULL,
                  `sex` VARCHAR(20),
                  `min_age` INT,
                  `max_age` INT,
                  PRIMARY KEY (`ward_uid`)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `lookup_status` (
                     `status_uid` VARCHAR(64) NOT NULL,
                     `status_code` VARCHAR(20) UNIQUE NOT NULL,
                     `status_description` VARCHAR(100) NOT NULL,
                     `class_1a_ward_cost` DECIMAL(10,2) NOT NULL,
                     `class_1b_ward_cost` DECIMAL(10,2) NOT NULL,
                     `class_1c_ward_cost` DECIMAL(10,2) NOT NULL,
                     `class_2_ward_cost` DECIMAL(10,2) NOT NULL,
                     `class_3_ward_cost` DECIMAL(10,2) NOT NULL,
                     PRIMARY KEY (`status_uid`)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `lookup_treatment` (
                `treatment_uid` VARCHAR(64) NOT NULL,
                `treatment_code` VARCHAR(20) UNIQUE NOT NULL,
                `treatment_name` VARCHAR(50) NOT NULL,
                `class_1_cost_per_unit` DECIMAL(10,2) NOT NULL,
                `class_2_cost_per_unit` DECIMAL(10,2) NOT NULL,
                `class_3_cost_per_unit` DECIMAL(10,2) NOT NULL,
                PRIMARY KEY (`treatment_uid`)
            );"
            ,
            "CREATE TABLE IF NOT EXISTS `lookup_general` (
                 `lookup_general_uid` VARCHAR(64) NOT NULL,
                 `code` VARCHAR(20) UNIQUE NOT NULL,
                 `category` VARCHAR(20) NOT NULL,
                 `name` VARCHAR(50) NOT NULL,
                 `long_description` VARCHAR(100) NOT NULL,
                 `recommend` BOOLEAN NOT NULL DEFAULT true,
                 PRIMARY KEY (`lookup_general_uid`)
            );"

         );
            
        for($i=0; $i < count($Tables); $i++)
        {
            $sqlCommand = Yii::$app->db->createCommand($Tables[$i]);
            $sqlCommand->execute();    
        }

        $model = new Patient_informationSearch();
        $modelNOK = new Patient_next_of_kin();

        if(Yii::$app->request->post('hasEditable')){
            $nok_uid = Yii::$app->request->post('editableKey');
            $model = Patient_next_of_kin::findOne($nok_uid);

            $out = Json::encode(['output'=>'','message'=>'']);
            $post = [];
            $posted = current($_POST['Patient_next_of_kin']);
            $post['Patient_next_of_kin'] = $posted;

            if($model->load($post)){
                $model -> save();
                $model = Patient_next_of_kin::findOne($nok_uid);
            }

            if(isset($posted['nok_name'])){
                $output = $model->nok_name;
            }

            if(isset($posted['nok_relationship'])){
                $output = $model->nok_relationship;
            }

            if(isset($posted['nok_phone_number'])){
                $output = $model->nok_phone_number;
            }

            if(isset($posted['nok_email'])){
                $output = $model->nok_email;
            }
            

            $out = Json::encode(['output'=>$output, 'message'=>'']);

            echo $out;
            return;
        }

        
        if ($this->request->isPost && $model->load($this->request->post()))
        {
            if($model->search($model->nric)) {
                return $this->render('/site/index', [
                 'model' => Patient_informationController::findModel_nric($model->nric)]);  
            }
        } else {
            $model->loadDefaultValues();
        }

        if ($this->request->isPost) {
            if ($modelNOK->load($this->request->post()) && $modelNOK->save()) {
                return $this->render('/site/index', [
                    'model' => Patient_informationController::findModel($modelNOK->patient_uid)]);  
            }
        } else {
            $model->loadDefaultValues();
        }

        // return $this->render('create', [
        //     'model' => $model,
        // ]);

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

