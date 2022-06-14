<?php

namespace app\controllers;

use Yii;
use app\models\New_user;
use app\models\New_userSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

/**
 * NewuserController implements the CRUD actions for Newuser model.
 */
class New_userController extends Controller
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
            'user' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => New_user::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;  
                }
            ]
        ]);
    }

    /**
     * Lists all Newuser models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $modeluser = new New_user();
        $searchModel = new New_userSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost)
        {
            if ($modeluser->load($this->request->post())) $this->actionuser($modeluser);
            else $modeluser->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionuser($modeluser){
        if ($modeluser->save()) {
             $model_founded = New_userController::findModel($modeluser->user_uid);
             if(!empty($model_founded))
                 return Yii::$app->getResponse()->redirect(array('/new_user/index', 
                     'user' => $model_founded->user_uid));
         }
    }    

    /**
     * Displays a single Newuser model.
     * @param string $user_uid User Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($user_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($user_uid),
        ]);
    }

    /**
     * Creates a new Newuser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new New_user();
        $searchModel = new New_userSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if ($this->request->isPost && $model->load($this->request->post())){

            $checkDuplicatedUser = New_user::findOne(['username' => $model->username, 'user_uid' => $model->user_uid]);
            if($model->validate() && empty($checkDuplicatedUser))
            {
                $model->user_password = New_user::hashPassword($model->user_password); // Hash the password before you save it.
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'user_id' => $model->user_uid]);
            }
            else
            {
                Yii::$app->session->setFlash('error_user', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong> Username '.$model->username.' is duplicated. !</div>'
                );
            }
        }else {
            $model->loadDefaultValues();
        }
        
        return $this->render('index', [
           'model' => $model,
           'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

        /*if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index', 'user_uid' => $model->user_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);*/
    }

    /**
     * Updates an existing Newuser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $user_uid User Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($user_uid)
    {
        $model = $this->findModel($user_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_uid' => $model->user_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Newuser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $user_uid User Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($user_uid)
    {
        $this->findModel($user_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Newuser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $user_uid User Uid
     * @return New_user the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_uid)
    {
        if (($model = New_user::findOne(['user_uid' => $user_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
