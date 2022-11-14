<?php

namespace app\controllers;
require 'vendor/autoload.php';

use Yii;
use app\models\Bill;
use app\models\BillSearch;
use app\models\Cancellation;
use app\models\DateFormat;
use app\models\Fpp;
use app\models\Inpatient_treatment;
use app\models\Lookup_department;
use app\models\Lookup_fpp;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Ward;
use yii\base\Exception;
use app\models\Model;
use yii\helpers\Json;
use app\models\Treatment_details;
use app\models\Lookup_status;
use app\models\Lookup_ward;
use app\models\Lookup_treatment;
use app\models\Receipt;
use app\models\Patient_information;
use app\models\Patient_admission;
use app\models\Patient_next_of_kin;
use app\models\SerialNumber;
use GpsLab\Component\Base64UID\Base64UID;
use yii\helpers\ArrayHelper;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use yii\helpers\VarDumper;
use app\models\PrintForm;
use DateTime;

/**
 * BillController implements the CRUD actions for Bill model.
 */
class BillController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['GET'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Bill models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRefresh()
    {
        return SerialNumber::getSerialNumber("bill");
    }

    public function actionGet_printer_1()
    {
        return SerialNumber::getSerialNumber("bill");
    }

    public function actionGet_printer_2()
    {
        return SerialNumber::getSerialNumber("bill2");
    }

    public function actionStatus($status) {
        $model = Lookup_status::findOne( ['status_code' => $status]);
        echo Json::encode($model);
    }

    public function actionDepartment($department) {
        $model = Lookup_department::findOne( ['department_code' => $department]);
        echo Json::encode($model);
    }

    public function actionTreatment($treatment) {
        $model = Lookup_treatment::findOne( ['treatment_code' => $treatment]);
        echo Json::encode($model);
    }

    public function actionFpp($fpp) {
        $model = Lookup_fpp::findOne(['kod' => $fpp]);
        echo Json::encode($model);
    }

    public function actionWard($ward) {
        $model = Lookup_ward::findOne( ['ward_code' => $ward]);
        echo Json::encode($model);
    }

    public function actionWardRow($ward){
        for($i = 0; $i < $ward; $i++) {
            $modelWard[] = new Ward();
        }
        $modelWard[] = new Ward();

        echo Json::encode($modelWard);
    }

    public function actionTreatmentRow($treatment){
        for($i = 0; $i < $treatment; $i++) {
            $modelTreatment[] = new Treatment_details();
        }
        $modelTreatment[] = new Treatment_details();

        echo Json::encode($modelTreatment);
    }

    // Check Date Clashing
    public function actionDate($bill_uid){
        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(['ward_start_datetime' => SORT_ASC])->all(); 
         
        // if($modelWard != null){
        //     $modelDate = Ward::find()->where(['between', 'ward_start_datetime', $modelWard[0]->ward_start_datetime, $modelWard[0]->ward_end_datetime])->all();
        // }
        echo Json::encode($modelWard);
    }   
    
    // Check FPP Cost Range
    public function actionCost($bill_uid){
        $modelFPP = Fpp::find()->where(['bill_uid' => $bill_uid])->all(); 
         
        // if($modelWard != null){
        //     $modelDate = Ward::find()->where(['between', 'ward_start_datetime', $modelWard[0]->ward_start_datetime, $modelWard[0]->ward_end_datetime])->all();
        // }
        echo Json::encode($modelFPP);
    }  

    public function actionBillable_final_fee($bill_uid) {
        $cost = array();
        $cost['billAble'] = (new Bill()) -> calculateBillable($bill_uid);
        $cost['finalFee'] = (new Bill()) -> calculateFinalFee($bill_uid);
        echo Json::encode($cost);
    }

    /**
     * Displays a single Bill model.
     * @param string $bill_uid Bill Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($bill_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($bill_uid),
        ]);
    }

    /**
     * Creates a new Bill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Bill();

        $rowsWard = (new \yii\db\Query())
            ->select('ward_uid')
            ->from('ward')
            ->where(['bill_uid' => null])
            ->all();

        $rowsTreatment = (new \yii\db\Query())
            ->select('treatment_details_uid')
            ->from('treatment_details')
            ->where(['bill_uid' => null])
            ->all();

        $rowsFPP = (new \yii\db\Query())
            ->select('kod')
            ->from('fpp')
            ->where(['bill_uid' => null])
            ->all();

        if ($this->request->isPost) {
            // Inseart Bill
            if($model->load($this->request->post()) && $model->save()) {
                foreach($rowsWard as $rowWard){
                    $modelWard = $this->findModel_Ward($rowWard['ward_uid']);
                    $modelWard->bill_uid = $model->bill_uid;
                    $modelWard->save();
                }

                foreach($rowsTreatment as $rowTreatment){
                    $modelTreatment = $this->findModel_Treatment($rowTreatment['treatment_details_uid']);
                    $modelTreatment->bill_uid = $model->bill_uid;
                    $modelTreatment->save();
                }

                foreach($rowsFPP as $rowFPP){
                    $modelFPP = $this->findModel_FPP($rowFPP['kod']);
                    $modelFPP->bill_uid = $model->bill_uid;
                    $modelFPP->save();
                }

                $model_cancellation = Cancellation::findAll(['replacement_uid' => null]);
              
                if(!empty($model_cancellation)){
                    foreach($model_cancellation as $model_cancellation){
                        $model_bill_cancel = Bill::findOne(['bill_uid' => $model_cancellation->cancellation_uid]);
                        if(!empty($model_bill_cancel))
                            if($model_bill_cancel->rn == $model->rn){
                                $model_cancellation->replacement_uid = $model->bill_uid;
                                $model_cancellation->save();
                            }
                    }
                }
                
                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                    'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'ward'));
            }
        }
        return $this->render('create', [
            'model' => $model,
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
            'modelTreatment' =>(empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
            'modelFPP' =>(empty($modelFPP)) ? [new Fpp] : $modelFPP,
            'model_cancellation' => new Cancellation(),
            'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
        ]);
    }


    /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($bill_uid)
    {
        $model = $this->findModel($bill_uid);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->bill_uid = $bill_uid;
            $model->save();           
        }

        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        $wardClass = $model->class;

        foreach($modelTreatment as $modelTreatment){
            $modelLoopUpTreatment = Lookup_treatment::findOne( ['treatment_code' => $modelTreatment->treatment_code]);
            if($wardClass == '1a' || $wardClass == '1b' || $wardClass == '1c') {
                $modelTreatment->item_per_unit_cost_rm = $modelLoopUpTreatment->class_1_cost_per_unit;
            }
            if($wardClass == '2'){
                $modelTreatment->item_per_unit_cost_rm = $modelLoopUpTreatment->class_2_cost_per_unit;
            }
            if($wardClass == '3'){
                $modelTreatment->item_per_unit_cost_rm = $modelLoopUpTreatment->class_3_cost_per_unit;
            }

            $modelTreatment->item_total_unit_cost_rm = $modelTreatment->item_per_unit_cost_rm * $modelTreatment->item_count;

            $modelTreatment->save();
        }      
        
        // return Yii::$app->getResponse()->redirect(array('/bill/generate', 
        //     'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'bill'));

        return Yii::$app->getResponse()->redirect(array('/bill/generate', 
            'bill_uid' => $model->bill_uid, 'rn' => $model->rn));
    }

      /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGenerate($bill_uid)
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
            
        $model = $this->findModel($bill_uid);
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        $modelFPP = Fpp::findAll(['bill_uid' => $bill_uid]);
        $modelInpatient = Inpatient_treatment::findOne(['bill_uid' => $bill_uid]);
        
        // Update Bill
        if(Yii::$app->request->post('updateBill') == 'true') {
            $this->actionUpdate($bill_uid);
        }

        // Insert and Update Ward
        if(Yii::$app->request->post('saveWard') == 'true') {
            (new WardController(null, null))->actionUpdate();
        }

        // Add Ward Row
        if (Yii::$app->request->post('addWardRow') == 'true') {
            (new WardController(null, null))->actionWardrow();
        }

        // Remove Ward Row
        // if (Yii::$app->request->post('removeWardRow') == 'true') {
        //     // $modelWard = array_pop($modelWard);

        //     return $this->render('generate', [
        //         'model' => $model,
        //         'modelWard' => $modelWard,
        //         'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
        //     ]);
        // }

        // Insert and Update Treatment
        if(Yii::$app->request->post('saveTreatment') == 'true') {
            (new Treatment_detailsController(null, null))->actionUpdate();
        }

        // Add Treatment Row
        if (Yii::$app->request->post('addTreatmentRow') == 'true') {
            $dbTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);

            if(empty($dbTreatment)) {
                $count = count(Yii::$app->request->post('Treatment_details', []));
                for($i = 0; $i < $count; $i++) {
                    $modelTreatment[] = new Treatment_details();
                }
                $modelTreatment[] = new Treatment_details();
            }
            else {
                $modelTreatment = $dbTreatment;
                $count = count(Yii::$app->request->post('Treatment_details', [])) - count($dbTreatment);
                for($i = 0; $i < $count; $i++) {
                    $modelTreatment[] = new Treatment_details();
                }
                $modelTreatment[] = new Treatment_details();
            }

            return $this->render('generate', [
                'model' => $model,
                'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
                'modelTreatment' => $modelTreatment,
                'model_cancellation' => (empty($model_cancellation)) ? [new Cancellation] : $model_cancellation,
                'modelFPP' => (empty($modelFPP)) ? [new Fpp] : $modelFPP,
                'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
            ]);
            // (new Treatment_detailsController(null, null))->actionTreatmentrow();
        }

        // Remove Treatment Row
        // if (Yii::$app->request->post('removeTreatmentRow') == 'true') {
        //     // $modelTreatment = array_pop($modelTreatment);

        //     return $this->render('generate', [
        //         'model' => $model,
        //         'modelWard' => $modelWard,
        //         'modelTreatment' => $modelTreatment,
        //     ]);
        // }

        // Insert and Update Fpp
        if(Yii::$app->request->post('saveFpp') == 'true') {
            (new FppController(null, null))->actionUpdate();
        }

        // Add Fpp Row
        if (Yii::$app->request->post('addFppRow') == 'true') {
            $dbFpp = Fpp::findAll(['bill_uid' => $bill_uid]);

            if(empty($dbFpp)) {
                $count = count(Yii::$app->request->post('Fpp', []));
                for($i = 0; $i < $count; $i++) {
                    $modelFPP[] = new Fpp();
                }
                $modelFPP[] = new Fpp();
            }
            else {
                $modelFPP = $dbFpp;
                $count = count(Yii::$app->request->post('Fpp', [])) - count($dbFpp);
                for($i = 0; $i < $count; $i++) {
                    $modelFPP[] = new Fpp();
                }
                $modelFPP[] = new Fpp();
            }

            return $this->render('generate', [
                'model' => $model,
                'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
                'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
                'model_cancellation' => (empty($model_cancellation)) ? [new Cancellation] : $model_cancellation,
                'modelFPP' => $modelFPP,
                'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
            ]);
            // (new FppController(null, null))->actionFpprow();
        }

        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(['ward_start_datetime' => SORT_ASC])->all();   
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        $model_cancellation = new Cancellation();
        $modelFPP = Fpp::findAll(['bill_uid' => $bill_uid]);
        $modelFPP[] = new Fpp();
        $modelTreatment[] = new Treatment_details();
        $modelWard[] = new Ward();

        return $this->render('generate', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
            'modelFPP' => $modelFPP,
            'model_cancellation' => $model_cancellation,
            'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
        ]); 
    }

    public function actionGeneratebill($bill_uid)
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model = Bill::findOne(['bill_uid' => $bill_uid]);
        $modelWards = Ward::find(['bill_uid' => $bill_uid]);
        $error_generate = '';


        // // Popup Generation
        if(Yii::$app->request->get('confirm') != 'true'){
            Yii::$app->session->set('billable_sum', (new Bill()) -> calculateBillable(Yii::$app->request->get('bill_uid')));
            Yii::$app->session->set('final_fee', (new Bill()) -> calculateFinalFee(Yii::$app->request->get('bill_uid')));
            return false;
        }

        if (Yii::$app->request->get('confirm') == 'true'){
            $checkTreatment = (new Bill()) -> checkTreatmentPekeliling($bill_uid);
            $checkWard = (new Bill()) -> checkWardPekeliling($bill_uid);
            $checkFPP = (new Bill()) -> checkFppPekeliling($bill_uid);
            $checkDepartment = (new Bill()) -> checkDepartmentPekeliling($bill_uid);
            $checkStatus = (new Bill()) -> checkStatusPekeliling($bill_uid);

            if($checkTreatment && $checkWard && $checkFPP && $checkDepartment && $checkStatus){
                if(empty($model->bill_generation_datetime))
                {
                    $model->bill_generation_datetime =  $date->format('Y-m-d H:i:s');

                    if($modelWards->count() > 0)
                    {
                        $final_ward_datetime = Ward::find()->select('ward_end_datetime')->where(['bill_uid' => $bill_uid])
                        ->orderBy('ward_end_datetime DESC')->limit(1)->one();
                        // $model->final_ward_datetime =  date($final_ward_datetime["ward_end_datetime"]);
                        if(!empty($final_ward_datetime["ward_end_datetime"])){
                            $model->final_ward_datetime =  date($final_ward_datetime["ward_end_datetime"]);
                        }
                        else{
                            $model->final_ward_datetime =  $date->format('Y-m-d H:i:s');
                        }
                    }
                    else
                        $model->final_ward_datetime =  $date->format('Y-m-d H:i:s');
                }

                $model->bill_generation_billable_sum_rm = Yii::$app->session->get('billable_sum');
                $model->bill_generation_final_fee_rm = Yii::$app->session->get('final_fee');

                if(!empty(Yii::$app->request->get('discharge'))){
                    if(Yii::$app->request->get('discharge')){
                        $checkFormat = DateTime::createFromFormat('Y-m-d H:i', Yii::$app->request->get('discharge'));

                        if($checkFormat){
                            $validDate = DateFormat::convert(Yii::$app->request->get('discharge'), 'datetime');
                            if($validDate){
                                $model->discharge_date = Yii::$app->request->get('discharge') . ':00';
                            }
                            else{
                                Yii::$app->session->setFlash('error_generate', '
                                    <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                    <strong>'.Yii::t('app', 'Invalid Datetime Format!').' <br/></strong> 
                                    '.Yii::t('app', 'Invalid Datetime Format of Discharge Date').'</div>'
                                );

                                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));
                            }
                        }
                        else{
                            Yii::$app->session->setFlash('error_generate', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Invalid Datetime Format!').' <br/></strong> 
                                '.Yii::t('app', 'Invalid Datetime Format of Discharge Date').'</div>'
                            );

                            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));   
                        }
                    }
                }

                if (Yii::$app->session->has('billable_sum')) Yii::$app->session->remove('billable_sum');
                if (Yii::$app->session->has('final_fee')) Yii::$app->session->remove('final_fee');

                $model->bill_uid = Yii::$app->request->get('bill_uid');
                $model->generation_responsible_uid = Yii::$app->user->identity->getId();
                $model->save();

                return Yii::$app->getResponse()->redirect(array('/bill/print', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));    
            }
            else{
                if(!$checkTreatment){
                    $error_generate .= 'Treatment Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkWard){
                    $error_generate .= 'Ward Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkFPP){
                    $error_generate .= 'FPP Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkDepartment){
                    $error_generate .= 'Department Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkStatus){
                    $error_generate .= 'Status Pekeliling Kod no longer exist'."<br/>";
                }

                    Yii::$app->session->setFlash('error_generate', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>'.Yii::t('app', 'Generate error! ').' <br/></strong> 
                    '.Yii::t('app', $error_generate).'</div>'
                );

                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));   
            }
        }
    }

    
      /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPrint($bill_uid)
    {        
        $session = Yii::$app->session;
        
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model = $this->findModel($bill_uid);
		// $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(['ward_start_datetime' => SORT_ASC])->all();   
        $modelreceipt = Receipt::findAll(['rn'=> $model->rn, 'receipt_type' => 'deposit']);
        $modelrefund = Receipt::findAll(['rn'=> $model->rn, 'receipt_type' => 'refund']);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        $modelFPP = Fpp::findAll(['bill_uid' => $bill_uid]);
        $modelInpatient = Inpatient_treatment::findOne(['bill_uid' => $bill_uid]);
        
        if ($this->request->isPost && $model->load($this->request->post())) {
            if($model->validate() && $model->bill_print_id != "")
            {    
                // set bill printer session
                $data = Yii::$app->request->post();
                $choice = $data['Bill']['printer_choices'];
                $session->set('bill_printer_session', $choice);

				$error = PrintForm::printBill($bill_uid);
				#would have thrown exception by this point if there was any issue
                if(!empty($error))
                {
                    Yii::$app->session->setFlash('msg', '
                    <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
                }
                          
                if($choice == ""){
                    Yii::$app->session->setFlash('error_print', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Printer Choice Error!').' <br/></strong> 
                        '.Yii::t('app', "At least choice one printer to print").'</div>'
                    );
                }
                else{
                    if($choice == 'Printer 1')
                        $serial_no =  $this->actionGet_printer_1();
                    else if($choice == 'Printer 2')
                    $serial_no =  $this->actionGet_printer_2();

                    if($model->bill_print_id != $serial_no)
                    {
                        if($choice == 'Printer 1')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill"]);
                        else if($choice == 'Printer 2')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill2"]);

                        $str = $model->bill_print_id;
                        $only_integer = preg_replace('/[^0-9]/', '', $str);
                        $model_serial->prepend = preg_replace('/[^a-zA-Z]/', '', $str);
                        $model_serial->digit_length = strlen($only_integer);
                        $model_serial->running_value = $only_integer;

                        $model_serial->save();    
                    }
                    else{
                        if($choice == 'Printer 1')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill"]);
                        else if($choice == 'Printer 2')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill2"]);
                        $model_serial->running_value =  $model_serial->running_value + 1;
                        $model_serial->save();    
                    }
                    
                    $model->bill_print_datetime =  $date->format('Y-m-d H:i:s');
                    $model->bill_uid = Yii::$app->request->get('bill_uid');
                    $model->bill_print_responsible_uid = Yii::$app->user->identity->getId();
                    $model->save();
                    return Yii::$app->getResponse()->redirect(array('/bill/print', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));       
                }  
            }
            else
            {
                $message =  Yii::t('app','Bill Print ID should not be empty!');
                $model->addError('bill_print_id', $message);
                return $this->render('print', [
                    'model' => $model,
                    'modelWard' => $modelWard,
                    'modelTreatment' => $modelTreatment,
                    'print_empty' => true,
                    'model_cancellation' => new Cancellation(),
                    'modelFPP' => $modelFPP,
                    'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
                ]);
            }
        }
        else{
            if(!(new Bill()) -> isPrinted(Yii::$app->request->get('rn')))
            {
                if ($session->has('bill_printer_session')) {
                    $choice = $session->get('bill_printer_session');

                    if($choice == 'Printer 1')
                        $model->bill_print_id = $this->actionGet_printer_1();
                    else if($choice == 'Printer 2')
                        $model->bill_print_id = $this->actionGet_printer_2();
                }
            }
        }

        return $this->render('print', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
            'model_cancellation' => new Cancellation(),
            'modelFPP' => $modelFPP,
            'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
        ]);
    }

    /**
     * Deletes an existing Bill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $bill_uid Bill Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCancellation($bill_uid)
    {
        // $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        // $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);

        // foreach ($modelWard as $modelWard) {
        //     $modelWard->delete();
        // }

        // foreach ($modelTreatment as $modelTreatment) {
        //     $modelTreatment->delete();
        // }

        // $this->findModel($bill_uid)->delete();

        $model = $this->findModel($bill_uid);
        $model->deleted = 1;
        // echo '<pre>';
        // var_dump($model);
        // echo '</pre>';
        // exit;
        // $model->validate();
        // var_dump($model->errors);
        // exit;
        $model->save();

        $model_cancellation = new Cancellation();
        if($this->request->isPost && $model_cancellation->load($this->request->post())){
            if(empty($model_cancellation->deleted_datetime))
            {
                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                $model_cancellation->deleted_datetime =  $date->format('Y-m-d H:i:s');
            }

            $model_cancellation->cancellation_uid = $model->bill_uid;
            $model_cancellation->table = 'bill';
            $model_cancellation->responsible_uid = Yii::$app->user->identity->getId();

            if($model_cancellation->validate() && $model_cancellation->save()){
                return Yii::$app->getResponse()->redirect(array('/bill/create', 
                    'rn' => Yii::$app->request->get('rn'))); 
            }
        }
    }

    /**
     * Finds the Bill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $bill_uid Bill Uid
     * @return Bill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($bill_uid)
    {
        if (($model = Bill::findOne(['bill_uid' => $bill_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function findModel_Rn($rn)
    {
        if (($model = Bill::findOne(['rn' => $rn])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel_Ward($ward_uid)
    {
        if (($model = Ward::findOne(['ward_uid' => $ward_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel_Treatment($treatment_details_uid)
    {
        if (($model = Treatment_details::findOne(['treatment_details_uid' => $treatment_details_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel_FPP($kod)
    {
        if (($model = Fpp::findOne(['kod' => $kod])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

                                                    // $connector = new WindowsPrintConnector("smb://JOSH2-LAPTOP/EPSON");

                                                        //                 $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                                                                //                 $printer = new Printer($connector);
                                                
                                                            //                 $printer -> text("\n\n\x20\n\x20\n\x20\n\x20\n\n\n"); // \n = 0.4cm
                                                            //$printer -> text($billdate);
                                                            //                 $printer -> text(date("d/m/Y" , strtotime($model->bill_generation_datetime)) . "\n");
                                                            //                 //$printer -> text(date("d/m/Y" , strtotime($printbilldate)) . "\n");
                                                            //                 $printer -> text($billname);
                                                            //                 $printer -> text($model->rn."\n"); // receipt number
                                                            //                 $printer -> text($billname);
                                                            //                 $printer -> text(strtoupper($modelpatient->name). "\n"); // patientname
                                                            //   $printer -> text($billadd1);
                                                            //   $printer -> text(strtoupper($modelpatient->address1)."\n"); // date
                                                            //   $printer -> text($billadd2);
                                                            //   $printer -> text(strtoupper($modelpatient->address2)."\n"); //r/n
                                                            //   $printer -> text($billadd3);
                                                            //   $printer -> text(strtoupper($modelpatient->address3)); // time
                                                                //   $printer -> text($blankmiddle);
                                                                //   $printer -> text($billmasuk);
                                                                //   $printer -> text("Caj Duduk Wad  (Tarikh Masuk  : ".date("d/m/Y" , strtotime($printstartdate))." )"."\n"); //tarikh masuk
                                                                //   $printer -> text($billkeluar);
                                                                //   $printer -> text("(Tarikh Keluar : ".date("d/m/Y" , strtotime($printlastenddate))." )"."\n\n\n"); // tarikh keluar
                                                                //   $printer -> text($billmasuk);
                                                                //   $printer -> text("Kelas  ".$modelwardfind->ward_code." :"."  ".$modelwardfind->ward_number_of_days." Hari"); // class and day 
                                                                //   $printer -> text($billkelas);
                                                                //   $printer -> text($model->daily_ward_cost.str_repeat("\x20", 9- strlen($model->daily_ward_cost)). Bill::getTotalWardCost(Yii::$app->request->get('bill_uid')) ."\n\n"); // kelas price x total days in ward, hvnt add in total day ward cose
                                                                
                                                                //   $printer -> text($fixfront);
                                                                //   $printer -> text("Caj Pemeriksaan/Ujian Makmal"."\n"); // 
                                                                //   $printer -> text($fixfront);
                                                                //   $printer -> text("-----------------------------"."\n");
                                                                //   /*
                                                                //   $printer -> text($billkumpulan);

                                                                //   $printer -> text("1G"."    "."UJIAN MAKMAL KUMPULAN G"."        x"."  "."64"."           "."128.00\n");
                                                                //   $printer -> text($billkumpulan);
                                                                //   $printer -> text("1D"."    "."UJIAN MAKMAL KUMPULAN D"."        x"."  "."1"."             "."10.00\n");
                                                                //   $printer -> text($billkumpulan);
                                                                //   $printer -> text("15D"."    "."PEMBEDAHAN D"."                  x"."  "."1"."             "."20.00\n\n");
                                                                //   */
                                                                //   //$printer -> text("1G"."    ".mb_strimwidth($description,0, 30) ."      x"."  "."days".""); // need restrict length, might need loop

                                                                //   $printer -> text($treatmentitem);
                                                                //   if($ncounter==4 && $ct >= 4)
                                                                //   {
                                                                // $printer -> text(str_repeat("\x20", 6). "...."."\n");
                                                                // $printer->text(str_repeat("\n", 4 - $ncounter));
                                                                //   }
                                                                //   if($ncounter==5 && $ct >= 3)
                                                                //   {
                                                                // $printer -> text(str_repeat("\x20", 6). "...."."\n");
                                                                // $printer->text(str_repeat("\n", 5 - $ncounter));
                                                                //   }
                                                                //   if($ncounter==5)
                                                                //   {
                                                                // //$printer -> text(str_repeat("\x20", 6). "...."."\n");
                                                                // $printer->text(str_repeat("\n", 5 - $ncounter)); // originally ncounter >= 4, "\n", 5-1-ncounter
                                                                //   }
                                                                //   if($ncounter==6 && $ct >= 2)
                                                                //   {
                                                                // $printer -> text(str_repeat("\x20", 6). "...."."\n");
                                                                // $printer->text(str_repeat("\n", 6 - $ncounter));
                                                                //   }
                                                                //   if($ncounter==6)
                                                                //   {
                                                                // //$printer -> text(str_repeat("\x20", 6). "...."."\n");
                                                                // $printer->text(str_repeat("\n", 6 - $ncounter));
                                                                //   }
                                                                //   if($ncounter==7 && $ct >= 1)
                                                                //   {
                                                                // $printer -> text(str_repeat("\x20", 6). "...."."\n");
                                                                // $printer->text(str_repeat("\n", 7 - $ncounter));
                                                                //   if($ncounter==7)
                                                                //   {
                                                                // //$printer -> text(str_repeat("\x20", 6). "...."."\n");
                                                                // $printer->text(str_repeat("\n", 7 - $ncounter));
                                                                //   }
                                                                //   }
                                                                //   if($ncounter<4)
                                                                //   {
                                                                //     $printer->text(str_repeat("\n", 5 - $ncounter));
                                                                //   }
                                                                
                                                                //   //$printer -> text("\n\n"); // if fix breaktop cant get value from ncounter, change to 2. thn remove \n\n at the back
                                                                //   $printer -> text($fixfront);
                                                                //   $printer -> text("Caj Rawatan Harian"."\n");
                                                                //   $printer -> text($fixfront);
                                                                //   $printer -> text("------------------");
                                                                //   $printer -> text(str_repeat("\x20" , 46)."  "."\n");
                                                                //   $printer -> text(str_repeat("\x20" , 67)."----------\n");
                                                                //   $printer -> text(str_repeat("\x20" , 68).$model->bill_generation_billable_sum_rm."\n");
                                                                //   $printer -> text($cagaranitem);
                                                                //  // if($rcounter>3)
                                                                // //  {
                                                                //   //    $printer ->text($fixfront."...."."\n");
                                                                //   //   $printer -> text(str_repeat("\n", 8-1 - $rcounter)); // else 8-rcounter
                                                                //  // }
                                                                //   if ($ct==0)
                                                                //  //else 
                                                                //   {
                                                                //     $printer -> text(str_repeat("\n", 8 - $rcounter));
                                                                //   }
                                                                //   if ($ct==1)
                                                                //  //else 
                                                                //   {
                                                                //     $printer -> text(str_repeat("\n", 7 - $rcounter));
                                                                //   }
                                                                //   if ($ct==2)
                                                                //  //else 
                                                                //   {
                                                                //     $printer -> text(str_repeat("\n",  6- $rcounter));
                                                                //   }
                                                                //   if ($ct==3)
                                                                //  //else 
                                                                //   {
                                                                //     $printer -> text(str_repeat("\n", 6 - $rcounter));
                                                                //   }
                                                                
                                                                //   if ($ct==4)
                                                                //   //else 
                                                                //    {
                                                                //     $printer ->text($fixfront."...."."\n");
                                                                //      $printer -> text(str_repeat("\n", 6 - $rcounter));
                                                                //    }

                                                                //   $printer -> text(str_repeat("\x20" , 28)."JUMLAH YANG PERLU DIBAYAR ==>");
                                                                //   $printer -> text(str_repeat("\x20" , 11).$model->bill_generation_final_fee_rm);
                                                                //   $printer -> text("\n\n\n\n\n");

                                                                //   $printer ->close();
                                                               