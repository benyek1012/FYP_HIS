<?php

use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use yii\data\ActiveDataProvider;


$model = Patient_information::findOne(Yii::$app->request->get('id'));
if(empty($model))
    $this->title = Yii::t('app','Home Page');
else
{
    $session = Yii::$app->session;
    $session->set('patient_id', Yii::$app->request->get('id'));

    if($model->name != "")
        $session->set('patient_name', $model->name);
    else $session->set('patient_name', "User");
   
    $this->title = $session['patient_name'];
    $this->params['breadcrumbs'][] = ['label' => $session['patient_name']];
}
?>

<body>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Patient Admission Summary');?></h3>
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
                'pagination'=>['pageSize'=>3],
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

                <?php   } else echo Yii::t('app','RN is not selected');
            ?>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Patient Information');?></h3>
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
             echo Yii::t('app','Patient is not selected');
        }  ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Waris');?></h3>
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
                        onclick="showDiv();"><?php echo Yii::t('app','Add Waris');?></button>
                    <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
                        onclick="hiddenForm();"><?php echo Yii::t('app','Cancel');?></button>
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