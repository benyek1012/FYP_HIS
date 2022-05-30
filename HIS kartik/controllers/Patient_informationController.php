<?php

namespace app\controllers;

use Yii;
use app\models\Patient_information;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use GpsLab\Component\Base64UID\Base64UID;

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
    public function actionCreate($ic)
    {
        $model = new Patient_information();
        if($ic  == 'undefined') $model->nric = ' ';
        else $model->nric = $ic;
        $model->patient_uid = Base64UID::generate(32);
        $model->first_reg_date = date("Y-m-d");     
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
                if($model->save())
                {
                    return Yii::$app->getResponse()->redirect(array('/site/admission', 
                    'id' => $model->patient_uid, '#' => 'patient'));                 
                } 
            }
            else{
                echo '<script type="text/javascript">',
                'setTimeout(function(){',
                    'duplicateIC('.$model->nric.');',
                    '},200);',
                '</script>';
            }
        }

        return $this->render('/site/admission', [
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
            // var_dump($patient_nric);
            // exit; 
            echo "<script type='text/javascript'>
            setTimeout(function(){
                confirmAction(\"$patient_nric\");
                },200);
            </script>";
    }

}
?>

<script>

<?php if( Yii::$app->language == "en"){ ?>
// The function below will start the confirmation dialog
function confirmAction(ic) {
    var answer = confirm("Are you sure to create patient information?");
    if (answer) {
        window.location.href = '/patient_information/create?ic=' + ic;
    } else {
        window.location.href = '/site/admission';
    }
}

// The function below will start the confirmation dialog
function duplicateIC(ic) {
   alert('NRIC ' + ic + ' is existed in system!');
   window.location.href = history.go(-1);
}

<?php }else{?>
// The function below will start the confirmation dialog
function confirmAction(ic) {
    var answer = confirm("Adakah anda pasti untuk membuat butiran pesakit?");
    if (answer) {
        window.location.href = '/patient_information/create?ic=' + ic;
    } else {
        window.location.href = '/site/admission';
    }
}

// The function below will start the confirmation dialog
function duplicateIC(ic) {
   alert('NRIC ' + ic + ' wujud dalam sistem!');
    window.location.href = history.go(-1);
}

<?php } ?>

</script>