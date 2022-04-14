<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use app\models\Patient_information;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


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
                $model_founded = $this->findModel($model->patient_uid);
                if(!empty($model_founded))
                    return Yii::$app->getResponse()->redirect(array('/site/index', 
                        'id' => $model_founded->patient_uid));
            }
        } else {
            $model->first_reg_date = date("yyyy-mm-dd");
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
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
           return Yii::$app->getResponse()->redirect(array('/site/index', 
            'id' => $model->patient_uid));            
        }

        return $this->render('/site/index', [
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
        else{
            echo '<script type="text/javascript">',
            'confirmAction('.$patient_nric.');',
            '</script>';
        }
    }
}

?>

<script>
// The function below will start the confirmation dialog
function confirmAction(ic) {
    var answer = confirm("Are you sure to create patient information?");
    if (answer) {
        window.location.href = '/site/index?ic='+ic;
    }else{
        window.location.href = '/site/index';
    }
}
</script>