<?php

namespace app\controllers;

use app\models\Reminder;
use app\models\New_user;
use app\models\ReminderSearch;
use app\models\Patient_admission;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

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
        if ($this->request->get() && !empty($_GET['function'])) {

            $model = new Reminder();
            $userid = Yii::$app->user->identity->name;
            $table = $model::batchCreate($userid);
            echo $table;
            //$this->$username::getId($table);
            //return $username->_toString($table);
            //return $_GET['function'];
            //return $this->$table->redirect(['index']);
        }

        $searchModel = new ReminderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*public function actionBatch($responsible_uid)
    {
        $model = $this->findModel($responsible_uid);

        $model->responsible_uid = Yii::$app->user->identity->responsible_uid;
         
        $model::batchCreate($responsible_uid);

        return $this->redirect(['index']);
    }*/

    /**
     * Displays a single Reminder model.
     * @param string $batch_date Batch Date
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($batch_date)
    {
        return $this->render('view', [
            'model' => $this->findModel($batch_date),
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
                return $this->redirect(['view', 'batch_date' => $model->batch_date]);
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
     * @param string $batch_date Batch Date
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($batch_date)
    {
        $model = $this->findModel($batch_date);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'batch_date' => $model->batch_date]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Reminder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $batch_date Batch Date
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($batch_date)
    {
        $this->findModel($batch_date)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Reminder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $batch_date Batch Date
     * @return Reminder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($batch_date)
    {
        if (($model = Reminder::findOne(['batch_date' => $batch_date])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
