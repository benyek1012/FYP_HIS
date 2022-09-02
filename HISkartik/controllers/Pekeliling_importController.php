<?php

namespace app\controllers;

use app\models\Lookup_department;
use app\models\Lookup_fpp;
use app\models\Lookup_status;
use app\models\Lookup_treatment;
use app\models\Pekeliling_import;
use app\models\Pekeliling_importSearch;
use app\models\New_user;
use app\models\Lookup_ward;
use app\models\Variable;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\web\UploadedFile;
use GpsLab\Component\Base64UID\Base64UID;
use Yii;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;

/**
 * Pekeliling_importController implements the CRUD actions for Pekeliling_import model.
 */
class Pekeliling_importController extends Controller
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
     * Lists all Pekeliling_import models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Pekeliling_import();
        $searchModel = new Pekeliling_importSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $flag_delete = true;

        if(!(new New_user()) -> isAdmin()) return $this->render('/site/no_access');
        
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

                $model->pekeliling_uid = Base64UID::generate(32);

                if($model->update_type == 'delete')
                {
                    // retrieve all data from particular table for batch insert later
                    $array_from_database = $table::find()->asArray()->all();
                    // delete all the rows 
                    $table->deleteAll();

                }
           
                if($model->file) {  
                    $path = 'uploads/';
                    $model->file_import = $path .rand(10, 100). '-' .str_replace('', '-', $model->file->name);

                    $random_date = Yii::$app->formatter->asDatetime(date("dmyyhis"), "php:dmYHis");
                    $uploadExists = 1;
                }

                if($uploadExists && $model->validate()){
                    try{
                        $transaction = Yii::$app->db->beginTransaction();

                        // create the folder if folder does not existed, then save the file 
                        $path = Yii::$app->basePath . '/web/uploads';
                        if (\yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true)) {
                            $model->file->saveAs($model->file_import);
                        }
                     
                        // file validation
                        $handle = fopen($model->file_import, 'r');
                        if($handle){
                            $first_column_csv = fgetcsv($handle);

                            $validate_header = Pekeliling_import::validateHeader($first_column_csv, $column);

                            if(!$validate_header)
                            {
                                Yii::$app->session->setFlash('msg', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Validation error! ').' </strong> Invalid column name ! <br/> 
                                    Column name must matched with database header name</div>');
                                
                                unlink(Yii::$app->basePath . '/web/' . $model->file_import);
                   
                                return $this->redirect(['index']);
                            }
                               
                            $row = 2;
                            $duplicateInfo = array();
                            $arrdup = "";

                            // file validation, check duplicated codes in whole csv file
                            while (($line = fgetcsv($handle)) !== false) 
                            {
                                if(!empty($line[0]))
                                {
                                    $key = $line[0];
                                    if (isset($duplicateInfo[$key])) {
                                        $arrdup .=  '<strong>'.$first_column_csv[0].' '.$key.'</strong> '.Yii::t("app", " is duplicated with the row ").$duplicateInfo[$key].
                                        Yii::t("app", " and the row ").$row.". <br/>";
                                    }
                                    $duplicateInfo[$key] = $row;
                                }
                               $row++;
                            }   
                        }
                        fclose($handle);

                        if(!empty($arrdup))
                            $string_error .= $arrdup."<br/>";

                        // database model validation
                        $handle = fopen($model->file_import, 'r');
                        fgetcsv($handle);
                        if($handle){
                            $row = 2;
                            while (($line = fgetcsv($handle)) !== false) 
                            {   
                                // ignore blank lines
                                if ($line[0] == NULL && $line[1] == NULL)  
                                {
                                    $row++;
                                    continue;
                                } 
                                $special_character_error = $model::validateSpecialCharacter($line, $first_column_csv);
                                $model_after_validate = $model::validateModel($model->lookup_type, $line);
                                $valid = $model_after_validate->validate();
                                $array_error = $model_after_validate->getFirstErrors();

                                if($special_character_error != "")
                                    $string_error .= Yii::t("app", "Row ").$row." : ".$special_character_error."<br/>";
                                foreach($array_error as $error){
                                    $string_error .= Yii::t("app", "Row ").$row." : ".$error."<br/>";
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

                    if(!empty($model_after_validate))
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
                            <strong>'.Yii::t('app', 'Validation error!').' </strong><br/>'.  StringHelper::truncateWords($string_error, 50).'</div>');
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

    public function actionExecute($id)
    {
        $model =  $this->findModel($id);

        $model_read_only = Variable::find()->one();
        $model_read_only->read_only = 1;
        $model_read_only->save();

        $table = $model::chooseLookupType($model->lookup_type);
        $tableName = $table->tableName();
        $column = $table->attributes();

        $handle = fopen($model->file_import, 'r');
        // remove first row
        fgetcsv($handle);

        if($model->update_type == 'delete')
            $table->deleteAll();

        if($handle){
            // read data lines 
            for ($i = 0; $line = fgetcsv($handle ); ++$i) {
                if(!empty($line[0]))
                {
                    $model_after_validate = $model::validateModel($model->lookup_type, $line);
                    $model_after_validate->save();
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

        if($model->save())
        {
            $model_read_only->read_only = 0;
            $model_read_only->save();

            Yii::$app->session->setFlash('msg', '
                <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                '.Yii::t('app', 'You have successfully import file '.$model->file_import.'.'). '</div>'
            );
        
            return $this->redirect(['index']);
        }

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

    public function actionUpload($id)
    { 
        $model = $this->findModel($id);
        return $this->renderPartial('/pekeliling_import/view', ['model' => $model]);
    }

    public function actionExport($id)
    {
        $model = $this->findModel($id);
        $content = preg_replace("<<br/>>","\r\n", $model->error);

        $array = explode("/", $model['file_import']);
        $array = explode(".csv", $array[1]);
        
        $filename = $array[0].'_Error'. '.txt'; 
       
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
    
    public function actionExport2()
    {   
        $exporter = new CsvGrid([
            'dataProvider' => new ActiveDataProvider([
                'query' => Lookup_ward::find(),
                'pagination' => [
                    'pageSize' => 100, // export batch size
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'ward_code',
                    'label' => Yii::t('app','Ward Code'),
                ],
                [
                    'attribute' => 'ward_name',
                    'label' => Yii::t('app','Ward Name'),
                ],
                [
                    'attribute' => 'sex',
                    'label' => Yii::t('app','Sex'),
                ],
                [
                    'attribute' => 'min_age',
                    'label' => Yii::t('app','Min Age'),
                ],
                [
                    'attribute' => 'max_age',
                    'label' => Yii::t('app','Max Age'),
                ],
            ],
        ]);
        
        return $exporter->export()->send('Lookup_ward_from_db.csv');
    }

    public function actionExport3()
    {   
        $exporter = new CsvGrid([
            'dataProvider' => new ActiveDataProvider([
                'query' => Lookup_status::find(),
                'pagination' => [
                    'pageSize' => 100, // export batch size
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'status_code',
                    'label' => Yii::t('app','Status Code'),
                ],
                [
                    'attribute' => 'status_description',
                    'label' => Yii::t('app','Status Description'),
                ],
                [
                    'attribute' => 'class_1a_ward_cost',
                    'label' =>  Yii::t('app','Class 1a Ward Cost'),
                ],
                [
                    'attribute' => 'class_1b_ward_cost',
                    'label' => Yii::t('app','Class 1b Ward Cost'),
                ],
                [
                    'attribute' => 'class_1c_ward_cost',
                    'label' => Yii::t('app','Class 1c Ward Cost'),
                ],
                [
                    'attribute' => 'class_2_ward_cost',
                    'label' => Yii::t('app','Class 2 Ward Cost'),
                ],
                [
                    'attribute' => 'class_3_ward_cost',
                    'label' => Yii::t('app','Class 3 Ward Cost'),
                ],
            ],
        ]);
        
        return $exporter->export()->send('Lookup_status_from_db.csv');		
    }

    public function actionExport4()
    {   
        $exporter = new CsvGrid([
            'dataProvider' => new ActiveDataProvider([
                'query' => Lookup_treatment::find(),
                'pagination' => [
                    'pageSize' => 100, // export batch size
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'treatment_code',
                    'label' => Yii::t('app','Treatment Code'),
                ],
                [
                    'attribute' => 'treatment_name',
                    'label' => Yii::t('app','Treatment Name'),
                ],
                [
                    'attribute' => 'class_1_cost_per_unit',
                    'label' => Yii::t('app','Class  1 Cost Per Unit'),
                ],
                [
                    'attribute' => 'class_2_cost_per_unit',
                    'label' =>  Yii::t('app','Class  2 Cost Per Unit'),
                ],
                [
                    'attribute' => 'class_3_cost_per_unit',
                    'label' => Yii::t('app','Class  3 Cost Per Unit'),
                ],
                [
                    'attribute' => 'class_Daycare_FPP_per_unit',
                    'label' => Yii::t('app','Class DayCare FPP Cost Per Unit'),
                ],
            ],
        ]);
        
        return $exporter->export()->send('Lookup_treatment_from_db.csv');		
    }

    public function actionExport5()
    {   
        $exporter = new CsvGrid([
            'dataProvider' => new ActiveDataProvider([
                'query' => Lookup_department::find(),
                'pagination' => [
                    'pageSize' => 100, // export batch size
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'department_code',
                    'label' => Yii::t('app','Department Code'),
                ],
                [
                    'attribute' => 'department_name',
                    'label' => Yii::t('app','Department Name'),
                ],
                [
                    'attribute' => 'phone_number',
                    'label' => Yii::t('app','Phone Number'),
                ],
                [
                    'attribute' => 'address1',
                    'label' =>Yii::t('app','Address 1'),
                ],
                [
                    'attribute' => 'address2',
                    'label' => Yii::t('app','Address 2'),
                ],
                [
                    'attribute' => 'address3',
                    'label' => Yii::t('app','Address 3'),
                ],
            ],
        ]);
        
        return $exporter->export()->send('Lookup_department_from_db.csv');		
    }

    public function actionExport6()
    {   
        $exporter = new CsvGrid([
            'dataProvider' => new ActiveDataProvider([
                'query' => Lookup_fpp::find(),
                'pagination' => [
                    'pageSize' => 100, // export batch size
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'kod',
                    'label' =>'Kod',
                ],
                [
                    'attribute' => 'name',
                    'label' => Yii::t('app', 'Name'),
                ],
                [
                    'attribute' => 'min_cost_per_unit',
                    'label' =>Yii::t('app', 'Min Cost Per Unit'),
                ],
                [
                    'attribute' => 'max_cost_per_unit',
                    'label' => Yii::t('app', 'Max Cost Per Unit'),
                ],
            ],
        ]);
        
        return $exporter->export()->send('Lookup_fpp_from_db.csv');		
    }

    /**
     * Displays a single Pekeliling_import model.
     * @param string $pekeliling_uid Pekeliling Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pekeliling_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($pekeliling_uid),
        ]);
    }

    /**
     * Creates a new Pekeliling_import model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Pekeliling_import();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'pekeliling_uid' => $model->pekeliling_uid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Pekeliling_import model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $pekeliling_uid Pekeliling Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($pekeliling_uid)
    {
        $model = $this->findModel($pekeliling_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pekeliling_uid' => $model->pekeliling_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Pekeliling_import model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $pekeliling_uid Pekeliling Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($pekeliling_uid)
    {
        $this->findModel($pekeliling_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Pekeliling_import model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $pekeliling_uid Pekeliling Uid
     * @return Pekeliling_import the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($pekeliling_uid)
    {
        if (($model = Pekeliling_import::findOne(['pekeliling_uid' => $pekeliling_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}