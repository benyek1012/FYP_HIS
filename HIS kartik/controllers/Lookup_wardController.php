<?php

namespace app\controllers;

use Yii;
use app\models\New_user;
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
        $model = new Lookup_ward();
        $searchModel = new Lookup_wardSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if(!(new New_user()) -> isClerkorAdmin()) echo $this->render('/site/no_access');
        if ($this->request->isPost && $model->load($this->request->post()))
        {
            $checkDuplicatedCode = Lookup_ward::findOne((['ward_code' => $model->ward_code]));

            if($model->validate() &&  empty( $checkDuplicatedCode))
            {
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'ward_uid' => $model->ward_uid]);
            }
            else
            {
                Yii::$app->session->setFlash('error_ward', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong> Ward Code '.$model->ward_code.' is duplicated. !</div>'
                );
                //$message = 'Code should not be duplicated.';
                //$model->addError('ward_code', $message);
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
        $searchModel = new Lookup_wardSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost && $model->load($this->request->post())) {

            $checkDuplicatedCode = Lookup_ward::findOne((['ward_code' => $model->ward_code]));

            if (empty($checkDuplicatedCode))
            {
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'ward_uid' => $model->ward_uid]);
            }
            else
            {
                Yii::$app->session->setFlash('error_ward', '
                <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                <strong>Validation error! </strong> Ward Code '.$model->ward_code.' is duplicated. !</div>'
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
