<?php

namespace app\controllers;
require 'vendor/autoload.php';

use app\models\Bill;
use Yii;
use app\models\Cancellation;
use app\models\DateFormat;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\Patient_admissionSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii2tech\csvgrid\CsvGrid;


use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use app\models\PrintForm;
use app\models\Reminder;
use DateTime;

/**
 * Patient_admissionController implements the CRUD actions for Patient_admission model.
 */
class Patient_admissionController extends Controller
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

    public function actionCode($rn){
        $model = Patient_admission::findOne(['rn' => $rn]);
        echo Json::encode($model);
    }

    public function actionCancellation($rn)
    {
        $model_cancellation = new Cancellation();

        if($this->request->isPost && $model_cancellation->load($this->request->post())){
            $model_cancellation->cancellation_uid = $rn;
            $model_cancellation->table = 'admission';
            $model_cancellation->responsible_uid = Yii::$app->user->identity->getId();
            
            if($model_cancellation->validate()){
                $rows = (new \yii\db\Query())
                    ->select(['rn'])
                    ->from('patient_admission')
                    ->where(['type' => Yii::$app->request->get('type')])
                    ->all();
                    $SID = "1" + count($rows);
        
                if(Yii::$app->request->get('type') == 'Normal')
                    $new_rn = date('Y')."/".sprintf('%06d', $SID);
                else $new_rn = date('Y')."/9".sprintf('%05d', $SID);
                
                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('+0800')); //GMT

                $model = new Patient_admission();

                $model->rn = $new_rn;
                $model->patient_uid = Yii::$app->request->get('id');
                $model->entry_datetime = $date->format('Y-m-d H:i:s');
                $model->type = Yii::$app->request->get('type');
                $model->loadDefaultValues();
                $model->initial_ward_class = "UNKNOWN";
                $model->initial_ward_code = "UNKNOWN";
                $model->save();
                
                $model_cancellation->replacement_uid = $new_rn;

                $model_cancellation->save();

                // return Yii::$app->getResponse()->redirect(array('/site/admission', 
                //     'id' => Yii::$app->request->get('id'))); 
                return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
                    'rn' => $model->rn));  
            }
            else{
                Yii::$app->session->setFlash('cancellation_error', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>'.Yii::t('app', 'Cancellation Fail!').'</strong>'.'</div>'
                );

                return Yii::$app->getResponse()->redirect(array('/site/admission', 
                    'id' => Yii::$app->request->get('id'))); 
            }
        }
        else{
            Yii::$app->session->setFlash('cancellation_error', '
                <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                <strong>'.Yii::t('app', 'Cancellation Fail!').'</strong>'.'</div>'
            );

            return Yii::$app->getResponse()->redirect(array('/site/admission', 
                'id' => Yii::$app->request->get('id'))); 
        }
    }

    public function actionPatient()
    {
        $model = Patient_information::findOne(Yii::$app->request->get('id'));

        $dataProvider1 = new ActiveDataProvider([
            'query'=> Patient_admission::find()->where(['patient_uid'=>$model->patient_uid])
            ->orderBy(['entry_datetime' => SORT_DESC]),
            'pagination'=>['pageSize'=>5],
        ]);
        
        return $this->renderPartial('/patient_admission/index', ['dataProvider'=>$dataProvider1]);   
    }

   /**
     * Displays a single Patient_admission model.
     * @param string $rn Rn
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($rn)
    {
        return $this->render('view', [
            'model' => $this->findModel($rn),
        ]);
    }

    /**
     * Creates a new Patient_admission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public static function actionCreate()
    {
        if(Yii::$app->request->get('confirm') == 't')
        {
            $SID = "0";

            // check RN Rules for starting on next year
            $model_latest_rn = Patient_admission::find()
            ->where(['type' => Yii::$app->request->get('type')])
            ->orderBy('rn DESC')
            ->one();
         
            if(!empty($model_latest_rn))
            {
                $arr = str_split($model_latest_rn['rn'], 4);
                $SID = (string)$model_latest_rn->rn;
                   
                //2022/000001, 2022/900001
                $SID = substr($SID,5,11); 
                if($arr[0] ==  date('Y')){
                    $SID = (int) $SID + 1;
                    if(Yii::$app->request->get('type') == 'Normal') $SID = str_pad($SID, 6, "0", STR_PAD_LEFT);
                }
                else{
                    if(Yii::$app->request->get('type') == 'Normal') $SID = "000001";
                    else $SID = "900001";
                }
            }
            else {
               if(Yii::$app->request->get('type') == 'Normal') $SID = "000001";
               else $SID = "900001";
            }
    
            if(Yii::$app->request->get('type') == 'Normal')
                $rn = date('Y')."/".$SID;
            else $rn = date('Y')."/".$SID;
            
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('+0800')); //GMT

            $model = new Patient_admission();

            $model->rn = $rn;
            $model->patient_uid = Yii::$app->request->get('id');
            $model->entry_datetime = $date->format('Y-m-d H:i:s');
            $model->type = Yii::$app->request->get('type');
            $model->loadDefaultValues();
            $model->initial_ward_class = "UNKNOWN";
            $model->initial_ward_code = "UNKNOWN";
            $model->initial_ward_class = '3';
            $model->save();

            // return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            // 'rn' => $model->rn));         
            
            return Yii::$app->getResponse()->redirect(array('/site/admission', 
            'id' => $model->patient_uid));   
        }
        else 
            return false;
    }


    /**
     * Updates an existing Patient_admission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $rn Rn
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($rn)
    {
        $model = $this->findModel($rn);
        $model_change_rn = $this->findModel($rn);

        $modelpatient = new Patient_information();
        $string_error = "";

        if ($this->request->isPost && isset($_POST['transfer'])){
            if ($modelpatient->load($this->request->post()) ){
                $ic = $modelpatient->nric;
                $modelpatient = Patient_information::find()->where(['nric' => $modelpatient->nric])->one();
                // var_dump(empty($modelpatient));
                // exit;
                if(empty($modelpatient)){
                    // echo ' Nric does not exist.';
                    // set the flash message
                   
                    Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Validation error! ').' </strong> Nric : '.$ic.''
                            .Yii::t('app', ' does not exist').' !</div>'
                    );
                }
                else{
                    if(!empty($ic)){
                        $model->patient_uid = $modelpatient->patient_uid;
                        $model->save();
                    }
                    else{
                        // set the flash message
                        Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Validation error! ').' </strong> 
                        '.Yii::t('app', 'Please enter patient nric').' !</div>');
                    }
                }
            
                return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
                'rn' => $model->rn));        
            }
        }

        // if ($this->request->isPost && isset($_POST['change'])){
        //     if ($model_change_rn->load($this->request->post()) ){
        //         $pid = $model_change_rn->patient_uid;
        //         $new_rn = $model_change_rn->rn;
        //         $modelpatient = Patient_information::findOne(['patient_uid' => $pid]);
            
        //         if(empty($modelpatient)){
                 
        //             Yii::$app->session->setFlash('msg', '
        //                 <div class="alert alert-danger alert-dismissable">
        //                 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
        //                 <strong>'.Yii::t('app', 'Validation error! ').' </strong> Nric : '.$pid.''
        //                     .Yii::t('app', ' does not exist').' !</div>'
        //             );
        //         }
        //         else{
        //             if(!empty($new_rn)){
        //                 $model->rn = $new_rn;
        //                 $arr = str_split($new_rn, 5);
        //                 $first_character = substr($arr[1], 0, 1);
        //                 if($first_character == '9')
        //                     $model->type = 'Labor';
        //                 else $model->type = 'Normal';

        //                 $model->validate();
        //                 $array_error = $model->getFirstErrors();
        //                 foreach($array_error as $error){
        //                     $string_error .= $error;
        //                 }     
        //                 if($string_error != "")
        //                 {
        //                     Yii::$app->session->setFlash('msg', '
        //                     <div class="alert alert-danger alert-dismissable">
        //                     <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
        //                     <strong>'.Yii::t('app', 'Validation error!').' </strong><br/>'. $string_error.'</div>');
        //                 }
        //                 else{
        //                     $model->save();
        //                     Yii::$app->session->setFlash('msg', '
        //                         <div class="alert alert-success alert-dismissable">
        //                         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
        //                         '.Yii::t('app', 'You have successfully changed Registration Number !').'</div>'
        //                     );
        //                     return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
        //                         'rn' => $model->rn));     
        //                 } 
        //             }
        //             else{
        //                 // set the flash message
        //                 Yii::$app->session->setFlash('msg', '
        //                 <div class="alert alert-danger alert-dismissable">
        //                 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
        //                 <strong>'.Yii::t('app', 'Validation error! ').' </strong> 
        //                 '.Yii::t('app', 'Please enter Registration Number').' !</div>');
        //             }
        //         }
            
        //         return $this->render('update', [
        //             'model' => $model,
        //             'modelpatient' => $modelpatient,
        //             'model_change_rn' => $model_change_rn
        //         ]);
        //     }
        // }
        
        if ($this->request->isPost && $model->load($this->request->post()) ) {
            if($model->initial_ward_code == null){
                $model->initial_ward_code = "UNKNOWN";
            }
            if($model->initial_ward_class == null){
                $model->initial_ward_class = "UNKNOWN";
            }

            $checkFormat = DateTime::createFromFormat('Y-m-d H:i', $model->entry_datetime);

            if($checkFormat){
                $validDate = DateFormat::convert($model->entry_datetime, 'datetime');

                if($validDate){
                    $date = new \DateTime();
                    $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                    $model->entry_datetime = $model->entry_datetime . ':' .$date->format('s');

                    if($model->save()){
                        return 'success';
                        // return $this->render('update', [
                        //     'model' => $model,
                        //     'modelpatient' => $modelpatient,
                        //     'model_change_rn' => $model_change_rn
                        // ]);
                    }   
                    else{
                        return 'error';
                    }
                }
            }

            if($model->save()){
                return 'success';
                // return $this->render('update', [
                //     'model' => $model,
                //     'modelpatient' => $modelpatient,
                //     'model_change_rn' => $model_change_rn
                // ]);
            }   
            else{
                return 'error';
            }

            // $model->validate();
            // var_dump($model->errors);
            // exit;
            // if($model->save()){
            //     return $this->render('update', [
            //         'model' => $model,
            //         'modelpatient' => $modelpatient,
            //         'model_change_rn' => $model_change_rn
            //     ]);
            // }    
        }

        $modelpatient = Patient_information::findOne(['patient_uid' => $model->patient_uid]);
        return $this->render('update', [
            'model' => $model,
            'modelpatient' => $modelpatient,
            'model_change_rn' => $model_change_rn
        ]);
       
    }

    public function actionPrintall($rn){
        $model = $this->findModel($rn); 

        // if($model->validate()) {
        //     (new Patient_admissionController(null, null))->actionPrint1($rn);
        //     (new Patient_admissionController(null, null))->actionPrint2($rn);
        //     (new Patient_admissionController(null, null))->actionPrint3($rn);
        //     (new Patient_admissionController(null, null))->actionPrint4($rn);

			return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
				'rn' => $model->rn));  
		// }

        // return $this->render('update', [
		// 	'model' => $model,
		// ]);
    }

	public function actionPrint1($rn)
    {
		$model = $this->findModel($rn);  
		if($model->validate()) {
            $error = PrintForm::printAdmissionForm($rn);
            if(!empty($error))
            {
                Yii::$app->session->setFlash('msg', '
                <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
            }
			return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
				'rn' => $model->rn));  
		}
 
		return $this->render('update', [
			'model' => $model,
		]);
    }

    public function actionPrint2($rn)
    {
		$model = $this->findModel($rn);  
		if($model->validate()) {
            $error = PrintForm::printChargeSheet($rn);
            if(!empty($error))
            {
                Yii::$app->session->setFlash('msg', '
                <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
            }
			return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
				'rn' => $model->rn));  
		}
 
		return $this->render('update', [
			'model' => $model,
		]);
    }

    public function actionPrint3($rn)
    {
		$model = $this->findModel($rn);  
		if($model->validate()) {
            $error = PrintForm::printCaseHistorySheet($rn);
            if(!empty($error))
            {
                Yii::$app->session->setFlash('msg', '
                <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
            }

			return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
				'rn' => $model->rn));  
		}
 
		return $this->render('update', [
			'model' => $model,
		]);
    }

    public function actionPrint4($rn)
    {
		$model = $this->findModel($rn);  
		if($model->validate()) {
            $error = PrintForm::printStickerLabels($rn);
            if(!empty($error))
            {
                Yii::$app->session->setFlash('msg', '
                <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
            }
			return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
				'rn' => $model->rn));  
		}
 
		return $this->render('update', [
			'model' => $model,
		]);
	}

    // public function actionPrint5($rn)
    // {
    //     $model = $this->findModel($rn);
    //     $batchdate = '9999-12-31';
        
    //     $exporter = new CsvGrid([
    //         'dataProvider' => new ActiveDataProvider([
    //             'query' => Patient_admission::find(),
    //             'pagination' => [
    //                 'pageSize' => 100, // export batch size
    //             ],
    //         ]),
    //         'columns' => [
    //             [
    //                 'attribute' => 'rn',
    //                 'label' => 'RN',
    //             ],
    //             [
    //                 'attribute' => 'nric',
    //                 'label' => 'IC',
    //                 'value' => function($model, $index, $dataColumn) {

    //                     return $model->patientU->nric;

    //                 },
    //             ],
    //             [
    //                 'attribute' => 'entry_datetime',
    //                 'label' => 'Entry Datetime',
    //                 'format' => 'datetime',
    //             ],
    //             [
    //                 'attribute' => 'reminder1',
    //                 'label' => 'Reminder 1',
    //             ],
    //             [
    //                 'attribute' => 'name',

    //                 'label' => 'patient Name',

    //                 'value' => function($model, $index, $dataColumn) {

    //                     return $model->patientU->name;

    //                 },
    //             ],
    //             [
    //                 'attribute' => 'batch_date',

    //                 'label' => 'Batch Date',

    //                 'value' => function($model, $index, $dataColumn) use ($batchdate) {

    //                     return $batchdate;

    //                 },
    //             ],

    //         ],
    //     ]);
    //     //$exporter->export()->saveAs('G:/Download/file.csv');
    //     return $exporter->export()->send('items.csv');

    //     return $this->render('update', [
	// 		'model' => $model,
	// 	]);
		
    // }

    public function actionPrint($rn)
    {
        var_dump("Print");
        exit;
	}

    /**
     * Deletes an existing Patient_admission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $rn Rn
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($rn)
    {
        $this->findModel($rn)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Patient_admission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $rn Rn
     * @return Patient_admission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public  static function findModel($rn)
    {
        if (($model = Patient_admission::findOne(['rn' => $rn])) !== null) {
            return $model;
        }
        else return 0;
        throw new NotFoundHttpException('The requested page does not exist.');
    }

   
}

?>