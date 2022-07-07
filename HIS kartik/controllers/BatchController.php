<?php

namespace app\controllers;

use app\models\Batch;
use app\models\BatchSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\web\UploadedFile;
use GpsLab\Component\Base64UID\Base64UID;
use Yii;

/**
 * BatchController implements the CRUD actions for Batch model.
 */
class BatchController extends Controller
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
     * Lists all Batch models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BatchSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $model = new Batch();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $uploadExists = 0;
                if($model->file){
                    $path = 'uploads/';
                    $model->file_import = $path .rand(10, 100). '-' .str_replace('', '-', $model->file->name);

                    $bulkInsertArray = array();
                    $random_date = Yii::$app->formatter->asDatetime(date("dmyyhis"), "php:dmYHis");
                    $random =  $random_date.rand(10, 100);
                    $userId = Yii::$app->user->identity->id;
                    $now = new Expression('NOW()');

                    $uploadExists = 1;
                }

                if($uploadExists){
                    $model->file->saveAs($model->file_import);
                    $handle = fopen($model->file_import, 'r');
                    if($handle){
                        $model->batch = $random;
                        if($model->save()){
                            while(($line = fgetcsv($handle, 1000, ",")) != FALSE){
                                $bulkInsertArray[] = [
                                    'ward_uid' => Base64UID::generate(32),
                                    'ward_code' => $line[0],
                                    'ward_name' => $line[1],
                                    'sex' =>   $random,
                                    'min_age' => 'a',
                                    'max_age' => 'a',
                                ];
                            }
                        }
                    }
                    fclose($handle);

                    $tableName = 'lookup_ward';
                    $col = [ 'ward_uid', 'ward_code', 'ward_name', 'sex', 'min_age', 'max_age'];
                    Yii::$app->db->createCommand()->batchInsert($tableName, $col, $bulkInsertArray)->execute();

                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Batch model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Batch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Batch();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $uploadExists = 0;
                if($model->file){
                    $path = 'uploads/';
                    $model->file_import = $path .rand(10, 100). '-' .str_replace('', '-', $model->file->name);

                    $bulkInsertArray = array();
                    $random_date = Yii::$app->formatter->asDatetime(date("dmyyhis"), "php:dmYHis");
                    $random =  $random_date.rand(10, 100);
                    $userId = Yii::$app->user->identity->id;
                    $now = new Expression('NOW()');

                    $uploadExists = 1;
                }

                if($uploadExists){
                    $model->file->saveAs($model->file_import);
                    $handle = fopen($model->file_import, 'r');
                    if($handle){
                        $model->batch = $random;
                        if($model->save()){
                            while(($line = fgetcsv($handle, 1000, ",")) != FALSE){
                                $bulkInsertArray[] = [
                                    'ward_uid' => Base64UID::generate(32),
                                    'ward_code' => $line[0],
                                    'ward_name' => $line[1],
                                    'sex' =>   $random,
                                    'min_age' => 'a',
                                    'max_age' => 'a',
                                ];
                            }
                        }
                    }
                    fclose($handle);

                    $tableName = 'lookup_ward';
                    $col = [ 'ward_uid', 'ward_code', 'ward_name', 'sex', 'min_age', 'max_age'];
                    Yii::$app->db->createCommand()->batchInsert($tableName, $col, $bulkInsertArray)->execute();

                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Batch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Batch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Batch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Batch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Batch::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
