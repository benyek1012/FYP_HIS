<?php

namespace app\controllers;

use app\models\Bill;
use app\models\Model;
use app\models\Lookup_treatment;
use app\models\Treatment_details;
use app\models\Treatment_detailsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use GpsLab\Component\Base64UID\Base64UID;
use Yii;

/**
 * Treatment_detailsController implements the CRUD actions for Treatment_details model.
 */
class Treatment_detailsController extends Controller
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

    public function actionTreatmentrow()
    {
        if (Yii::$app->request->post('addTreatmentRow') == 'true') {
            $dbTreatment = Treatment_details::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);

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

            return $this->renderPartial('/treatment_details/_form', [
                'modelTreatment' => $modelTreatment,
            ]);
        }
    }

    /**
     * Lists all Treatment_details models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new Treatment_detailsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Treatment_details model.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($treatment_details_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($treatment_details_uid),
        ]);
    }

    /**
     * Creates a new Treatment_details model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Treatment_details();
        $model->treatment_details_uid = Base64UID::generate(32);
        $model->loadDefaultValues();
        $model-> save();
        return $this->redirect(['bill/create', 'rn' => Yii::$app->request->get('rn')]);

        return $this->renderPartial('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Treatment_details model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
         // Insert and Update Treatment
         // Yii::$app->request->post('saveTreatment') == 'true' && 
         if(Yii::$app->request->post('Treatment_details', [])) {
            $dbTreatment = Treatment_details::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);   
            $modelTreatment = Model::createMultiple(Treatment_details::className());

            if(empty($dbTreatment)) {
                if( Model::loadMultiple($modelTreatment, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelTreatment);
                    
                    if($valid) {                    
                        foreach ($modelTreatment as $modelTreatment) {
                            $modelTreatment->treatment_details_uid = Base64UID::generate(32);
                            $modelTreatment->bill_uid = Yii::$app->request->get('bill_uid');

                            if(!empty($modelTreatment->treatment_code) && !empty($modelTreatment->treatment_name) && !empty($modelTreatment->item_per_unit_cost_rm) && !empty($modelTreatment->item_count) && !empty($modelTreatment->item_total_unit_cost_rm)){
                                $modelTreatment->save();
                            }
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
                                $modelTreatment[$i - 1]->bill_uid = Yii::$app->request->get('bill_uid');

                                if(!empty($modelTreatment[$i - 1]->treatment_code) && !empty($modelTreatment[$i - 1]->treatment_name) && !empty($modelTreatment[$i - 1]->item_per_unit_cost_rm) && !empty($modelTreatment[$i - 1]->item_count) && !empty($modelTreatment[$i - 1]->item_total_unit_cost_rm)){
                                    $modelTreatment[$i - 1]->save();
                                }
                            }
                        }
                        else if($countTreatment == $countdb){
                            $modelTreatmentUpdate = Treatment_details::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]); 
                            $modelBill = Bill::findOne(['bill_uid' => Yii::$app->request->get('bill_uid')]);
                
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
            // return Yii::$app->getResponse()->redirect(array('/bill/generate', 
            //     'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'treatment'));
        }
    }

    /**
     * Deletes an existing Treatment_details model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($treatment_details_uid)
    {
        $this->findModel($treatment_details_uid)->delete();

        // if(!empty( Yii::$app->request->get('bill_uid'))){ 
        //     return Yii::$app->getResponse()->redirect(array('/bill/generate', 
        //         'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn'), "#" => 'treatment')); 
        // } 
    }

    /**
     * Finds the Treatment_details model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return Treatment_details the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($treatment_details_uid)
    {
        if (($model = Treatment_details::findOne(['treatment_details_uid' => $treatment_details_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
