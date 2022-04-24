<?php

namespace app\controllers;

use Yii;
use app\models\Lookup_department;
use app\models\Lookup_departmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

/**
 * Lookup_departmentController implements the CRUD actions for Lookup_department model.
 */
class Lookup_departmentController extends Controller
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
            'department' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => Lookup_department::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;  
                }
            ]
        ]);
    }

    /**
     * Lists all Lookup_department models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $modelLOD = new Lookup_department();
        $searchModel = new Lookup_departmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost)
        {
            if ($modelLOD->load($this->request->post())) $this->actionLOD($modelLOD);
            else $modelLOD->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLOD($modelLOD){
        if ($modelLOD->save()) {
             $model_founded = Lookup_departmentController::findModel($modelLOD->department_uid);
             if(!empty($model_founded))
                 return Yii::$app->getResponse()->redirect(array('/lookup_department/index', 
                     'depart' => $model_founded->department_uid));
         }
    }

    public function InitSQL(){
        $Tables = array(
            "CREATE TABLE IF NOT EXISTS `lookup_department` (
                `department_uid` VARCHAR(64) NOT NULL,
                `department_code` VARCHAR(20) UNIQUE NOT NULL,
                `department_name` VARCHAR(50) NOT NULL,
                `phone_number` VARCHAR(100),
                `address1` VARCHAR(100),
                `address2` VARCHAR(100),
                `address3` VARCHAR(100),
                PRIMARY KEY (`department_uid`)
            );"
        );

        for($i=0; $i < count($Tables); $i++)
        {
            $sqlCommand = Yii::$app->db->createCommand($Tables[$i]);
            $sqlCommand->execute();    
        }
    }

    /**
     * Displays a single Lookup_department model.
     * @param string $department_uid Department Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($department_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($department_uid),
        ]);
    }

    /**
     * Creates a new Lookup_department model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lookup_department();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index', 'department_uid' => $model->department_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lookup_department model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $department_uid Department Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($department_uid)
    {
        $model = $this->findModel($department_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'department_uid' => $model->department_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lookup_department model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $department_uid Department Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($department_uid)
    {
        $this->findModel($department_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lookup_department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $department_uid Department Uid
     * @return Lookup_department the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($department_uid)
    {
        if (($model = Lookup_department::findOne(['department_uid' => $department_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
