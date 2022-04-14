<?php

use app\controllers\Patient_informationController;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use kartik\editable\Editable;


$model = Patient_information::findOne(Yii::$app->request->get('id'));
if(empty($model))
    $this->title = 'Home Page';
else
{
    $session = Yii::$app->session;
    $session->set('patient_id', Yii::$app->request->get('id'));
    $session->set('patient_name', $model->name);
   

    $this->title = $model->name;
    $this->params['breadcrumbs'][] = ['label' => $model->name];
}
?>

<body>

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
                <?php 
        if(!empty($model))
        {
            $dataProvider = new ActiveDataProvider([
                'query'=> Patient_admission::find()->where(['patient_uid'=>$model->patient_uid]),
                'pagination'=>['pageSize'=>5],
                ]);
        }

        if(!empty($model))
        {
?>
                <!-- This is the gridview that shows patient admission summary-->
                <?= kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                        'rn',
                        'entry_datetime',
                        'initial_ward_code',
                        'initial_ward_class',
                        'reference',
                        'medigal_legal_code',
                        'reminder_given',
                        'guarantor_name',
                        'guarantor_nric',
                        'guarantor_phone_number',
                        'guarantor_email:email',
                 ],
            ]) ?>

                <?php   } else echo "RN is not selected";
            ?>

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


                <!-- This is the form that shows patient information which can directly updating-->
                <?php
        if(!empty($model))
        {
    ?>
                <?= $this->render('/patient_information/update', [
                    'model' => $model]) ?>
                <?php   } else{
             echo "Patient is not selected";
        }  ?>
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
            if(!empty($model))
            {
                $dataProvider = new ActiveDataProvider([
                    'query'=> Patient_next_of_kin::find()->where(['patient_uid'=>$model->patient_uid]),
                    'pagination'=>['pageSize'=>5],
                    ]);
                echo $this->render('/patient_next_of_kin/index', ['dataProvider'=>$dataProvider]);
            ?>
                <?php 
            } 
            ?>

                <div class="form-group">
                    <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
                        onclick="showDiv();">Add Waris</button>
                    <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
                        onclick="hiddenForm();">Cancel</button>
                </div>

                <?php
            if(!empty($model)){
                $model_nok = new Patient_next_of_kin();
                echo $this->render('/patient_next_of_kin/_form', ['model' => $model_nok, 'value' => $model->patient_uid]);
            }
            ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </div>

    <script>

    function hiddenForm() {
        document.getElementById("NOk_Div").style.display = "none";
    }

    function showDiv() {
        document.getElementById('NOk_Div').style.display = "block";
    }
    </script>