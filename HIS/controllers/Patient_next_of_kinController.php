<?php

namespace app\controllers;

use app\models\Patient_next_of_kin;
use app\models\Patient_next_of_kinSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Patient_next_of_kinController implements the CRUD actions for Patient_next_of_kin model.
 */
class Patient_next_of_kinController extends Controller
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
     * Lists all Patient_next_of_kin models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new Patient_next_of_kinSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Patient_next_of_kin model.
     * @param string $nok_uid Nok Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($nok_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($nok_uid),
        ]);
    }

    /**
     * Creates a new Patient_next_of_kin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Patient_next_of_kin();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'nok_uid' => $model->nok_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Patient_next_of_kin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $nok_uid Nok Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($nok_uid)
    {
        $model = $this->findModel($nok_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'nok_uid' => $model->nok_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Patient_next_of_kin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $nok_uid Nok Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($nok_uid)
    {
        $this->findModel($nok_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Patient_next_of_kin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $nok_uid Nok Uid
     * @return Patient_next_of_kin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($nok_uid)
    {
        if (($model = Patient_next_of_kin::findOne(['nok_uid' => $nok_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function findModel_patient_uid($patient_uid)
    {
        if (($model = Patient_next_of_kin::findOne(['patient_uid' => $patient_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
