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
        $model = new Lookup_treatment();
        $searchModel = new Lookup_treatmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost && $model->load($this->request->post())) {
            
            $checkDuplicatedCode = Lookup_treatment::findOne(['treatment_code' => $model->treatment_code]);
       
            if($model->validate() &&  empty( $checkDuplicatedCode))
            {
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'treatment_uid' => $model->treatment_uid]);
            }
            else
            {
                Yii::$app->session->setFlash('error_treatment', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong>Treatment Code '.$model->treatment_code.' is duplicated. !</div>'
                );
                //$message = 'Code should not be duplicated.';
                //$model->addError('treatment_code', $message);
            }
           
        } 
        else {
            $model->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
        $searchModel = new Lookup_treatmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost && $model->load($this->request->post())) {
            
            $checkDuplicatedCode = Lookup_treatment::findOne(['treatment_code' => $model->treatment_code]);
       
            if($model->validate() &&  empty( $checkDuplicatedCode))
            {
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'treatment_uid' => $model->treatment_uid]);
            }
            else
            {
                Yii::$app->session->setFlash('error_treatment', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong>Treatment Code '.$model->treatment_code.' is duplicated. !</div>'
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
