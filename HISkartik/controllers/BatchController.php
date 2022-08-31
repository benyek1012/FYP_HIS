<?php

namespace app\controllers;

use app\models\Batch;
use app\models\New_user;
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
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

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
        $model = new Batch();
        $searchModel = new BatchSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $flag_delete = true;

        if(!(new New_user()) -> isAdmin()) echo $this->render('/site/no_access');

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $table = $model::chooseLookupType($model->lookup_type);
                $tableName = $table->tableName();
                $column = $table->attributes();
                $string_error = "";

                $model->file = UploadedFile::getInstance($model, 'file');
                $uploadExists = 0;

                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                $model->upload_datetime = $date->format('Y-m-d H:i:s');
           
                if($model->file) {  
                    $path = 'uploads/';
                    $model->file_import = $path .rand(10, 100). '-' .str_replace('', '-', $model->file->name);

                    $bulkInsertArray = array();
                    $random_date = Yii::$app->formatter->asDatetime(date("dmyyhis"), "php:dmYHis");
                    $random =  $random_date.rand(10, 100);
                    $userId = Yii::$app->user->identity->id;
                    $uploadExists = 1;
                }


                if($uploadExists && $model->validate()){
                    try{
                        $transaction = Yii::$app->db->beginTransaction();

                        $model->file->saveAs($model->file_import);
                        $handle = fopen($model->file_import, 'r');
                        if($handle){
                            $first_column_csv = fgetcsv($handle);
                            // filter empty column
                            $first_column_csv = array_filter($first_column_csv);

                            // remove first element
                            $col = $column;
                            array_shift($col);
                            // var_dump($col);
                            // exit;

                            // Check first column of CSV and database column name equal
                            if(count($first_column_csv) != count($col))
                            {
                                Yii::$app->session->setFlash('msg', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Validation error! ').' </strong> Lost column name !</div>');
                                
                                unlink(Yii::$app->basePath . '/web/' . $model->file_import);

                                return $this->redirect(['index']);
                            }
                            
                            $result_different_first_row = array_diff($first_column_csv, $col);
                    
                            if(!empty($result_different_first_row))
                            {
                                Yii::$app->session->setFlash('msg', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Validation error! ').' </strong> Invalid column name !</div>');

                                unlink(Yii::$app->basePath . '/web/' . $model->file_import);
        
                                return $this->redirect(['index']);
                            }

                            if($model->update_type == 'delete')
                            {
                                // retrieve all data from particular table for batch insert later
                                $array_from_database = $table::find()->asArray()->all();

                                // delete all the rows 
                                $table->deleteAll();
                            }
                               
                            $str = "";
                            $row = 2;
                            // read data lines 
                            for ($i = 0; $line = fgetcsv($handle ); ++$i) {
                                if(!empty($line[0]))
                                {
                                    $model_lookup_ward = Lookup_ward::findOne(['ward_code' =>  $line[0]]);

                                    if(!$model_lookup_ward):
                                        $model_lookup_ward = new Lookup_ward();
                                    endif;

                                    $model_lookup_ward->ward_uid =  Base64UID::generate(32);
                                    $model_lookup_ward->ward_code = $line[0];
                                    $model_lookup_ward->ward_name = $line[1];
                                    $model_lookup_ward->sex = $line[2];
                                    $model_lookup_ward->min_age = $line[3];
                                    $model_lookup_ward->max_age = $line[4];
    
                                    $valid = $model_lookup_ward->validate();
                                    $array_error = $model_lookup_ward->getFirstErrors();
                                 
                                    foreach($array_error as $error){
                                        $string_error .= "Row ".$row." : ".$error."<br/>";
                                    }
                                    
                                    if($valid) 
                                        $bulkInsertArray[] = [
                                            'ward_uid' =>  Base64UID::generate(32),
                                            'ward_code' => $line[0],
                                            'ward_name' => $line[1],
                                            'sex' =>   $line[2],
                                            'min_age' => $line[3],
                                            'max_age' => $line[4],
                                        ];
                                }
                                $row++;
                            }
                        }
                        fclose($handle);

                        $transaction->commit();
                    }catch(Exception $error){
                        print_r($error);
                        $transaction->rollback();
                    }      

                    if(!empty($bulkInsertArray))
                    {
                        // insert back to table
                        if($model->update_type == 'delete')
                            Yii::$app->db->createCommand()->batchInsert($tableName, $column, $array_from_database)->execute();    
                                              
                        // insert into batch table
                        $model->error = $string_error;
                        $model->approval1_responsible_uid = Yii::$app->user->identity->id;
                        $model->save();

                        if($string_error != "")
                        {
                            Yii::$app->session->setFlash('msg', '
                            <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <strong>'.Yii::t('app', 'Validation error!<br/> ').' </strong>'. $string_error.'</div>');
                        }
                        else $flag_delete = false;
                    }
                    else
                        Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Validation error!<br/> ').' </strong>All data from CSV file have been inserted! </div>');
                 
                    // delete file folder from PC
                    // if($flag_delete == true)
                    //     unlink(Yii::$app->basePath . '/web/' . $model->file_import);
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

    public function actionUpload()
    { 
        $model = Batch::findOne(Yii::$app->request->get('id'));

        return $this->renderPartial('/batch/view', ['model' => $model]);
    }

    public function actionExport()
    {
        $model = Batch::findOne(Yii::$app->request->get('id'));
        $content = preg_replace("<<br/>>","\r\n", $model->error);
        $filename = $model->id.'_ErrorMsg'. '.txt'; 
       
        $myfile = fopen($filename, "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: ". filesize("$filename").";");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/octet-stream; "); 
        header("Content-Transfer-Encoding: binary");
        readfile($filename);
       

    }
    public function actionExecute($id)
    {
        $model =  $this->findModel($id);

        $table = $model::chooseLookupType($model->lookup_type);
        $tableName = $table->tableName();
        $column = $table->attributes();

        $handle = fopen($model->file_import, 'r');
        // remove first row
        fgetcsv($handle);

        $model_lookup_ward = new Lookup_ward();
        if($model->update_type == 'delete')
            $model_lookup_ward->deleteAll();

        if($handle){
            // read data lines 
            for ($i = 0; $line = fgetcsv($handle ); ++$i) {
                if(!empty($line[0]))
                {
                    $model_lookup_ward = Lookup_ward::findOne(['ward_code' =>  $line[0]]);

                    if(!$model_lookup_ward):
                        $model_lookup_ward = new Lookup_ward();
                    endif;

                    $model_lookup_ward->ward_uid =  Base64UID::generate(32);
                    $model_lookup_ward->ward_code = $line[0];
                    $model_lookup_ward->ward_name = $line[1];
                    $model_lookup_ward->sex = $line[2];
                    $model_lookup_ward->min_age = $line[3];
                    $model_lookup_ward->max_age = $line[4];
                    $model_lookup_ward->save();
                }
            }
        }
        fclose($handle);

    
        // delete file folder from PC
        // unlink(Yii::$app->basePath . '/web/' . $model->file_import);

        $model->execute_responsible_uid = Yii::$app->user->identity->id;

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model->executed_datetime = $date->format('Y-m-d H:i:s');

        $model->save();

        Yii::$app->session->setFlash('msg', '
            <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            '.Yii::t('app', 'You have successfully import file '.$model->file_import.'.'). '</div>'
        );
    

        return $this->redirect(['index']);

    }

    public function actionApprove($id)
    {
       
        $model = $this->findModel($id);

        if(empty($model->approval1_responsible_uid))
            $model->approval1_responsible_uid = Yii::$app->user->identity->id;
        else $model->approval2_responsible_uid = Yii::$app->user->identity->id;

        $model->save();

        return $this->redirect(['index']);
    }

    public function actionDownload($id)
    {
       
        $model = $this->findModel($id);

        $path = Yii::$app->basePath . '/web/' . $model->file_import;

        $array = explode("/", $model->file_import);

        if (file_exists($path)) 
            return Yii::$app->response->sendFile($path, $array[1]);
        else 
        {
            Yii::$app->session->setFlash('msg', '
            <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            '.$array[1].' is not found !</div>');

            return $this->redirect(['index']);
        }
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