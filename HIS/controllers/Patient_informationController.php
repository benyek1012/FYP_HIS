<?php

namespace app\controllers;

use Yii;;
use app\models\Patient_information;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Patient_informationSearch;
use app\models\Patient_next_of_kin;
use app\models\Patient_next_of_kinSearch;
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

    public function actionView()
    {
        $model = new Patient_informationSearch();
        $modelNOK = new Patient_next_of_kinSearch();

        if(Yii::$app->request->post('hasEditable')){
            $nok_uid = Yii::$app->request->post('editableKey');
            $nok = Patient_next_of_kin::findOne($nok_uid);

            $out = Json::encode(['output'=>'','message'=>'']);
            $post = [];
            $posted = current($_POST['Patient_next_of_kin']);
            $post['Patient_next_of_kin'] = $posted;

            if($nok->load($post)){
                $nok -> save();
            }
            echo $out;
            return;
        }

        if ($this->request->isPost && $model->load($this->request->post()))
        {
            if($model->search($model->nric)) {
                $findPatient_uid = $this->findModel_nric($model->nric);
                // var_dump($findPatient_uid->patient_uid);
                // exit();
                if($modelNOK->search($findPatient_uid->patient_uid)){
                    // $nokPatient_uid = Patient_next_of_kinController::findModel_patient_uid($findPatient_uid->patient_uid);
                    // var_dump($nokPatient_uid);
                    // exit();
                    // $dataProvider = $modelNOK->search($this->request->queryParams);
                    // $dataProvider = $modelNOK->search($findPatient_uid->patient_uid);
                    $dataProvider = $modelNOK->search($this->request->get());
                    // var_dump($findPatient_uid->patient_uid);
                    // exit();
                    return $this->render('/site/index', [
                        'model' => $this->findModel_nric($model->nric),
                        'modelNOK' => Patient_next_of_kinController::findModel_patient_uid($findPatient_uid->patient_uid),
                        'dataProvider' => $dataProvider,
                    ]);  
                }
                // return $this->render('/site/index', [
                //  'model' => $this->findModel_nric($model->nric)]);  
            }
        } else {
            $model->loadDefaultValues();
        }
    }

    /**
     * Creates a new Patient_information model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Patient_information();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->render('/site/index', [
                    'model' => $this->findModel($model->patient_uid)]);  
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Patient_information model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $patient_uid Patient Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($patient_uid)
    {
        $model = $this->findModel($patient_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'patient_uid' => $model->patient_uid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
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

    public function findModel_nric($patient_nric)
    {
        if (($model = Patient_information::findOne(['nric' => $patient_nric])) !== null) {
            return $model;
        }

        // throw new NotFoundHttpException('The requested page does not exist.');
    }
}