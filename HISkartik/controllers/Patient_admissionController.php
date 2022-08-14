<?php

namespace app\controllers;
require 'vendor/autoload.php';

use Yii;
use app\models\Cancellation;
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

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use app\models\PrintForm;
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
                $model->reminder_given = 0;
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
     * Lists all Patient_admission models.
     *
     * @return string
     */
    public function actionIndex()
    {        
        $searchModel = new Patient_AdmissionSearch();
        // $searchModel::find()->select('max(entry_datetime)', 'patient_uid');
        // $searchModel::find()->groupBy('patient_uid');
        // //$searchModel = Patient_AdmissionSearch::class()->findAll($searchModel);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    
        return $this->render('search_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

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
            $rows = (new \yii\db\Query())
            ->select(['rn'])
            ->from('patient_admission')
            ->where(['type' => Yii::$app->request->get('type')])
            ->all();
            $SID = "1" + count($rows);
    
            if(Yii::$app->request->get('type') == 'Normal')
                $rn = date('Y')."/".sprintf('%06d', $SID);
            else $rn = date('Y')."/9".sprintf('%05d', $SID);
            
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
            $model->reminder_given = 0;
            $model->save();

            return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
            'rn' => $model->rn));          
        }
        else 
            return false;
            // echo '<script type="text/javascript">',
            //         'setTimeout(function(){',
            //             'confirmAction();',
            //             '},200);',
            //     '</script>';
        
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

        $modelpatient = new Patient_information();

        if ($this->request->isPost && isset($_POST['Update'])){
            if ($modelpatient->load($this->request->post()) ){
                $ic = $modelpatient->nric;
                $modelpatient = Patient_information::find()->where(['nric' => $modelpatient->nric])->one();
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
                            '.Yii::t('app', 'Please enter patient nric').' !</div>'
                    );
                        }
                }
            
                return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
                        'rn' => $model->rn));  
            }
       }
        
        if ($this->request->isPost && $model->load($this->request->post()) ) {
            if($model->initial_ward_code == null){
                $model->initial_ward_code = "UNKNOWN";
            }
            if($model->initial_ward_class == null){
                $model->initial_ward_class = "UNKNOWN";
            }
            if($model->save()){
                return Yii::$app->getResponse()->redirect(array('/patient_admission/update', 
                    'rn' => $model->rn));  
            }    
        }

        $modelpatient = Patient_information::findOne(['patient_uid' => $model->patient_uid]);
        return $this->render('update', [
            'model' => $model,
            'modelpatient' => $modelpatient
        ]);
       
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