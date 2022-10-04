<?php

namespace app\controllers;

use Yii;
use app\models\Bill;
use app\models\Ward;
use app\models\Model;
use app\models\WardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;
use GpsLab\Component\Base64UID\Base64UID;
use yii\helpers\Json;
use DateTime;

/**
 * WardController implements the CRUD actions for Ward model.
 */
class WardController extends Controller
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

    public function actionWard($bill_uid) {
        $cost = array();
        $cost['wardTotal'] = (new Bill()) -> getTotalWardCost($bill_uid);
        $cost['billAble'] = (new Bill()) -> calculateBillable($bill_uid);
        $cost['finalFee'] = (new Bill()) -> calculateFinalFee($bill_uid);
        echo Json::encode($cost);
    }

    public function actionDischargedate($bill_uid) {
        $discharge_date = array();
        $discharge_date['date'] = (new Bill()) -> getLastWardEndDateTime($bill_uid);
        echo Json::encode($discharge_date);
    }

    public function actionWardrow()
    {
        // Add Ward Row
        // if (Yii::$app->request->post('addWardRow') == 'true') {
            $dbWard = Ward::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);   

            // if(empty($dbWard)) {
            //     $countWard = count(Yii::$app->request->post('Ward', []));
            //     for($i = 0; $i < $countWard; $i++) {
            //         $modelWard[] = new Ward();
            //     }
            //     $modelWard[] = new Ward();
            // }
            // else {
            //     $modelWard = $dbWard;
            //     $countWard = count(Yii::$app->request->post('Ward', [])) - count($dbWard);
            //     for($i = 0; $i < $countWard; $i++) {
            //         $modelWard[] = new Ward();
            //     }
            //     $modelWard[] = new Ward();
            // }   

            if(empty(Yii::$app->request->get('update'))){
                if(!empty(Yii::$app->request->get('countWard'))){
                    $modelWard = $dbWard;
                    $countWard = (int)Yii::$app->request->get('countWard');
                    $count = $countWard - count($dbWard);

                    for($i = 0; $i < $count; $i++){
                        $modelWard[] = new Ward();
                    }
                    $modelWard[] = new Ward();
                }
            }
            else{
                $modelWard = $dbWard;
                $modelWard[] = new Ward();
            }

            return $this->renderPartial('/ward/_form', [
                'modelWard' => $modelWard,
            ]);
        // }
    }

    /**
     * Lists all Ward models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new WardSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ward model.
     * @param string $ward_uid Ward Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ward_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($ward_uid),
        ]);
    }

    /**
     * Creates a new Ward model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Ward();
        $model->ward_uid = Base64UID::generate(32);
        $model->ward_start_datetime = date("d-m-Y H:i:s");
        $model->ward_end_datetime = date("d-m-Y H:i:s");
        $model->loadDefaultValues();
        $model-> save();
        return $this->redirect(['bill/create', 'rn' =>  Yii::$app->request->get('rn')]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Ward model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ward_uid Ward Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        // Insert and Update Ward
        // Yii::$app->request->post('saveWard') == 'true' &&
        if(Yii::$app->request->post('Ward', [])) {
            $dbWard = Ward::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);   
            $modelWard = Model::createMultiple(Ward::className());

            if(empty($dbWard)) {
                if( Model::loadMultiple($modelWard, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelWard);
                    
                    
                    if($valid ) {                    
                        foreach ($modelWard as $modelWard) {
                            $modelWard->ward_uid = Base64UID::generate(32);
                            $modelWard->bill_uid = Yii::$app->request->get('bill_uid');

                            if(!empty($modelWard->ward_code) && !empty($modelWard->ward_start_datetime) && !empty($modelWard->ward_end_datetime) && !empty($modelWard->ward_number_of_days)){
                                $modelWard->save();
                            }
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
                                $modelWard[$i - 1]->bill_uid = Yii::$app->request->get('bill_uid');

                                if(!empty($modelWard[$i - 1]->ward_code) && !empty($modelWard[$i - 1]->ward_start_datetime) && !empty($modelWard[$i - 1]->ward_end_datetime) && !empty($modelWard[$i - 1]->ward_number_of_days)){
                                    $modelWard[$i - 1]->save();
                                }
                                else{
                                    $modelWardUpdate = Ward::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]); 
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


                        // else if($countWard == $countdb){   
                        //     $modelWardUpdate = Ward::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]); 
                        //     if( Model::loadMultiple($modelWardUpdate, Yii::$app->request->post())) {
                        //         $valid = Model::validateMultiple($modelWardUpdate);
                                
                        //         if($valid) {            
                        //             foreach ($modelWardUpdate as $modelWardUpdate) {
                        //                 $modelWardUpdate->save();
                        //             }
                        //         }
                        //     }
                        // }
                    }
                }
            } 

            // return Yii::$app->getResponse()->redirect(array('/bill/generate', 
            //     'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'ward'))->send();
        }
    }

    /**
     * Deletes an existing Ward model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ward_uid Ward Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ward_uid)
    {
        $this->findModel($ward_uid)->delete();

        // if(!empty( Yii::$app->request->get('bill_uid'))){ 
        //     return Yii::$app->getResponse()->redirect(array('/bill/generate', 
        //         'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn'), '#' => 'ward')); 
        // } 
    }

    /**
     * Finds the Ward model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ward_uid Ward Uid
     * @return Ward the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ward_uid)
    {
        if (($model = Ward::findOne(['ward_uid' => $ward_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
