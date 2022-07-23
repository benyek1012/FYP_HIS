<?php

namespace app\controllers;

use Yii;
use app\models\New_user;
use app\models\Lookup_general;
use app\models\Lookup_generalSearch;
use app\models\Patient_information;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

/**
 * Lookup_generalController implements the CRUD actions for Lookup_general model.
 */
class Lookup_generalController extends Controller
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
            'lookup' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => Lookup_general::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;  
                }
            ]
        ]);
    }

    /**
     * Lists all Lookup_general models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Lookup_general();
        $searchModel = new Lookup_generalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if(!(new New_user()) -> isCashierorAdminorClerk()) echo $this->render('/site/no_access');
        if ($this->request->isPost && $model->load($this->request->post())) {
            
            $checkDuplicatedCode = Lookup_general::findOne(['code' => $model->code, 'category' => $model->category]);
       
            if($model->validate() && empty( $checkDuplicatedCode))
            {
                // try catch of check row is inserted in SQL
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'lookup_general_uid' => $model->lookup_general_uid]);
            }
            else
            {
                // set the flash message
                Yii::$app->session->setFlash('msg', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong> Code '.$model->code.' is duplicated in category '.$model->category.' !</div>'
                );
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
     * Displays a single Lookup_general model.
     * @param string $lookup_general_uid Lookup General Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lookup_general_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($lookup_general_uid),
        ]);
    }

    /**
     * Creates a new Lookup_general model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lookup_general();

        if ($this->request->isPost && $model->load($this->request->post())) {
            
            $checkDuplicatedCode = Lookup_general::findOne(['code' => $model->code, 'category' => $model->category]);
            // var_dump($checkDuplicatedCode);
            // exit();
            if(empty( $checkDuplicatedCode))
            {
                $model->save();
                return $this->redirect(['index', 'lookup_general_uid' => $model->lookup_general_uid]);
            }
            else
            {
                $message = 'Code should not be duplicated.';
                $model->addError('code', $message);
            }
           
        } 
        else 
        {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lookup_general model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $lookup_general_uid Lookup General Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lookup_general_uid)
    {
        $model = $this->findModel($lookup_general_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lookup_general_uid' => $model->lookup_general_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lookup_general model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $lookup_general_uid Lookup General Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lookup_general_uid)
    {
        $this->findModel($lookup_general_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lookup_general model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $lookup_general_uid Lookup General Uid
     * @return Lookup_general the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lookup_general_uid)
    {
        if (($model = Lookup_general::findOne(['lookup_general_uid' => $lookup_general_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
