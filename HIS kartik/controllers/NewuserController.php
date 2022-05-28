<?php

namespace app\controllers;

use Yii;
use app\models\Newuser;
use app\models\NewuserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

/**
 * NewuserController implements the CRUD actions for Newuser model.
 */
class NewuserController extends Controller
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
                'modelClass' => Newuser::className(),                   // the update model class
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
        $modeluser = new Newuser();
        $searchModel = new NewuserSearch();
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
             $model_founded = NewuserController::findModel($modeluser->user_uid);
             if(!empty($model_founded))
                 return Yii::$app->getResponse()->redirect(array('/newuser/index', 
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
        $model = new Newuser();
        if ($this->request->isPost && $model->load($this->request->post())){

            $checkDuplicatedUser = NewUser::findOne(['username' => $model->username, 'user_uid' => $model->user_uid]);
            if(empty($checkDuplicatedUser))
            {
                $model->user_password = NewUser::hashPassword($model->user_password); // Hash the password before you save it.
                $model->save();
                return $this->redirect(['index', 'user_id' => $model->user_uid]);
            }
            else
            {
                $message = 'User should not be duplicated.';
                $model->addError('username',$message);
            }
        }else {
            $model->loadDefaultValues();
        }
        
        return $this->render('create', [
           'model' => $model,
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
     * @return Newuser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_uid)
    {
        if (($model = Newuser::findOne(['user_uid' => $user_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
