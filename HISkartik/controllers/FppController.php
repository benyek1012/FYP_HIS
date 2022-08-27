<?php

namespace app\controllers;

use app\models\Fpp;
use app\models\FppSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FppController implements the CRUD actions for Fpp model.
 */
class FppController extends Controller
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
     * Lists all Fpp models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FppSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Fpp model.
     * @param string $kod Kod
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($kod)
    {
        return $this->render('view', [
            'model' => $this->findModel($kod),
        ]);
    }

    /**
     * Creates a new Fpp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Fpp();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'kod' => $model->kod]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Fpp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $kod Kod
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($kod)
    {
        $model = $this->findModel($kod);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'kod' => $model->kod]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Fpp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $kod Kod
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($kod)
    {
        $this->findModel($kod)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Fpp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $kod Kod
     * @return Fpp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($kod)
    {
        if (($model = Fpp::findOne(['kod' => $kod])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
