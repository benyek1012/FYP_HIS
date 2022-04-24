<?php

namespace app\controllers;

use Yii;
use app\models\Lookup_treatment;
use app\models\Lookup_treatmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

/**
 * Lookup_treatmentController implements the CRUD actions for Lookup_treatment model.
 */
class Lookup_treatmentController extends Controller
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

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'treatment' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => Lookup_treatment::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;  
                }
            ]
        ]);
    }

    /**
     * Lists all Lookup_treatment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $modelLOT = new Lookup_treatment();
        $searchModel = new Lookup_treatmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost)
        {
            if ($modelLOT->load($this->request->post())) $this->actionLOT($modelLOT);
            else $modelLOT->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLOT($modelLOT){
        if ($modelLOT->save()) {
             $model_founded = Lookup_treatmentController::findModel($modelLOT->treatment_uid);
             if(!empty($model_founded))
                 return Yii::$app->getResponse()->redirect(array('/lookup_treatment/index', 
                     'treat' => $model_founded->treatment_uid));
         }
    }

    public function InitSQL(){
        $Tables = array(
            "CREATE TABLE IF NOT EXISTS `lookup_treatment` (
                `treatment_uid` VARCHAR(64) NOT NULL,
                `treatment_code` VARCHAR(20) UNIQUE NOT NULL,
                `treatment_name` VARCHAR(50) NOT NULL,
                `class_1_cost_per_unit` DECIMAL(10,2) NOT NULL,
                `class_2_cost_per_unit` DECIMAL(10,2) NOT NULL,
                `class_3_cost_per_unit` DECIMAL(10,2) NOT NULL,
                PRIMARY KEY (`treatment_uid`)
            );"
        );

        for($i=0; $i < count($Tables); $i++)
        {
            $sqlCommand = Yii::$app->db->createCommand($Tables[$i]);
            $sqlCommand->execute();    
        }
    }

    /**
     * Displays a single Lookup_treatment model.
     * @param string $treatment_uid Treatment Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($treatment_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($treatment_uid),
        ]);
    }

    /**
     * Creates a new Lookup_treatment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lookup_treatment();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index', 'treatment_uid' => $model->treatment_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lookup_treatment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $treatment_uid Treatment Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($treatment_uid)
    {
        $model = $this->findModel($treatment_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'treatment_uid' => $model->treatment_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lookup_treatment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $treatment_uid Treatment Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($treatment_uid)
    {
        $this->findModel($treatment_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lookup_treatment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $treatment_uid Treatment Uid
     * @return Lookup_treatment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($treatment_uid)
    {
        if (($model = Lookup_treatment::findOne(['treatment_uid' => $treatment_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
