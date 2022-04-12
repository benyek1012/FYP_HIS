<?php

namespace app\controllers;

use app\models\Treatment_details;
use app\models\Treatment_detailsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Treatment_detailsController implements the CRUD actions for Treatment_details model.
 */
class Treatment_detailsController extends Controller
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
     * Lists all Treatment_details models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new Treatment_detailsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Treatment_details model.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($treatment_details_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($treatment_details_uid),
        ]);
    }

    /**
     * Creates a new Treatment_details model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Treatment_details();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'treatment_details_uid' => $model->treatment_details_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Treatment_details model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($treatment_details_uid)
    {
        $model = $this->findModel($treatment_details_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'treatment_details_uid' => $model->treatment_details_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Treatment_details model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($treatment_details_uid)
    {
        $this->findModel($treatment_details_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Treatment_details model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $treatment_details_uid Treatment Details Uid
     * @return Treatment_details the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($treatment_details_uid)
    {
        if (($model = Treatment_details::findOne(['treatment_details_uid' => $treatment_details_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
