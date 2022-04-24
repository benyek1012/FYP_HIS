<?php

namespace app\controllers;

use Yii;
use app\models\Lookup_ward;
use app\models\Lookup_wardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;


/**
 * Lookup_wardController implements the CRUD actions for Lookup_ward model.
 */
class Lookup_wardController extends Controller
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
            'ward' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => Lookup_ward::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;  
                }
            ]
        ]);
    }

    /**
     * Lists all Lookup_ward models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $modelLOW = new Lookup_ward();
        $searchModel = new Lookup_wardSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost)
        {
            if ($modelLOW->load($this->request->post())) $this->actionLOW($modelLOW);
            else $modelLOW->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLOW($modelLOW){
        if ($modelLOW->save()) {
             $model_founded = Lookup_wardController::findModel($modelLOW->ward_uid);
             if(!empty($model_founded))
                 return Yii::$app->getResponse()->redirect(array('/lookup_ward/index', 
                     'ward' => $model_founded->ward_uid));
         }
    }

    public function InitSQL(){
        $Tables = array(
            "CREATE TABLE IF NOT EXISTS `lookup_ward` (
                `ward_uid` VARCHAR(64) NOT NULL,
                `ward_code` VARCHAR(20) UNIQUE NOT NULL,
                `ward_name` VARCHAR(50) NOT NULL,
                `sex` VARCHAR(20),
                `min_age` INT,
                `max_age` INT,
                PRIMARY KEY (`ward_uid`)
            );"
        );

        for($i=0; $i < count($Tables); $i++)
        {
            $sqlCommand = Yii::$app->db->createCommand($Tables[$i]);
            $sqlCommand->execute();    
        }
    }
    /**
     * Displays a single Lookup_ward model.
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
     * Creates a new Lookup_ward model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lookup_ward();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index', 'ward_uid' => $model->ward_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lookup_ward model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ward_uid Ward Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ward_uid)
    {
        $model = $this->findModel($ward_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ward_uid' => $model->ward_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lookup_ward model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ward_uid Ward Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ward_uid)
    {
        $this->findModel($ward_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lookup_ward model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ward_uid Ward Uid
     * @return Lookup_ward the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ward_uid)
    {
        if (($model = Lookup_ward::findOne(['ward_uid' => $ward_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
