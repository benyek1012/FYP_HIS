<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Patient_information;
use app\controllers\Patient_informationController;
use app\models\AdjustPrint;
use app\models\Patient_next_of_kin;
use Exception;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Json;
use app\models\Patient_admission;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\New_user;
use app\models\PrintForm;
use app\models\Bill;
use app\models\BillForgive;
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
                        'actions' => ['logout', 'language'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
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
                    $date = new \DateTime();
                    $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                    $model->nok_datetime_updated = $date->format('Y-m-d H:i:s');
                    $model->save();
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
        if (Yii::$app->user->isGuest)
            $this->redirect(Url::to(['/site/login']));
        else 
        {
            $this->redirect(Url::to(['/site/admission']));

            // if((new New_user())->isAdmin())
            //     return $this->render('admin_dashboard');
            // else if((new New_user())->isCashier())
            //     $this->redirect(Url::to(['/site/admission']));
            // else if((new New_user())->isClerk())
            //     return $this->render('clerk_dashboard');
            // else if((new New_user())->isGuestPrinter())
            //     $this->redirect(Url::to(['/site/guest_printer_dashboard']));
        }
    }

    /**
     * Displays admission page
     *
     * @return string
     */
    public function actionBatch_entry()
    {        
        $model = new Patient_admission();
        $flag = 0;
        if ($this->request->isPost){
            if ($model->load($this->request->post())){


                //check empty
                if($model->startrn != "" && $model->endrn != ""){
                    // var_dump(substr($model->startrn,-7,1));
                    // exit;
                    //check rn format
                    if((substr($model->startrn,-7,1) == '/' && substr($model->endrn,-7,1)  == '/')
                        && (substr($model->startrn,-6,1) == '0' ||  substr($model->startrn,-6,1) == '9') &&
                        (substr($model->endrn,-6,1) == '0' ||  substr($model->endrn,-6,1) == '9')){
                                //check format same
                                if(substr($model->startrn, 0, 6) == substr($model->endrn, 0, 6)){
                                    //convert to integer
                                    $startrn  = preg_replace('/\D/', '', $model->startrn);
                                    $endrn  = preg_replace('/\D/', '', $model->endrn);
                                    if($endrn >= $startrn){
										
										$connection = \Yii::$app->db;
										$transaction = $connection->beginTransaction();;
										try {
											for($i = $startrn; $i <= $endrn; $i++){
												$rn = substr($i, 0, 4)."/". substr($i, 4,9);
												// var_dump($rn);
												// exit;
												

												
												
												
												
												$model_admission = Patient_admission::findOne(['rn' => $rn]);
												if(empty($model_admission)){
													$flag = 1;
												   // print_r($rn);
												   
													$model_patient = new Patient_information();
													$model_patient->patient_uid = Base64UID::generate(32);
													$model_patient->nric = "";
													$model_patient->first_reg_date = date("Y-m-d");
													$model_patient->nationality = "";
													$model_patient->name = NULL;
													$model_patient->sex = "";
													$model_patient->race = "";
													$model_patient->phone_number = "";
													$model_patient->email = "";
													$model_patient->address1 = "";
													$model_patient->address2 = "";
													$model_patient->address3 = "";
													$model_patient->job = "";
													$model_patient->DOB = "";
													$model_patient->save();
												   
												   
													$admission = new Patient_admission();
													$date = new \DateTime();
													$date->setTimezone(new \DateTimeZone('+0800')); //GMT
													$admission->rn = $rn;
													$admission->patient_uid = $model_patient->patient_uid;
													$admission->entry_datetime = $date->format('Y-m-d H:i:s');
													if(substr($rn,-6,1) == '0')     $admission->type = "Normal";
													else if(substr($rn,-6,1) == '9') $admission->type = "Labor";
							
													$admission->initial_ward_class = "UNKNOWN";
													$admission->initial_ward_code = "UNKNOWN";
													$admission->save();
													// $model->validate();
													// var_dump($model->errors);
													// exit;
												}
												else {
													$flag = 0;
													break;
												}
											}
											if($flag = 1)
												$transaction->commit();
										} catch (\Exception $e) {
											$transaction->rollBack();
											return $e->getMessage();
											//var_dump( $e);
											return;
										} catch (\Throwable $e) {
											$transaction->rollBack();
											return $e->getMessage();
											//var_dump( $e);
											return;
										}
										
										
										
                                    }
                                    else{
                                        $flag = 1;
                                        Yii::$app->session->setFlash('msg', '
                                        <div class="alert alert-danger alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <strong>'.Yii::t('app', 'Validation error! ').' </strong>'
                                            .Yii::t('app', ' Start RN must be greater than end RN').' !</div>'
                                        );
                                    }
                                }
                                else{
                                    $flag = 1;
                                    Yii::$app->session->setFlash('msg', '
                                    <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                    <strong>'.Yii::t('app', 'Validation error! ').' </strong>'
                                        .Yii::t('app', ' This rn format must be same').' !</div>');
                                }
                        
                    }
                    else{
                        $flag = 1;
                        Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Validation error! ').' </strong>'
                            .Yii::t('app', ' This rn format is wrong').' !</div>');
                    }
                }
                //empty
                else{
                    $flag = 1;
                    Yii::$app->session->setFlash('msg', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>'.Yii::t('app', 'Validation error! ').' </strong>'
                        .Yii::t('app', ' This range cannot be empty').' !</div>');
                }
                
                if($flag == 0)
                Yii::$app->session->setFlash('msg', '
                <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                <strong>'.Yii::t('app', 'Duplicate error! ').' </strong>'
                    .Yii::t('app', ' This range is already existed').' !</div>');
                // exit;
            }
        }
        return $this->render('batch_entry', [
            'model' => $model,
        ]);
    } 
    public function actionAdmission()
    {
     //   $this->InitSQL();

        $model_Patient = new Patient_information();
        $model_NOK = new Patient_next_of_kin();

        if ($this->request->isPost)
        {
            // if($model_Patient->load($this->request->post())) $this->actionSidebar($model_Patient);
            // else $model_Patient->loadDefaultValues();
            
            try{
                if ($model_NOK->load($this->request->post())) {
                    $date = new \DateTime();
                    $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                    $model_NOK->nok_datetime_updated = $date->format('Y-m-d H:i');
                    $model_NOK->save();
                    $this->actionNOK($model_NOK);
                }
                else $model_NOK->loadDefaultValues();
            }
            catch(Exception $e){
                $this->errorMessage($e->getMessage());
            }
        }

        // if(!empty(Yii::$app->request->get('type'))) (new Patient_admissionController(null, null)) -> actionCreate();
        
        return $this->render('admission');
    }

    public function actionGuest_printer_dashboard()
    {
     //   $this->InitSQL();

        $model_Patient = new Patient_information();
        $model_NOK = new Patient_next_of_kin();

        if ($this->request->isPost)
        {
            // if($model_Patient->load($this->request->post())) $this->actionSidebar($model_Patient);
            // else $model_Patient->loadDefaultValues();
            
            try{
                if ($model_NOK->load($this->request->post())) {
                    $date = new \DateTime();
                    $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                    $model_NOK->nok_datetime_updated = $date->format('Y-m-d H:i');
                    $model_NOK->save();
                    $this->actionNOK($model_NOK);
                }
                else $model_NOK->loadDefaultValues();
            }
            catch(Exception $e){
                $this->errorMessage($e->getMessage());
            }
        }

        // if(!empty(Yii::$app->request->get('type'))) (new Patient_admissionController(null, null)) -> actionCreate();
        
        return $this->render('guest_printer_dashboard');
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
        if ($model->load(Yii::$app->request->post()) && $model->login($model)) {
            $this->redirect (Url::to(['/site/index']));
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
        
        $cookies = Yii::$app->response->cookies;
        $cookies->remove('cookie_login');
        //unset($cookies['username']);

        return $this->goHome();
    }


    //Functions of search patient in sidebar
    public function actionSidebar()
    {
        // $globalSearch = $model->nric;
        $globalSearch = Yii::$app->request->get('search');
        $model_admission_founded = (new Patient_admissionController(null, null)) -> findModel($globalSearch);

        if($globalSearch == Yii::$app->params['other_payment_rn'])
        {
            if(!empty( $model_admission_founded)){
                echo Json::encode($model_admission_founded);
            }
            else{
                return 'No';
            }
        }

        if((new Patient_admission()) -> checkRnFormat($globalSearch) == true){
            // RN is founded
            if(!empty( $model_admission_founded)){
                echo Json::encode($model_admission_founded);
            }
            else{
                return 'No';
            }
        }
        else 
        {
            // IC is founded
            $model_founded = (new Patient_informationController(null, null)) -> findModel_nric($globalSearch);
            if(!empty($model_founded))
                echo Json::encode($model_founded);
                // return Yii::$app->getResponse()->redirect(array('/site/admission', 
                //     'id' => $model_founded->patient_uid));
            else
                return false;
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
            $model_founded = (new Patient_informationController(null, null)) -> findModel($modelNOK->patient_uid);
            if(!empty($model_founded))
                return Yii::$app->getResponse()->redirect(array('/site/admission', 
                    'id' => $model_founded->patient_uid, '#' => 'nok'));        
        }
    }

    public function actionLanguage()
    {
        if(isset($_POST['lang'])){
            Yii::$app->language = $_POST['lang'];
            Yii::$app->session->set('language', $_POST['lang']);
        }
    }

    public function errorMessage($message){
        echo '<script>alert('.$message.')</script>';
    }
    
    public function actionNo_access()
    {
        return $this->render('no_access');
    }

    // Return true = controller ID equels url
    public function accessControl(){
        $flag = false;
        $actions_sidebar = array("patient_admission", "bill", "receipt");
        foreach ($actions_sidebar as $action) {
            if(Yii::$app->controller->id == "site" && (Yii::$app->controller->action->id == "admission" || Yii::$app->controller->action->id == "guest_printer_dashboard"))
            {
                $flag = true;
                break;
            }
            if(Yii::$app->controller->id == $action)
            {
                $flag = true;
                break;
            }
        }
        if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn'])
            $flag = false;
        return $flag;
    }

    // public function actionAdjust_print()
    // {
    //     if ($this->request->isPost) {
    //         PrintForm::printTest();
    //     } 

    //     return $this->render('print');
    // }

    public function actionForgive_bill()
    {
        $model = new Bill();

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model_forgive = BillForgive::find()->where(['bill_forgive_date' => $date->format('Y-m-d')])->one();
        if(empty($model_forgive))
        {
            $model_forgive = new BillForgive();
            $model_forgive->bill_forgive_uid = Base64UID::generate(32);
            $model_forgive->bill_forgive_date = $date->format('Y-m-d');
        }

        if ($this->request->isPost && $model_forgive->load($this->request->post())) { 
            if($model_forgive->validate())
            {
                $action=Yii::$app->request->post('action');
                $selection=(array)Yii::$app->request->post('selection');
                foreach($selection as $rn){
                    // update bill_forgive_date
                    $model = Bill::findone(['rn' => $rn]);
                    $date = new \DateTime();
                    $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                    $model->bill_forgive_date = $date->format('Y-m-d H:i:s');
                    $model->save();
                }
                if(!empty($selection))
                    $model_forgive->save();
                else
                    Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Bill Forgive Execution Failed!').'  </strong>'.
                            Yii::t('app', 'No items selected!').'</div>'
                    );
            }
        }
        return $this->render('forgive_bill',['model' => $model, 'model_forgive' => $model_forgive]);
    }

    public function actionRender_gridview()
    {

        // $query = Bill::find()->select('date(bill_forgive_date) as bill_forgive_date')   
        //   ->where(['<>', 'bill_forgive_date', date(Yii::$app->request->get('id'))]);

        $query =  Patient_admission::find()
            ->select('patient_admission.*, patient_information.*, bill.*, date(bill.bill_forgive_date) as bill_forgive_date')
            ->from('patient_admission')->joinWith('bill',true)->joinWith('receipt',true)
            ->joinWith('patient_information',true)
            ->where(['>=', 'bill_forgive_date', date(Yii::$app->request->get('id'))]);
        
        $dataProvider = new ActiveDataProvider([
            'query'=> $query,
            // ->joinWith('bill',true)
            'pagination'=>['pageSize'=>5],
        ]);
        
        return $this->renderPartial('/site/forgive_bill_gridview', ['dataProvider'=>$dataProvider, 'check' => 'false']);   
    }
}