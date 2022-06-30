<?php

namespace app\controllers;

use Yii;
use app\models\New_user;
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
        $model = new Lookup_status();
        $searchModel = new Lookup_statusSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if(!(new New_user()) -> isClerkorAdmin()) echo $this->render('/site/no_access');
        if ($this->request->isPost && $model->load($this->request->post())) 
        {
            
            $checkDuplicatedCode = Lookup_status::findOne(['status_code' => $model->status_code]);
       
            if($model->validate() &&  empty( $checkDuplicatedCode))
            {
                // try catch of check row is inserted in SQL
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'status_uid' => $model->status_uid]);
            }
            else
            {
                Yii::$app->session->setFlash('error_status', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong>Status Code '.$model->status_code.' is duplicated. !</div>'
                );
                //$message = 'Code should not be duplicated.';
                //$model->addError('status_code', $message);
            }
           
        } 
        else 
        {
            $model->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
        $searchModel = new Lookup_statusSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost && $model->load($this->request->post())) 
        {
            
            $checkDuplicatedCode = Lookup_status::findOne(['status_code' => $model->status_code]);
       
            if($model->validate() &&  empty( $checkDuplicatedCode))
            {
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'status_uid' => $model->status_uid]);
            }
            else
            {
                Yii::$app->session->setFlash('error_status', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong>Status Code '.$model->status_code.' is duplicated. !</div>'
                );
            }
           
        } 
        
        $model->loadDefaultValues();

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
