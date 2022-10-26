<?php

namespace app\controllers;

use app\models\Inpatient_treatment;
use app\models\Inpatient_treatmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Inpatient_treatmentController implements the CRUD actions for Inpatient_treatment model.
 */
class Inpatient_treatmentController extends Controller
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
     * Lists all Inpatient_treatment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new Inpatient_treatmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Inpatient_treatment model.
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
     * Creates a new Inpatient_treatment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Inpatient_treatment();

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
     * Updates an existing Inpatient_treatment model.
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
     * Deletes an existing Inpatient_treatment model.
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
     * Finds the Inpatient_treatment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $inpatient_treatment_uid Inpatient Treatment Uid
     * @return Inpatient_treatment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($inpatient_treatment_uid)
    {
        if (($model = Inpatient_treatment::findOne(['inpatient_treatment_uid' => $inpatient_treatment_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
