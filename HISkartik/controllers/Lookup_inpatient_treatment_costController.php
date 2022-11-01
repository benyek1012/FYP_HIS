<?php

namespace app\controllers;

use app\models\Lookup_inpatient_treatment_cost;
use app\models\Lookup_inpatient_treatment_costSearch;
use app\models\New_user;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Lookup_inpatient_treatment_costController implements the CRUD actions for Lookup_inpatient_treatment_cost model.
 */
class Lookup_inpatient_treatment_costController extends Controller
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
     * Lists all Lookup_inpatient_treatment_cost models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Lookup_inpatient_treatment_cost();
        $searchModel = new Lookup_inpatient_treatment_costSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if(!(new New_user()) -> isCashierorAdminorClerk()) echo $this->render('/site/no_access');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lookup_inpatient_treatment_cost model.
     * @param string $inpatient_treatment_uid Inpatient Treatment Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($inpatient_treatment_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($inpatient_treatment_uid),
        ]);
    }

    /**
     * Creates a new Lookup_inpatient_treatment_cost model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lookup_inpatient_treatment_cost();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'inpatient_treatment_uid' => $model->inpatient_treatment_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lookup_inpatient_treatment_cost model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $inpatient_treatment_uid Inpatient Treatment Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($inpatient_treatment_uid)
    {
        $model = $this->findModel($inpatient_treatment_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'inpatient_treatment_uid' => $model->inpatient_treatment_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lookup_inpatient_treatment_cost model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $inpatient_treatment_uid Inpatient Treatment Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($inpatient_treatment_uid)
    {
        $this->findModel($inpatient_treatment_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lookup_inpatient_treatment_cost model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $inpatient_treatment_uid Inpatient Treatment Uid
     * @return Lookup_inpatient_treatment_cost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($inpatient_treatment_uid)
    {
        if (($model = Lookup_inpatient_treatment_cost::findOne(['inpatient_treatment_uid' => $inpatient_treatment_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
