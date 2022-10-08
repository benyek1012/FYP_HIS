<?php

namespace app\controllers;

use Yii;
use app\models\New_user;
use app\models\Cancellation;
use app\models\CancellationSearch;
use app\models\Patient_admission;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CancellationController implements the CRUD actions for Cancellation model.
 */
class CancellationController extends Controller
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

    /**
     * Lists all Cancellation models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CancellationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cancellation model.
     * @param string $cancellation_uid Cancellation Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cancellation_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($cancellation_uid),
        ]);
    }

    /**
     * Creates a new Cancellation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Cancellation();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // return $this->redirect(['view', 'cancellation_uid' => $model->cancellation_uid]);
                return $this->redirect(['receipt/create', 'rn' => Yii::$app->request->get('rn'), 'cancellation' => $model->cancellation_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Cancellation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $cancellation_uid Cancellation Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($cancellation_uid)
    {
        $model = $this->findModel($cancellation_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cancellation_uid' => $model->cancellation_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Cancellation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $cancellation_uid Cancellation Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($cancellation_uid)
    {
        $this->findModel($cancellation_uid)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleted(){
        $model_bill = Cancellation::findAll(['table' => 'bill']);
        $model_receipt = Cancellation::findAll(['table' => 'receipt']);

        if(!(new New_user()) -> isAdmin()) return $this->render('/site/no_access');
        
        return $this->render('deleted', [
            'model_bill' => $model_bill,
            'model_receipt' => $model_receipt,
        ]);
    }

    /**
     * Finds the Cancellation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $cancellation_uid Cancellation Uid
     * @return Cancellation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($cancellation_uid)
    {
        if (($model = Cancellation::findOne(['cancellation_uid' => $cancellation_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
