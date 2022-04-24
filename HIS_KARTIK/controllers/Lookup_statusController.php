<?php

namespace app\controllers;

use Yii;
use app\models\Lookup_status;
use app\models\Lookup_statusSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

/**
 * Lookup_statusController implements the CRUD actions for Lookup_status model.
 */
class Lookup_statusController extends Controller
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
            'status' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => Lookup_status::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;  
                }
            ]
        ]);
    }

    /**
     * Lists all Lookup_status models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $modelLOS = new Lookup_status();
        $searchModel = new Lookup_statusSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost)
        {
            if ($modelLOS->load($this->request->post())) $this->actionLOS($modelLOS);
            else $modelLOS->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLOS($modelLOS){
        if ($modelLOS->save()) {
             $model_founded = Lookup_statusController::findModel($modelLOS->status_uid);
             if(!empty($model_founded))
                 return Yii::$app->getResponse()->redirect(array('/lookup_status/index', 
                     'stat' => $model_founded->status_uid));
         }
    }

    public function InitSQL(){
        $Tables = array(
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
        );

        for($i=0; $i < count($Tables); $i++)
        {
            $sqlCommand = Yii::$app->db->createCommand($Tables[$i]);
            $sqlCommand->execute();    
        }
    }
    

    /**
     * Displays a single Lookup_status model.
     * @param string $status_uid Status Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($status_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($status_uid),
        ]);
    }

    /**
     * Creates a new Lookup_status model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lookup_status();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index', 'status_uid' => $model->status_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lookup_status model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $status_uid Status Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($status_uid)
    {
        $model = $this->findModel($status_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'status_uid' => $model->status_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lookup_status model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $status_uid Status Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($status_uid)
    {
        $this->findModel($status_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lookup_status model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $status_uid Status Uid
     * @return Lookup_status the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($status_uid)
    {
        if (($model = Lookup_status::findOne(['status_uid' => $status_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
