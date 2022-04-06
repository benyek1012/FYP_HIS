<?php

namespace app\controllers;

use app\models\Patient_information;
use app\models\Patient_informationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Patient_informationController implements the CRUD actions for Patient_information model.
 */
class Patient_informationController extends Controller
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
     * Lists all Patient_information models.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $searchModel = new Patient_informationSearch();
        // $dataProvider = $searchModel->search($this->request->queryParams);

        // return $this->render('index', [
        //     'searchModel' => $searchModel,
        //     'dataProvider' => $dataProvider,
        // ]);
    }

    /**
     * Displays a single Patient_information model.
     * @param string $patient_uid Patient Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        $model = new Patient_information();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                return $this->render('/site/index', [
                 'model' => $this->findModel($model->patient_uid)]);  
            }
        } else {
            $model->loadDefaultValues();
        }
    }

    /**
     * Creates a new Patient_information model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Patient_information();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'patient_uid' => $model->patient_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Patient_information model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $patient_uid Patient Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($patient_uid)
    {
        $model = $this->findModel($patient_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'patient_uid' => $model->patient_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Patient_information model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $patient_uid Patient Uid
     * @return Patient_information the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($patient_uid)
    {
        if (($model = Patient_information::findOne(['patient_uid' => $patient_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
