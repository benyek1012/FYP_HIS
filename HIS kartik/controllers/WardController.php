<?php

namespace app\controllers;

use Yii;
use app\models\Ward;
use app\models\Model;
use app\models\WardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;
use GpsLab\Component\Base64UID\Base64UID;


/**
 * WardController implements the CRUD actions for Ward model.
 */
class WardController extends Controller
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
                        'delete' => ['GET'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Ward models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new WardSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ward model.
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
     * Creates a new Ward model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Ward();
        $model->ward_uid = Base64UID::generate(32);
        $model->ward_start_datetime = date("d-m-Y H:i:s");
        $model->ward_end_datetime = date("d-m-Y H:i:s");
        $model->loadDefaultValues();
        $model-> save();
        return $this->redirect(['bill/create', 'rn' =>  Yii::$app->request->get('rn')]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Ward model.
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
     * Deletes an existing Ward model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ward_uid Ward Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ward_uid)
    {
        $this->findModel($ward_uid)->delete();

        if(!empty( Yii::$app->request->get('bill_uid'))){ 
            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                'bill_uid' => Yii::$app->request->get('bill_uid'), 'rn' => Yii::$app->request->get('rn'))); 
        } 
        else{
            return Yii::$app->getResponse()->redirect(array('/bill/create', 
                'rn' => Yii::$app->request->get('rn')));  
        }
    }

    /**
     * Finds the Ward model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ward_uid Ward Uid
     * @return Ward the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ward_uid)
    {
        if (($model = Ward::findOne(['ward_uid' => $ward_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
