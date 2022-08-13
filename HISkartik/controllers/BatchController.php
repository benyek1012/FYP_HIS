<?php

namespace app\controllers;

use app\models\Batch;
use app\models\BatchSearch;
use app\models\Lookup_ward;
use Codeception\Step\Skip;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\web\UploadedFile;
use GpsLab\Component\Base64UID\Base64UID;
use Yii;
use yii\db\Query;

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
           
                if($model->file) {  
                    $path = 'uploads/';
                    $model->file_import = $path .rand(10, 100). '-' .str_replace('', '-', $model->file->name);

                    $bulkInsertArray = array();
                    $random_date = Yii::$app->formatter->asDatetime(date("dmyyhis"), "php:dmYHis");
                    $random =  $random_date.rand(10, 100);
                    $userId = Yii::$app->user->identity->id;
                    $now = new Expression('NOW()');

                    $uploadExists = 1;
                }

                $table = new Lookup_ward();
                $tableName = $table->tableName();
                $col = $table->attributes();

                if($uploadExists && $model->validate()){
                    try{
                        $transaction = Yii::$app->db->beginTransaction();

                        $model->file->saveAs($model->file_import);
                        $handle = fopen($model->file_import, 'r');
                        if($handle){
                            $model->batch = $random;
                            $first_column_csv = fgetcsv($handle);

                            // Check first column of CSV and database column name equal
                            if(count(array_filter($first_column_csv)) != count($col))
                            {
                                Yii::$app->session->setFlash('msg', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Validation error! ').' </strong> Lost column name !</div>');
        
                                return $this->redirect(['index']);
                            }

                            $result_different_first_row = array_diff($first_column_csv, $col);
                    
                            if(!empty($result_different_first_row))
                            {
                                Yii::$app->session->setFlash('msg', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Validation error! ').' </strong> Invalid column name !</div>');
        
                                return $this->redirect(['index']);
                            }
            
                            $string_error = "";
                            // read data lines 
                            while(($line = fgetcsv($handle, 1000, ",")) != FALSE){
                                $model_lookup_ward = new Lookup_ward();
                                $model_lookup_ward->ward_uid = $line[0];
                                $model_lookup_ward->ward_code = $line[1];
                                $model_lookup_ward->ward_name = $line[2];
                                $model_lookup_ward->sex = $line[3];
                                $model_lookup_ward->min_age = $line[4];
                                $model_lookup_ward->max_age = $line[5];

                                $valid = $model_lookup_ward->validate();
                                $array_error = $model_lookup_ward->getFirstErrors();
                             
                                foreach($array_error as $error){
                                    $string_error .= $error."<br/>";
                                }
                                
                                if($valid) 
                                    $bulkInsertArray[] = [
                                        'ward_uid' => $line[0],
                                        'ward_code' => $line[1],
                                        'ward_name' => $line[2],
                                        'sex' =>   $line[3],
                                        'min_age' => $line[4],
                                        'max_age' => $line[5]
                                    ];
                            }
                        }
                        fclose($handle);

                        $transaction->commit();
                    }catch(Exception $error){
                        print_r($error);
                        $transaction->rollback();
                    }      
                    
                    // $array_all = Lookup_ward::find()
                    //             ->select(['ward_code'])
                    //             ->column();
                    
                    // $array_ward_code = array();
                    // foreach($bulkInsertArray as $i) {
                    //     array_push($array_ward_code, $i['ward_code']);
                    // }

                    // compare two tables
                    // $result = array_intersect($array_all, $array_ward_code);

                    
                    // insert into lookup table
                    Yii::$app->db->createCommand()->batchInsert($tableName, $col, $bulkInsertArray)->execute();
                    
                    // insert into batch table
                    $model->save();

                    if($string_error != "")
                    {
                        Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Validation error!<br/> ').' </strong>'. $string_error.'</div>');
                    }
                 

                    // delete file folder from PC
                    unlink(Yii::$app->basePath . '/web/' . $model->file_import);
                }

                return $this->redirect(['index']);
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