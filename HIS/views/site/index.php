<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\controllers\Patient_informationController;
use app\models\Patient_next_of_kin;
use yii\bootstrap4\ActiveForm;

$this->title = 'Home Page';
$this->params['breadcrumbs'] = [['label' => $this->title]];
?>
<?php
$query = ArrayHelper::getValue(Yii::$app->request->post(), 'Patient_informationSearch.nric');
if(isset($query))
    $info = Patient_informationController::findModel_nric($query);
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title">Patient Admission Summary</h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            The body of the card
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title">Patient Information</h3>
            <div class="card-tools">

                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body d-flex flex-column">
            <?php
            if(!empty($model))
            {
            ?>
                <?= $this->render('/patient_information/_form', [
                    'model' => $model]) ?>
        <?php } else echo "Patient is not selected"; ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title">Waris</h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body d-flex flex-column">
            <?php   
      
            // $NOK = Patient_next_of_kin::findOne(['patient_uid' => $model->patient_uid]);
            // if (!empty($NOK)) 
            //     echo $this->render('/patient_next_of_kin/view', ['model'=>$NOK]);
            if(!empty($modelNOK)){
                echo $this->render('/patient_next_of_kin/index', ['model' => $modelNOK, 'dataProvider' => $dataProvider]);
                echo $this->render('/patient_next_of_kin/_form', ['model' => $modelNOK]);
                // $NOK = Patient_next_of_kin::findOne(['patient_uid' => $model->patient_uid]);
                // var_dump($NOK);
                // exit();
                // if (!empty($NOK)) 
                //     echo $this->render('/patient_next_of_kin/view', ['model'=>$NOK]);
            }
            // echo $this->render('/patient_next_of_kin/_form', ['model' => $modelNOK]);
            ?>
            
            <!-- <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;">Add
                Waris</button> -->

            <?php $form = ActiveForm::begin([]); ?>
                <div class="form-group">
                    <?= Html::submitButton('Add Waris', ['class' => 'btn btn-outline-primary align-self-start', 'name' => 'buttonAddWaris']) ?>
                </div>
            <?php ActiveForm::end(); ?>
            
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>