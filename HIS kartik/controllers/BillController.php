<?php

namespace app\controllers;
require 'vendor/autoload.php';

use Yii;
use app\models\Bill;
use app\models\BillSearch;
use app\models\Lookup_department;
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
use GpsLab\Component\Base64UID\Base64UID;
use yii\helpers\ArrayHelper;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use yii\helpers\VarDumper;
use app\models\PrintForm;
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
                
                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                    'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'ward'));
            }
        }
        return $this->render('create', [
            'model' => $model,
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
            'modelTreatment' =>(empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
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
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        

        if ($this->request->isPost && $model->load($this->request->post())) {
            foreach($modelWard as $w)
                $w->save();
            foreach($modelTreatment as $t)
                $t->save();

            $model->bill_uid = Yii::$app->request->get('bill_uid');
            $model->save();
            return Yii::$app->getResponse()->redirect(array('/bill/update', 
            'bill_uid' => $bill_uid, 'rn' => $model->rn));     
        }

        return $this->render('update', [
            'model' => $model,
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
            'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details()] : $modelTreatment,
        ]);
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

        // Post method from form
        if ($this->request->isPost && $model->load($this->request->post()) && Yii::$app->request->post('generate') == 'true') {
            Yii::$app->session->set('billable_sum', $model->bill_generation_billable_sum_rm);
            Yii::$app->session->set('final_fee', $model->bill_generation_final_fee_rm);
            
            // Popup Generation
            if(Yii::$app->request->get('confirm') != 'true'){
                echo '<script type="text/javascript">',
                    'setTimeout(function(){',
                        'confirmAction();',
                        '},200);',
                '</script>';
            }
        }   

        if (Yii::$app->request->get('confirm') == 'true'){
            if(empty($model->bill_generation_datetime))
            {
                $model->bill_generation_datetime =  $date->format('Y-m-d H:i');
            }
            // var_dump(Yii::$app->session->get('billable_sum'));
            // exit();
            $model->bill_generation_billable_sum_rm = Yii::$app->session->get('billable_sum');
            $model->bill_generation_final_fee_rm = Yii::$app->session->get('final_fee');

            if (Yii::$app->session->has('billable_sum')) Yii::$app->session->remove('billable_sum');
            if (Yii::$app->session->has('final_fee')) Yii::$app->session->remove('final_fee');

            $cookies = Yii::$app->request->cookies;
            $model->generation_responsible_uid = $cookies->getValue('cookie_login');
            $model->bill_uid = Yii::$app->request->get('bill_uid');
            $model->save();

            return Yii::$app->getResponse()->redirect(array('/bill/print', 
                'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));        
        }
        // else{
        //     if (Yii::$app->session->has('billable_sum')) Yii::$app->session->remove('billable_sum');
        //     if (Yii::$app->session->has('final_fee')) Yii::$app->session->remove('final_fee');
        // }
        
        // Update Bill
        if(Yii::$app->request->post('updateBill') == 'true') {
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

                $modelTreatment->save();
            }            
            
            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'bill'));
        }

        // Insert and Update Ward
        if(Yii::$app->request->post('saveWard') == 'true' && Yii::$app->request->post('Ward', [])) {
            $dbWard = Ward::findAll(['bill_uid' => $bill_uid]);   
            $modelWard = Model::createMultiple(Ward::className());

            if(empty($dbWard)) {
                if( Model::loadMultiple($modelWard, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelWard);
                    
                    if($valid) {                    
                        foreach ($modelWard as $modelWard) {
                            $modelWard->ward_uid = Base64UID::generate(32);
                            $modelWard->bill_uid = $bill_uid;
                            $modelWard->save();
                        }
                    }
                }
            }
            else {
                $countWard = count(Yii::$app->request->post('Ward', []));
                $countdb = count($dbWard);

                if( Model::loadMultiple($modelWard, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelWard);
                    
                    if($valid) {         
                        if($countWard > $countdb){
                            for($i = $countWard; $i > $countdb; $i--) {
                                $modelWard[$i - 1]->ward_uid = Base64UID::generate(32);
                                $modelWard[$i - 1]->bill_uid = $bill_uid;
                                $modelWard[$i - 1]->save();
                            }
                        }   
                        else if($countWard == $countdb){   
                            $modelWardUpdate = Ward::findAll(['bill_uid' => $bill_uid]); 
                            if( Model::loadMultiple($modelWardUpdate, Yii::$app->request->post())) {
                                $valid = Model::validateMultiple($modelWardUpdate);
                                
                                if($valid) {            
                                    foreach ($modelWardUpdate as $modelWardUpdate) {
                                        $modelWardUpdate->save();
                                    }
                                }
                            }
                        }
                    }
                }
            } 
            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'ward'));
        }

        // Insert and Update Treatment
        if(Yii::$app->request->post('saveTreatment') == 'true' && Yii::$app->request->post('Treatment_details', [])) {
            $dbTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);   
            $modelTreatment = Model::createMultiple(Treatment_details::className());

            if(empty($dbTreatment)) {
                if( Model::loadMultiple($modelTreatment, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelTreatment);
                    
                    if($valid) {                    
                        foreach ($modelTreatment as $modelTreatment) {
                            $modelTreatment->treatment_details_uid = Base64UID::generate(32);
                            $modelTreatment->bill_uid = $bill_uid;
                            $modelTreatment->save();
                        }
                    }
                }
            }
            else {
                $countTreatment = count(Yii::$app->request->post('Treatment_details', []));
                $countdb = count($dbTreatment);

                if( Model::loadMultiple($modelTreatment, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelTreatment);
                    
                    if($valid) {    
                        if($countTreatment > $countdb){                
                            for($i = $countTreatment; $i > $countdb; $i--) {
                                $modelTreatment[$i - 1]->treatment_details_uid = Base64UID::generate(32);
                                $modelTreatment[$i - 1]->bill_uid = $bill_uid;
                                $modelTreatment[$i - 1]->save();
                            }
                        }
                        else if($countTreatment == $countdb){
                            $modelTreatmentUpdate = Treatment_details::findAll(['bill_uid' => $bill_uid]); 
                
                            if( Model::loadMultiple($modelTreatmentUpdate, Yii::$app->request->post())) {
                                $valid = Model::validateMultiple($modelTreatmentUpdate);
                                
                                if($valid) {                    
                                    foreach ($modelTreatmentUpdate as $modelTreatmentUpdate) {
                                        $modelTreatmentUpdate->save();
                                    }
                                }
                            }
                        }
                    }
                }
            } 
            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'treatment'));
        }

        // Add Ward Row
        if (Yii::$app->request->post('addWardRow') == 'true') {
            $dbWard = Ward::findAll(['bill_uid' => $bill_uid]);   

            if(empty($dbWard)) {
                $countWard = count(Yii::$app->request->post('Ward', []));
                for($i = 0; $i < $countWard; $i++) {
                    $modelWard[] = new Ward();
                }
                $modelWard[] = new Ward();
            }
            else {
                $modelWard = $dbWard;
                $countWard = count(Yii::$app->request->post('Ward', [])) - count($dbWard);
                for($i = 0; $i < $countWard; $i++) {
                    $modelWard[] = new Ward();
                }
                $modelWard[] = new Ward();
            }   

            return $this->render('generate', [
                'model' => $model,
                'modelWard' => $modelWard,
                'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
            ]);
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
                'modelTreatment' => $modelTreatment
            ]);
        }

        // Remove Ward Row
        if (Yii::$app->request->post('removeWardRow') == 'true') {
            // $modelWard = array_pop($modelWard);

            return $this->render('generate', [
                'model' => $model,
                'modelWard' => $modelWard,
                'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
            ]);
        }

        // Remove Treatment Row
        if (Yii::$app->request->post('removeTreatmentRow') == 'true') {
            // $modelTreatment = array_pop($modelTreatment);

            return $this->render('generate', [
                'model' => $model,
                'modelWard' => $modelWard,
                'modelTreatment' => $modelTreatment,
            ]);
        }

        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);   
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);

        return $this->render('generate', [
            'model' => $model,
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
            'modelTreatment' =>(empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
        ]);
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
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
            
        $model = $this->findModel($bill_uid);
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelreceipt = Receipt::findAll(['rn'=> $model->rn, 'receipt_type' => 'deposit']);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        $modeladmission = Patient_admission::findOne(['rn' => $model->rn]);
        $modelpatient = Patient_information::findOne(['patient_uid' => $modeladmission->patient_uid]);
        $cagaranitem ='';
        $treatmentitem = '';
        $ncounter = 1;
        $rcounter = 1;
        $billkumpulan = str_repeat("\x20", 6);
        $fixfront = str_repeat("\x20", 8);
        $ct = 1;
        $abc = '';

        $form = new PrintForm(PrintForm::BorangDaftarMasuk);
        $totalLine = 10;
        $totalCostTreatment = 0;
        $totalCostReceipt = 0;
        $countTreatment = count($modelTreatment);
        $countReceipt = count($modelreceipt);
        $lineleft = $totalLine - ($countTreatment + $countReceipt);

        // if total treatment and total receipt > total line(10)
        if($countTreatment + $countReceipt > $totalLine){
            foreach ($modelTreatment as $index => $modeltreatmentfind) {
                // if treatment > 9, calculate total unit cost in "...."
                if($index > 8){
                    $totalCostTreatment += $modeltreatmentfind->item_total_unit_cost_rm;
                }
                // if treatment < 9, directly print out
                else{
                    // $form->printElementArray(
                    //     [
                    //         [6, "\x20"],
                    //         [5, $modeltreatmentfind->treatment_code, true],
                    //         [1,"\x20"],
                    //         [30, $modeltreatmentfind->treatment_name,true],
                    //         [2,"\x20"],
                    //         [1,"x"],
                    //         [2,"\x20"],
                    //         [5,$modeltreatmentfind->item_count],
                    //         [7,"\x20"],
                    //         [8, $modeltreatmentfind->item_per_unit_cost_rm],
                    //         [8, $modeltreatmentfind->item_total_unit_cost_rm],
    
                    //     ]
                    // );
                    // $form->printNewLine(1);
                    $form -> printBillTreatment($bill_uid, $modeltreatmentfind->treatment_code, $modeltreatmentfind->treatment_name, $modeltreatmentfind->item_count, $modeltreatmentfind->item_per_unit_cost_rm,  $modeltreatmentfind->item_total_unit_cost_rm);
                }
            }
    
            // if treatment > 9, print "...."
            if($index > 8){
                // $form->printElementArray(
                //     [
                //         [6, "\x20"],
                //         [4, "...."],
                //         [6, "\x20"],
                //         [9, number_format((float)$totalCostTreatment, 2, '.', '')],
                //     ]
                // );
                $form -> printMoreTreatment($totalCostTreatment);
            }

            // caj rawatan harian
            // $form->printElementArray(
            //     [
            //         [6, "\x20"],
            //         [4, "...."],
            //         [6, "\x20"],
            //         [9, number_format((float)$totalCostTreatment, 2, '.', '')],
            //     ]
            // );
    
            foreach ($modelreceipt as $index => $modelreceiptfind)
            {
                // if total receipt > total treatment
                if($countReceipt > $countTreatment){
                    // total receipt + total treatment - total line, get different between total receipt and total treatment with total line
                    $count1 = ($countReceipt + $countTreatment) - $totalLine;
                    // get deposit print how many line and another print in "...."
                    $count2 = $countReceipt - ($count1 + 2);
                    // if treatment > count2, calculate total deposit in "...."
                    if($index > $count2){
                        $totalCostReceipt += $modelreceiptfind->receipt_content_sum;
                    }
                    // directly print out deposit
                    else{
                        // $form->printElementArray(
                        //     [
                        //         [8, "\x20"],
                        //         [14, "Tolak Cagaran "],
                        //         [8,$modelreceiptfind->receipt_serial_number],
                        //         [38,"\x20"],
                        //         [9, $modelreceiptfind->receipt_content_sum],
                                
                        //     ]
                        // );
                        $form -> printBillDeposit($model->rn, $modelreceiptfind->receipt_serial_number, $modelreceiptfind->receipt_content_sum);
                    }
                }
                // if total receipt < total treatment
                else{
                    if($countReceipt > 2){
                        // total receipt + total treatment - total line, get different between total receipt and total treatment with total line
                        $count1 = ($countReceipt + $countTreatment) - $totalLine;
                        // get deposit print how many line and another print in "...."
                        $count2 = $countReceipt - ($count1 + 2);
                        if($index > $count2){
                            $totalCostReceipt += $modelreceiptfind->receipt_content_sum;
                        }
                        else{
                            // $form->printElementArray(
                            //     [
                            //         [8, "\x20"],
                            //         [14, "Tolak Cagaran "],
                            //         [8,$modelreceiptfind->receipt_serial_number],
                            //         [38,"\x20"],
                            //         [9, $modelreceiptfind->receipt_content_sum],
                                    
                            //     ]
                            // );
                            $form -> printBillDeposit($model->rn, $modelreceiptfind->receipt_serial_number, $modelreceiptfind->receipt_content_sum);
                        }
                    }
                    else{
                        if($index > 1){
                            // $totalCostReceipt += $modelreceiptfind->receipt_content_sum;
                            $totalCostReceipt = $form -> printMoreDeposit($totalCostReceipt, $modelreceiptfind->receipt_content_sum);
                        }
                        else{
                            // $form->printElementArray(
                            //     [
                            //         [8, "\x20"],
                            //         [14, "Tolak Cagaran "],
                            //         [8,$modelreceiptfind->receipt_serial_number],
                            //         [38,"\x20"],
                            //         [9, $modelreceiptfind->receipt_content_sum],
                                    
                            //     ]
                            // );
                            $form -> printBillDeposit($model->rn, $modelreceiptfind->receipt_serial_number, $modelreceiptfind->receipt_content_sum);
                        }
                    }
                }
            }

            if($index > 1 || $countReceipt > 2){
                // $form->printElementArray(
                //     [
                //         [6, "\x20"],
                //         [4, "...."],
                //         [6, "\x20"],
                //         [9, number_format((float)$totalCostReceipt, 2, '.', '')],
                //     ]
                // );
                $form -> printMoreDeposit($totalCostReceipt);
            }
        }
        // if total treatment and total receipt < total line(10)
        else{
            foreach ($modelTreatment as $index => $modeltreatmentfind) {
                if($index > 9){
                    // $totalCostTreatment += $modeltreatmentfind->item_total_unit_cost_rm;
                    $totalCostTreatment = $form -> printTotalTreamentUnitCost($totalCostTreatment, $modeltreatmentfind->item_total_unit_cost_rm);
                }
                else{
                    // $form->printElementArray(
                    //     [
                    //         [6, "\x20"],
                    //         [5, $modeltreatmentfind->treatment_code, true],
                    //         [1,"\x20"],
                    //         [30, $modeltreatmentfind->treatment_name,true],
                    //         [2,"\x20"],
                    //         [1,"x"],
                    //         [2,"\x20"],
                    //         [5,$modeltreatmentfind->item_count],
                    //         [7,"\x20"],
                    //         [8, $modeltreatmentfind->item_per_unit_cost_rm],
                    //         [8, $modeltreatmentfind->item_total_unit_cost_rm],

                    //     ]
                    // );
                    // $form->printNewLine(1);
                    $form -> printBillTreatment($bill_uid, $modeltreatmentfind->treatment_code, $modeltreatmentfind->treatment_name, $modeltreatmentfind->item_count, $modeltreatmentfind->item_per_unit_cost_rm,  $modeltreatmentfind->item_total_unit_cost_rm);
                }
            }

            if($index > 9){
                // $form->printElementArray(
                //     [
                //         [6, "\x20"],
                //         [4, "...."],
                //         [6, "\x20"],
                //         [9, number_format((float)$totalCostTreatment, 2, '.', '')],
                //     ]
                // );
                $form -> printMoreTreatment($totalCostTreatment);
            }

            // caj rawatan harian
            $form -> printBillDailyTreatment($model->bill_generation_billable_sum_rm);
            // $form->printElementArray(
            //     [
            //         [8, "\x20"],
            //         [18, "Caj Rawatan Harian"],
            //     ]
            // );
            // $form->printNewLine(1);
            // $form->printElementArray(
            //     [
            //         [8, "\x20"],
            //         [18, "------------------"],
            //         [45, "\x20"],
            //         [9, "  "], // need ask what price is this for
            //     ]
            // );
            // $form->printNewLine(1);
            // $form->printElementArray(
            //     [
            //         [67, "\x20"],
            //         [10, "----------"],
            //     ]
            // );
            // $form->printNewLine(1);
            // $form->printElementArray(
            //     [
            //         [68, "\x20"],
            //         [9, $model->bill_generation_billable_sum_rm],
            //     ]
            // );
            
            foreach ($modelreceipt as $index => $modelreceiptfind)
            {
                if($countTreatment + $countReceipt <= 10){
                    // $form->printElementArray(
                    //     [
                    //         [8, "\x20"],
                    //         [14, "Tolak Cagaran "],
                    //         [8,$modelreceiptfind->receipt_serial_number],
                    //         [38,"\x20"],
                    //         [9, $modelreceiptfind->receipt_content_sum],
                        
                    //     ]
                    // );
                    $form -> printBillDeposit($model->rn, $modelreceiptfind->receipt_serial_number, $modelreceiptfind->receipt_content_sum);

                }
                // else{
                //     if($index > 0){
                //         $totalCostReceipt += $modelreceiptfind->receipt_content_sum;
                //     }
                //     else{
                //         $form->printElementArray(
                //             [
                //                 [8, "\x20"],
                //                 [14, "Tolak Cagaran "],
                //                 [8,$modelreceiptfind->receipt_serial_number],
                //                 [38,"\x20"],
                //                 [9, $modelreceiptfind->receipt_content_sum],
                            
                //             ]
                //         );
                //     }
                // }
            }

            // if($index > 0){
            //     $form->printElementArray(
            //         [
            //             [6, "\x20"],
            //             [4, "...."],
            //             [6, "\x20"],
            //             [9, number_format((float)$totalCostReceipt, 2, '.', '')],
            //         ]
            //     );
            // }
        }

        $form->close();
              
            
        foreach ($modelreceipt as $index => $modelreceiptfind)
        {
            if($modelreceiptfind->receipt_type = 'deposit')
            {
           $ct ++;
            }
            if($ct>3)
            {
               // $abc = "...";
                break;
            }
        }
       
        foreach ($modelTreatment as $index => $modeltreatmentfind) 
        {
            if($index == 0)
            {
            
             $treatmentitem =$billkumpulan.$modeltreatmentfind->treatment_code.str_repeat("\x20", 6- strlen($modeltreatmentfind->treatment_code)).mb_strimwidth($modeltreatmentfind->treatment_name,0,30).str_repeat("\x20" ,31- strlen($modeltreatmentfind->treatment_name)).'  x  '.$modeltreatmentfind->item_count.str_repeat("\x20", 18 - 6 - strlen($modeltreatmentfind->item_count)).$modeltreatmentfind->item_per_unit_cost_rm.str_repeat("\x20", 9- strlen($modeltreatmentfind->item_per_unit_cost_rm)).$modeltreatmentfind->item_total_unit_cost_rm.("\n");            
             $ncounter = 1; 
            }
            else
            {
            
             if($ncounter<(8-$ct))// previously is <4 
             {  
                 //$treatmentitem =  $treatmentitem.$billkumpulan.implode($gettreatmentcode).str_repeat("\x20", 12 - strlen(implode($gettreatmentcode))). mb_strimwidth(implode($gettreatmentname),0,30).'         '.'x  '.implode($gettreamentunit).str_repeat("\x20", 6 - strlen(implode($gettreamentunit))).implode($gettreatmentunitcost).'    '.implode($gettreatmenttotalcost).("\n"); 
                 //$ncounter = $ncounter +1; 
                 $treatmentitem =$treatmentitem.$billkumpulan.$modeltreatmentfind->treatment_code.str_repeat("\x20", 6- strlen($modeltreatmentfind->treatment_code)).mb_strimwidth($modeltreatmentfind->treatment_name,0,30).str_repeat("\x20" ,31- strlen($modeltreatmentfind->treatment_name)).'  x  '.$modeltreatmentfind->item_count.str_repeat("\x20", 18 - 6 - strlen($modeltreatmentfind->item_count)).$modeltreatmentfind->item_per_unit_cost_rm.str_repeat("\x20", 9- strlen($modeltreatmentfind->item_per_unit_cost_rm)).$modeltreatmentfind->item_total_unit_cost_rm.("\n");                      
                $ncounter = $ncounter +1;  }
            
            } 
            
            
           foreach ($modelreceipt as $index => $modelreceiptfind)
           {
               if($index == 0 && $modelreceiptfind->receipt_type = 'deposit')
               {
                $cagaranitem = $fixfront."Tolak Cagaran ".$modelreceiptfind->receipt_serial_number.str_repeat("\x20" , 46-strlen($modelreceiptfind->receipt_serial_number)).$modelreceiptfind->receipt_content_sum.("\n");
                $rcounter = 1;
               }
               else
               {
                   if($rcounter<3)
                   
                   {
                       $cagaranitem = $cagaranitem .$fixfront."Tolak Cagaran ".$modelreceiptfind->receipt_serial_number.str_repeat("\x20" , 46-strlen($modelreceiptfind->receipt_serial_number)).$modelreceiptfind->receipt_content_sum.("\n");
                       $rcounter = $rcounter +1;
                   }
               }
           }

        } 

        /*
        if($rcounter>=3)
  {
      print_r($fixfront."...."."\n");
      print_r(str_repeat("\n", 8-1 -$rcounter));
      print_r("doesthiswork?");
  }
 // if ($rcounter<=3)
if ($rcounter<=3)
  {
      print_r($cagaranitem);
    print_r(str_repeat(nl2br("\n"), 8-$rcounter));
    print_r("do you work?");
    print_r($rcounter);
  } */
//print_r($treatmentitem);
//print_r($index);
  //print_r($ncounter);
  /*
  if ($rcounter<=3)
  { 
      print_r($treatmentitem);
    }
   
  
  //print_r($treatmentitem);
  
  if($ncounter>=4)
  {
   //$ncounter -1;
   print_r($treatmentitem);
   print_r(str_repeat("\x20", 6). "...."."\n");
   print_r(str_repeat("\n", 5 -  $ncounter));
   print_r($ncounter);
   
  } */
  /*
  print_r($treatmentitem);
print_r($ncounter);
if ($ncounter>4)
{
print_r("...");
}
print_r($cagaranitem);
  exit();
  */
      //print_r($treatmentitem.nl2br("\n"));
     // print_r($cagaranitem);
     //print_r($ncounter.("  ct= "));
    // print_r($ct);
       // exit();
        foreach($modelWard as $index => $modelwardfind){
            if($index == 0){
                $lastEndDate =ArrayHelper::toArray($modelwardfind->ward_end_datetime);
            }
            if($index == count($modelWard) - 1){
                
                $firstStartDate = ArrayHelper::toArray($modelwardfind->ward_start_datetime);
            }
        }

        $printstartdate = implode($firstStartDate);
        $printlastenddate = implode($lastEndDate);

        if ($this->request->isPost && $model->load($this->request->post())) {
            if($model->validate() && $model->bill_print_id != "")
            {

                        $blankmiddle = str_repeat("\n", 11);
                        $fixfront = str_repeat("\x20", 8);
                        $billdate = str_repeat("\x20", 62);
                        $billrn = str_repeat("\x20", 7);
                        $billname = str_repeat("\x20", 7); // adds 14 spaces
                        $billadd1 = str_repeat("\x20", 7);
                        $billadd2 = str_repeat("\x20", 7);
                        $billadd3 = str_repeat("\x20", 7);
                        $billkelas = str_repeat("\x20", 35);
                        $billmasuk = str_repeat("\x20", 7);
                        $billkeluar = str_repeat("\x20", 22);
                        $billkumpulan = str_repeat("\x20", 6);
                        $fixbackblank = str_repeat("\x20", 36);
                        $fixbackblank2 = str_repeat("\x20", 34);
                        $fixbackblank3 = str_repeat("\x20", 35);
                        $fixbreakbottom = str_repeat("\n", 5);
                        $fixbreakmiddle= str_repeat("\n", 3);
                        $fixbreaktop= str_repeat("\n", $ncounter);

                                    
                                    //$cagaranno = " ";
                                                // $totalCost = 0;

                                                // // $countTreatment = count($modelTreatment);

                                                // foreach ($modelTreatment as $index => $modeltreatmentfind) {
                                                //     if($index > 5){
                                                //         $totalCost += $modeltreatmentfind->item_total_unit_cost_rm;
                                                    //     $form->printElementArray(
                                                    //         [
                                                    //             [6, "\x20"],
                                                    //             [4, "...."],
                                                    //             [6, "\x20"],
                                                    //             [9, $totalCost],
                                                    //         ]
                                                    //     );
                                                    // }
                                                //     else{
                                                //     $form->printElementArray(
                                                //             [
                                                //                 [6, "\x20"],
                                                //                 [5, $modeltreatmentfind->treatment_code, true],
                                                //                 [1,"\x20"],
                                                //                 [30, $modeltreatmentfind->treatment_name,true],
                                                //                 [2,"\x20"],
                                                //                 [1,"x"],
                                                //                 [2,"\x20"],
                                                //                 [5,$modeltreatmentfind->item_count],
                                                //                 [7,"\x20"],
                                                //                 [8, $modeltreatmentfind->item_per_unit_cost_rm],
                                                //                 [1,"x"],
                                                //                 [8, $modeltreatmentfind->item_total_unit_cost_rm],

                                                //             ]
                                                //         );
                                                //         $form->printNewLine(1);
                                                //     }
                                                // }

                                                // $form->close();
                                                $entrydate = date("d/m/Y" , strtotime($model->bill_generation_datetime));
                                    $wardentrydate =date("d/m/Y" , strtotime($printstartdate));
                                    $wardleavedate =date("d/m/Y" , strtotime($printlastenddate));
                                    $entrydatetime =date("d/m/Y H:i" , strtotime($model->entry_datetime));
                                    
                                                $form = new PrintForm(PrintForm::Bill);
                                                $form->printNewLine(8); // mayb 9
                                                $form->printElementArray(
                                                    [
                                                        [62, "\x20"],
                                                        [10, $entrydate],
                                                    ]
                                                );
                                            
                                            $form->printNewLine(1);
                                            $form->printElementArray(
                                                [
                                                    [7, "\x20"],
                                                    [11, $model->rn],
                                                ]
                                            );
                                        
                                        $form->printNewLine(1);
                                        $form->printElementArray(
                                            [
                                                [7, "\x20"],
                                                [35, $modelpatient->name,true],
                                            ]
                                        );
                                    $form->printNewLine(1);
                                    $form->printElementArray(
                                        [
                                            [7, "\x20"],
                                            [35, $modelpatient->address1,true],
                                        ]
                                    );
                                $form->printNewLine(1);
                                $form->printElementArray(
                                    [
                                        [7, "\x20"],
                                        [35, $modelpatient->address2,true],
                                    ]
                                );
                                $form->printNewLine(1);
                                $form->printElementArray(
                                    [
                                        [7, "\x20"],
                                        [35, $modelpatient->address3,true],
                                    ]
                                );
                                $form->printNewLine(11);
                                $form->printElementArray(
                                    [
                                        [7, "\x20"],
                                        [32, "Caj Duduk Wad  (Tarikh Masuk  : "],
                                        [10, $wardentrydate],
                                        [2," )"],

                                    ]
                                );
                                $form->printNewLine(1);
                                $form->printElementArray(
                                    [
                                        [22, "\x20"],
                                        [17, "(Tarikh Keluar  : "],
                                        [10, $wardleavedate],
                                        [2," )"],

                                    ]
                                );
                                $form->printNewLine(3);
                                $form->printElementArray(
                                    [
                                        [7, "\x20"],
                                        [7, "Kelas  "],
                                        [2, $modelwardfind->ward_code],
                                        [4," :  "],
                                        [5,$modelwardfind->ward_number_of_days],
                                        [1," "],
                                        [4, "hari"],
                                        [28, "\x20"],
                                        [9, $model->daily_ward_cost],
                                        [2, "\x20"],
                                        [9, Bill::getTotalWardCost(Yii::$app->request->get('bill_uid'))],
                                    ]
                                );
                                $form->printNewLine(2);
                                $form->printElementArray(
                                    [
                                        [7, "\x20"],
                                        [28, "Caj Pemeriksaan/Ujian Makmal"],
                                    ]
                                );
                                $form->printNewLine(1);
                                $form->printElementArray(
                                    [
                                        [7, "\x20"],
                                        [28, "-----------------------------"],
                                    ]
                                );
                                $form->printNewLine(1);

                                //print bill/caj rawatan harian and cagaran, refund
                                $form->printElementArray(
                                    [
                                        [28, "\x20"],
                                        [29, "JUMLAH YANG PERLU DIBAYAR ==>"], //$model->bill_generation_final_fee_rm
                                        [11,"\x20"],
                                        [9,$model->bill_generation_final_fee_rm],
                                    ]
                                );
                                            $form->close();
                                            
                                                                 
                                                                
                    $model->bill_print_datetime =  $date->format('Y-m-d H:i');
                    $model->bill_uid = Yii::$app->request->get('bill_uid');
                    $cookies = Yii::$app->request->cookies;
                    $model->bill_print_responsible_uid = $cookies->getValue('cookie_login');
                    $model->save();

                    return Yii::$app->getResponse()->redirect(array('/bill/print', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));         
                      
            }
            else
            {
                $message = 'Bill Print ID should not be empty.';
                $model->addError('bill_print_id', $message);
            }
        }

        return $this->render('print', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
        ]);
    }

    /**
     * Deletes an existing Bill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $bill_uid Bill Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($bill_uid)
    {
        $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);

        foreach ($modelWard as $modelWard) {
            $modelWard->delete();
        }

        foreach ($modelTreatment as $modelTreatment) {
            $modelTreatment->delete();
        }

        $this->findModel($bill_uid)->delete();

        return Yii::$app->getResponse()->redirect(array('/bill/create', 
            'rn' => Yii::$app->request->get('rn'))); 
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