<?php

namespace app\controllers;

use app\models\Reminder;
use app\models\ReminderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReminderController implements the CRUD actions for Reminder model.
 */
class ReminderController extends Controller
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
     * Lists all Reminder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ReminderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Reminder model.
     * @param string $batch_datetime Batch datetime
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($batch_datetime)
    {
        return $this->render('view', [
            'model' => $this->findModel($batch_datetime),
        ]);
    }

    /**
     * Creates a new Reminder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Reminder();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'batch_datetime' => $model->batch_datetime]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Reminder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $batch_datetime batch_datetime
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($batch_datetime)
    {
        $model = $this->findModel($batch_datetime);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'batch_datetime' => $model->batch_datetime]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Reminder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $batch_datetime batch_datetime
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($batch_datetime)
    {
        $this->findModel($batch_datetime)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Reminder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $batch_datetime batch_datetime
     * @return Reminder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($batch_datetime)
    {
        if (($model = Reminder::findOne(['batch_datetime' => $batch_datetime])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
