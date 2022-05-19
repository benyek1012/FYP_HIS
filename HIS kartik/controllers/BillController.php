<?php

namespace app\controllers;

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
use GpsLab\Component\Base64UID\Base64UID;

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

    // Check Date Clashing
    public function actionDate($bill_uid){
        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(['ward_start_datetime' => SORT_ASC])->all(); 
         
        if($modelWard != null){
            $modelDate = Ward::find()->where(['between', 'ward_start_datetime', $modelWard[0]->ward_start_datetime, $modelWard[0]->ward_end_datetime])->all();
        }
        echo Json::encode($modelDate);
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

                $modelTreatment->item_total_unit_cost_rm = $modelTreatment->item_per_unit_cost_rm * $modelTreatment->item_count;

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
                            $modelBill = Bill::findOne(['bill_uid' => $bill_uid]);
                
                            if( Model::loadMultiple($modelTreatmentUpdate, Yii::$app->request->post())) {
                                $valid = Model::validateMultiple($modelTreatmentUpdate);
                                
                                if($valid) {                    
                                    foreach ($modelTreatmentUpdate as $modelTreatmentUpdate) {
                                        $modelLookupTreatment = Lookup_treatment::findOne( ['treatment_code' => $modelTreatmentUpdate->treatment_code]);
                                        
                                        if($modelBill->class == '1a' || $modelBill->class == '1b' || $modelBill->class == '1c'){
                                            $modelTreatmentUpdate->item_per_unit_cost_rm = $modelLookupTreatment->class_1_cost_per_unit;
                                        }
                                        if($modelBill->class == '2'){
                                            $modelTreatmentUpdate->item_per_unit_cost_rm = $modelLookupTreatment->class_2_cost_per_unit;
                                        }
                                        if($modelBill->class == '3'){
                                            $modelTreatmentUpdate->item_per_unit_cost_rm = $modelLookupTreatment->class_3_cost_per_unit;
                                        }
                            
                                        $itemPerUnit = $modelTreatmentUpdate->item_per_unit_cost_rm;
                                        $itemCount = $modelTreatmentUpdate->item_count;
                            
                                        $modelTreatmentUpdate->item_total_unit_cost_rm = $itemPerUnit * $itemCount;

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

        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(['ward_start_datetime' => SORT_ASC])->all();   
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
        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);


        if ($this->request->isPost && $model->load($this->request->post())) {
            if($model->validate() && $model->bill_print_id != "")
            {
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
