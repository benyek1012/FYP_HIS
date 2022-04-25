<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Patient_information;
use app\controllers\Patient_informationController;
use app\models\Patient_admission;
use app\models\Patient_next_of_kin;
use yii\helpers\Json;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

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
                        'actions' => ['logout', 'language'],
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
        // return [
        //     'error' => [
        //         'class' => 'yii\web\ErrorAction',
        //     ],
        //     'captcha' => [
        //         'class' => 'yii\captcha\CaptchaAction',
        //         'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
        //     ],
        // ];

        return ArrayHelper::merge(parent::actions(), [
            'nok' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => Patient_next_of_kin::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;                                   // your attribute value
                    // $value = $model->$attribute;                                   // your attribute value
                    // if ($attribute === 'nok_name') {                                // selective validation by attribute
                    //     return $value;                                             // return formatted value if desired
                    // } 
                    // elseif ($attribute === 'nok_relationship') {                   
                    //     return $value;                                             
                    // } 
                    // elseif ($attribute === 'nok_phone_number') {                   
                    //     return $value;                                             
                    // } 
                    // elseif ($attribute === 'nok_email') {                          
                    //     return $value;                                             
                    // }                
                    // return '';                                                     // empty is same as $value
                },                  
                // 'outputMessage' => function($model, $attribute, $key, $index) {
                //     return '';                                                    // any custom error after model save
                // },
            ]
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->InitSQL();

        $model_Patient = new Patient_information();
        $model_NOK = new Patient_next_of_kin();

        if ($this->request->isPost)
        {
            if($model_Patient->load($this->request->post())) $this->actionSidebar($model_Patient);
            else $model_Patient->loadDefaultValues();
            
            if ($model_NOK->load($this->request->post())) {
                 $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                $model_NOK->nok_datetime_updated = $date->format('Y-m-d H:i');
                $model_NOK->save();
                $this->actionNOK($model_NOK);
            }
            else $model_NOK->loadDefaultValues();
        }

        if(!empty(Yii::$app->request->get('type'))) Patient_admissionController::actionCreate();
        
        if (Yii::$app->user->isGuest)
            return $this->redirect('/site/login');
        else return $this->render('index');
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


    //Functions of search patient in sidebar
    public function actionSidebar($model)
    {
        $globalSearch = $model->nric;
        $model_admission_founded = Patient_admissionController::findModel($globalSearch);
        if(!empty( $model_admission_founded)){
            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
                'rn' => $model_admission_founded->rn));
            // return Yii::$app->getResponse()->redirect(array('/site/index', 
            // 'id' => $model_admission_founded->patient_uid));
        }
        else 
        {
            $model_founded = Patient_informationController::findModel_nric($globalSearch);
            if(!empty($model_founded))
                return Yii::$app->getResponse()->redirect(array('/site/index', 
                    'id' => $model_founded->patient_uid));
        }
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionNOK($modelNOK)
    {
        // if(Yii::$app->request->post('hasEditable')){
        //     $nok_uid = Yii::$app->request->post('editableKey');
        //     $model = Patient_next_of_kin::findOne($nok_uid);

        //     $out = Json::encode(['output'=>'','message'=>'']);
        //     $post = [];
        //     $posted = current($_POST['Patient_next_of_kin']);
        //     $post['Patient_next_of_kin'] = $posted;

        //     if($model->load($post)){
        //         $model -> save();
        //         $model = Patient_next_of_kin::findOne($nok_uid);
        //     }
            
        //     if(isset($posted['nok_name'])){
        //         $output = $model->nok_name;
        //     }

        //     if(isset($posted['nok_relationship'])){
        //         $output = $model->nok_relationship;
        //     }

        //     if(isset($posted['nok_phone_number'])){
        //         $output = $model->nok_phone_number;
        //     }

        //     if(isset($posted['nok_email'])){
        //         $output = $model->nok_email;
        //     }
            

        //     $out = Json::encode(['output'=>$output, 'message'=>'']);

        //     echo $out;
        //     return;
        // }

         //Fucntions of add NOK in sidebar
        if ($modelNOK->save()) {
            $model_founded = Patient_informationController::findModel($modelNOK->patient_uid);
            if(!empty($model_founded))
                return Yii::$app->getResponse()->redirect(array('/site/index', 
                    'id' => $model_founded->patient_uid));
        }
    }

    public function InitSQL(){
        $Tables = array(
            "CREATE TABLE IF NOT EXISTS `new_user` (
                `user_uid` VARCHAR(64) NOT NULL,
                `username` VARCHAR(100) NOT NULL,
                `user_password` VARCHAR(20) NOT NULL,
                `role` VARCHAR(20) NOT NULL,
                `retire` BOOLEAN DEFAULT false,
                `authKey` VARCHAR(45) NOT NULL,
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
                `race` VARCHAR(20),
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
                `nok_address1` VARCHAR(100),
                `nok_address2` VARCHAR(100),
                `nok_address3` VARCHAR(100),
                `nok_datetime_updated`  DATETIME NOT NULL,
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
                `type` VARCHAR(20),
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
                `bill_print_id` VARCHAR(20), 
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
                `ward_number_of_days` INT NOT NULL,
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
                `receipt_content_datetime_paid` DATETIME NOT NULL,
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
    }

    public function actionLanguage()
    {
        if(isset($_POST['lang'])){
            Yii::$app->language = $_POST['lang'];
            // $cookie = new yii\web\Cookie([
            //     'name' => 'lang',
            //     'value' => $_POST['lang']
            // ]);

            // Yii::$app->getResponse()->getCookies()->add($cookie);

            Yii::$app->session->set('language', $_POST['lang']);
        }
    }
}