<?php

namespace app\controllers;

use Yii;
use app\models\Patient_information;
use app\models\Patient_informationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use GpsLab\Component\Base64UID\Base64UID;
use yii\helpers\Url;
use yii\helpers\Json;

/**
 * Patient_informationController implements the CRUD actions for Patient_information model.
 */
class Patient_informationController extends Controller
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

    public function actionGetdob($id){
        $model = $this->findModel($id);
        
        if($model->hasValidIC() && $model->Date_validate($model->getStartDate()))
        {
            $model->DOB = $model->getDateForDatabase();
        }
        echo Json::encode($model);
    }

    public function actionPatient()
    {
        $model = Patient_information::findOne(Yii::$app->request->get('id'));

        return $this->renderPartial('/patient_information/view', ['model' => $model]);
    }

    /**
     * Creates a new Patient_information model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Patient_information();
        $session = Yii::$app->session;
        if ($session->has('patient_ic'))
        {
            $ic = $session->get('patient_ic');
            $session->remove('patient_ic');
        }

        if($ic  == 'undefined') $model->nric = ' ';
        else $model->nric = $ic;
        $model->patient_uid = Base64UID::generate(32);
        $model->first_reg_date = date("Y-m-d");     

        // var_dump($model->hasValidIC());
        // exit();
        if($model->hasValidIC())
        {
			
            $model->sex = $model->getGender();
            $model->nationality = $model->getNationality();
			if($model->Date_validate($model->getStartDate()))
			{
				$model->DOB = $model->getDateForDatabase();
			}
        }

        $model->save();

        return Yii::$app->getResponse()->redirect(array('/site/admission', 
            'id' => $model->patient_uid));
    }

    /**
     * Updates an existing Patient_information model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $patient_uid Patient Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $flag = true;
        $rowIC = (new \yii\db\Query())
        ->select(['nric'])
        ->from('patient_information')
        ->where(['patient_uid' => $id])
        ->one();

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->nric = trim($model->nric);
            $rows = (new \yii\db\Query())
            ->select(['nric'])
            ->from('patient_information')
            ->all();
            foreach ($rows as $row) {
                // Check IC is existed in database, and allow empty IC input and allow unchanged IC could did update
                if($model->nric == trim($row['nric']) && $model->nric != "" && $model->nric != trim($rowIC['nric'])){
                    $flag = false;
                    break;
                }
            }
        
            if($flag == true){
                if($model->hasValidIC() && $model->Date_validate($model->getStartDate()))
                {
                    //$model->DOB = $model->getDateForDatabase();
                    //$model->sex = $model->getGender();
                    // $model->nationality = $model->getNationality();
                    echo 's';
                }
                    

                if($model->save())
                {
                    // return Yii::$app->getResponse()->redirect(array('/site/admission', 
                    // 'id' => $model->patient_uid, '#' => 'patient'));                 
                }
            }
            else{
                echo '<script type="text/javascript">',
                'setTimeout(function(){',
                    'duplicateIC();',
                    '},200);',
                '</script>';
            }
        }

        return $this->render('/site/admission', [
            'model' => $model,
        ]);
    }

    public function actionSearch_name()
    {        
        $searchModel = new Patient_informationSearch();
        $dataProvider = $searchModel->search_name(Yii::$app->request->queryParams);
    
        return $this->render('search_patient_name', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    } 

    public function actionSearch_date()
    {        
        $searchModel = new Patient_informationSearch();
        $dataProvider = $searchModel->search_date(Yii::$app->request->queryParams);

        return $this->render('search_patient_date', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    } 

    public function actionSearch_ward()
    {        
        $searchModel = new Patient_informationSearch();
        $dataProvider = $searchModel->search_ward(Yii::$app->request->queryParams);

        // echo '<pre>';
        // var_dump(Yii::$app->request->queryParams);
        // echo '</pre>';
        // exit;
    
        return $this->render('search_patient_ward', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    } 

    public function actionSearch_discharge()
    {        
        $searchModel = new Patient_informationSearch();
        $dataProvider = $searchModel->search_discharge(Yii::$app->request->queryParams);

        // echo '<pre>';
        // var_dump(Yii::$app->request->queryParams);
        // echo '</pre>';
        // exit;
    
        return $this->render('search_patient_discharge', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    } 

    // pass DOB from datepicker, then ajax calculate age then render on Age textInput
    public function actionDob($dob, $id) {
        $model = Patient_information::findOne($id);
        echo Json::encode($model->calculateDob($model->DOB));
    }

    // pass DOB from datepicker, then ajax calculate age then render on Age textInput
    public function actionLoad_dob_from_ic($nric, $id) {
        $model = Patient_information::findOne($id);
        $model->nric = $nric;
        echo Json::encode($model->getDateForDatabase());
    }

    public function actionGender($nric, $id) {
        $model = Patient_information::findOne($id);
        $model->nric = $nric;
		if(empty($model->sex))
			echo Json::encode($model->getGender());
		else	
			echo Json::encode($model->sex);
    }

    public function actionNationality($id){
        $model = Patient_information::findOne($id);

        echo Json::encode($model->getNationality());
    }

    /**
     * Finds the Patient_information model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $patient_uid Patient Uid
     * @return Patient_information the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($patient_uid)
    {
        if (($model = Patient_information::findOne(['patient_uid' => $patient_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public static function findModel_nric($patient_nric)
    {
        // true flag means prompt out alert to show create patient info
        $flag = false;
        if($patient_nric == '') $flag = true;
        else
        {
            // IC is existed in Database
            if ((($model = Patient_information::findOne(['nric' => $patient_nric])) !== null) ) 
                return $model;
            else $flag = true;
        }
      
        if($flag == true) 
        {
            $session = Yii::$app->session;
            $session->set('patient_ic', $patient_nric);
            // var_dump($patient_nric);
            // exit; 
            // echo "<script type='text/javascript'>
            // setTimeout(function(){
            //     confirmActionPatient();
            //     },200);
            // </script>";
        }          
    }

}
?>