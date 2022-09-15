<?php

namespace app\controllers;

use Yii;
use app\models\Bill;
use app\models\Model;
use app\models\Fpp;
use app\models\FppSearch;
use app\models\Lookup_fpp;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use GpsLab\Component\Base64UID\Base64UID;
use yii\helpers\Json;

/**
 * FppController implements the CRUD actions for Fpp model.
 */
class FppController extends Controller
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

    public function actionFpp($bill_uid) {
        $cost = array();
        $cost['fppTotal'] = (new Bill()) -> getTotalFPPCost($bill_uid);
        $cost['billAble'] = (new Bill()) -> calculateBillable($bill_uid);
        $cost['finalFee'] = (new Bill()) -> calculateFinalFee($bill_uid);
        echo Json::encode($cost);
    }

    public function actionFpprow()
    {
        // if (Yii::$app->request->post('addFppRow') == 'true') {
            $dbFpp = Fpp::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);

            // if(empty($dbFpp)) {
            //     $count = count(Yii::$app->request->post('Fpp', []));
            //     for($i = 0; $i < $count; $i++) {
            //         $modelFPP[] = new Fpp();
            //     }
            //     $modelFPP[] = new Fpp();
            // }
            // else {
            //     $modelFPP = $dbFpp;
            //     $count = count(Yii::$app->request->post('Fpp', [])) - count($dbFpp);
            //     for($i = 0; $i < $count; $i++) {
            //         $modelFPP[] = new Fpp();
            //     }
            //     $modelFPP[] = new Fpp();
            // }

            if(empty(Yii::$app->request->get('update'))){
                if(!empty(Yii::$app->request->get('countFpp'))){
                    $modelFPP = $dbFpp;
                    $countFpp = (int)Yii::$app->request->get('countFpp');
                    $count = $countFpp - count($dbFpp);

                    for($i = 0; $i < $count; $i++){
                        $modelFPP[] = new Fpp();
                    }
                    $modelFPP[] = new Fpp();
                }
            }
            else{
                $modelFPP = $dbFpp;
                $modelFPP[] = new Fpp();
            }

            return $this->renderPartial('/fpp/_form', [
                'modelFPP' => $modelFPP,
            ]);
        // }
    }

    /**
     * Lists all Fpp models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FppSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Fpp model.
     * @param string $kod Kod
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($kod)
    {
        return $this->render('view', [
            'model' => $this->findModel($kod),
        ]);
    }

    /**
     * Creates a new Fpp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Fpp();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'kod' => $model->kod]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Fpp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $kod Kod
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        // Insert and Update Treatment
        // Yii::$app->request->post('saveFpp') == 'true' && 
        if(Yii::$app->request->post('Fpp', [])) {
            $dbFpp = Fpp::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);   
            $modelFPP = Model::createMultiple(Fpp::className());

            if(empty($dbFpp)) {
                if( Model::loadMultiple($modelFPP, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelFPP);
                    
                    if($valid) {                    
                        foreach ($modelFPP as $modelFPP) {
                            $modelFPP->fpp_uid = Base64UID::generate(32);
                            $modelFPP->bill_uid = Yii::$app->request->get('bill_uid');

                            if(!empty($modelFPP->kod) && !empty($modelFPP->name) && !empty($modelFPP->cost_per_unit) && !empty($modelFPP->min_cost_per_unit) && !empty($modelFPP->max_cost_per_unit) && !empty($modelFPP->number_of_units) && !empty($modelFPP->total_cost)){
                                $modelFPP->save();
                            }
                        }
                    }
                }
            }
            else {
                $countFpp = count(Yii::$app->request->post('Fpp', []));
                $countdb = count($dbFpp);

                if( Model::loadMultiple($modelFPP, Yii::$app->request->post())) {
                    $valid = Model::validateMultiple($modelFPP);
                    
                    if($valid) {    
                        if($countFpp > $countdb){          
                            for($i = $countFpp; $i > $countdb; $i--) {
                                $modelFPP[$i - 1]->fpp_uid = Base64UID::generate(32);
                                $modelFPP[$i - 1]->bill_uid = Yii::$app->request->get('bill_uid');

                                if(!empty($modelFPP[$i - 1]->kod) && !empty($modelFPP[$i - 1]->name) && !empty($modelFPP[$i - 1]->cost_per_unit) && !empty($modelFPP[$i - 1]->min_cost_per_unit) && !empty($modelFPP[$i - 1]->max_cost_per_unit) && !empty($modelFPP[$i - 1]->number_of_units) && !empty($modelFPP[$i - 1]->total_cost)){
                                    $modelFPP[$i - 1]->save();
                                }
                                else{
                                    $modelFppUpdate = Fpp::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]); 
                
                                    if( Model::loadMultiple($modelFppUpdate, Yii::$app->request->post())) {
                                        $valid = Model::validateMultiple($modelFppUpdate);
                                        
                                        if($valid) {                    
                                            foreach ($modelFppUpdate as $modelFppUpdate) {
                                                $modelLookUpFpp = Lookup_fpp::findOne( ['kod' => $modelFppUpdate->kod]);

                                                $modelFppUpdate->name = $modelLookUpFpp->name;
                                                $modelFppUpdate->min_cost_per_unit = $modelLookUpFpp->min_cost_per_unit;
                                                $modelFppUpdate->max_cost_per_unit = $modelLookUpFpp->max_cost_per_unit;
                                    
                                                $costPerUnit = $modelFppUpdate->cost_per_unit;
                                                $numberOfUnit = $modelFppUpdate->number_of_units;
                                    
                                                $modelFppUpdate->total_cost = $costPerUnit * $numberOfUnit;

                                                $modelFppUpdate->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // else if(($countFpp - 1) == $countdb){
                        //     $modelFppUpdate = Fpp::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]); 
                
                        //     if( Model::loadMultiple($modelFppUpdate, Yii::$app->request->post())) {
                        //         $valid = Model::validateMultiple($modelFppUpdate);
                                
                        //         if($valid) {                    
                        //             foreach ($modelFppUpdate as $modelFppUpdate) {
                        //                 $modelLookUpFpp = Lookup_fpp::findOne( ['kod' => $modelFppUpdate->kod]);

                        //                 $modelFppUpdate->name = $modelLookUpFpp->name;
                        //                 $modelFppUpdate->min_cost_per_unit = $modelLookUpFpp->min_cost_per_unit;
                        //                 $modelFppUpdate->max_cost_per_unit = $modelLookUpFpp->max_cost_per_unit;
                            
                        //                 $costPerUnit = $modelFppUpdate->cost_per_unit;
                        //                 $numberOfUnit = $modelFppUpdate->number_of_units;
                            
                        //                 $modelFppUpdate->total_cost = $costPerUnit * $numberOfUnit;

                        //                 $modelFppUpdate->save();
                        //             }
                        //         }
                        //     }
                        // }
                    }
                }
            } 

            // return Yii::$app->getResponse()->redirect(array('/bill/generate', 
            //     'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'treatment'));
        }
    }

    /**
     * Deletes an existing Fpp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $kod Kod
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($fpp_uid)
    {
        $this->findModel($fpp_uid)->delete();
        // $this->findModel($kod)->delete();

        // return $this->redirect(['index']);
    }

    /**
     * Finds the Fpp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $kod Kod
     * @return Fpp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($fpp_uid)
    {
        if (($model = Fpp::findOne(['fpp_uid' => $fpp_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
