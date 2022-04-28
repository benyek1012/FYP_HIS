<?php

namespace app\controllers;

use Yii;
use app\models\Bill;
use app\models\BillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Ward;
use yii\base\Exception;
use app\models\Model;
use app\models\Treatment_details;
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
                        'delete' => ['POST'],
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

        //Find out how many ward have been submitted by the form
        // $count = count(Yii::$app->request->post('Ward', []));
        $count = 1;

        //Send at least one model to the form
        $modelWard = [new Ward];

        //Create an array of the wards submitted
        for($i = 1; $i < $count; $i++) {
            $modelWard[] = new Ward();
        }

        $modelTreatment = [new Treatment_details];

        for($i = 1; $i < $count; $i++) {
            $modelTreatment[] = new Treatment_details();
        }

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $model->save()) {
                $modelWard = Model::createMultiple(Ward::classname());
                $modelTreatment = Model::createMultiple(Treatment_details::className());
                Model::loadMultiple($modelWard, Yii::$app->request->post());
                Model::loadMultiple($modelTreatment, Yii::$app->request->post());

                // validate all models
                $valid = $model->validate();
                $valid = Model::validateMultiple($modelWard) && $valid;
                $valid = Model::validateMultiple($modelTreatment) && $valid;
                
                if ($valid) {
                    
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            foreach ($modelWard as $modelWard) {
                                $modelWard->bill_uid = $model->bill_uid;
                                $modelWard->ward_uid = Base64UID::generate(32);
                                if (! ($flag = $modelWard->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }

                            foreach ($modelTreatment as $modelTreatment) {
                                $modelTreatment->bill_uid = $model->bill_uid;
                                $modelTreatment->treatment_details_uid = Base64UID::generate(32);
                                if (! ($flag = $modelTreatment->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            // var_dump($modelWard);
                            // exit();
                            // return $this->redirect(['view', 'bill_uid' => $model->bill_uid, 'rn' => $model->rn]);
                            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'b'));    
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
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

        // $totalWardDays = 0;
        // $dailyWardCost = 0.0;
        // $totalTreatmentCost = 0.0;
        // $billable = 0.0;

        if ($this->request->isPost && $model->load($this->request->post())) {
            if(empty($model->bill_generation_datetime))
            {
                $model->bill_generation_datetime =  $date->format('Y-m-d H:i');
                // foreach ($modelWard as $index => $modelWard){
                //     $totalWardDays += "[$index]ward_number_of_days";
                //     $dailyWardCost = "[$index]daily_ward_cost";
                //     $totalTreatmentCost += "[$index]item_per_unit_cost" * "[$index]item_count";
                // }
                
                // $billable = ($totalWardDays * $dailyWardCost) + $totalTreatmentCost;
                // $model->bill_generation_billable_sum_rm = $billable;
                // $model->bill_generation_final_fee_rm = $billable;
            }
           
            $model->bill_uid = Yii::$app->request->get('bill_uid');
            $model->save();

            return Yii::$app->getResponse()->redirect(array('/bill/print', 
                'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'p'));        
        }

        return $this->render('generate', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
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

        // $totalWardDays = 0;
        // $dailyWardCost = 0.0;
        // $totalTreatmentCost = 0.0;
        // $billable = 0.0;

        if ($this->request->isPost && $model->load($this->request->post())) {
           
            // if(empty($model->bill_print_datetime))
            // {
            $model->bill_print_datetime =  $date->format('Y-m-d H:i');
            // }
            $model->bill_uid = Yii::$app->request->get('bill_uid');
            $model->save();

            return Yii::$app->getResponse()->redirect(array('/bill/print', 
                'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'p'));             
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
        $this->findModel($bill_uid)->delete();

        return $this->redirect(['index']);
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
}
