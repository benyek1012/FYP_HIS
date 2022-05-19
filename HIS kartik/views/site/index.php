<?php

use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use yii\data\ActiveDataProvider;
use yii\bootstrap4\Html;
use yii\bootstrap4\Button;
use yii\helpers\Url;

$model = Patient_information::findOne(Yii::$app->request->get('id'));
if(empty($model))
    $this->title = Yii::t('app','Home Page');
else
{
    if($model->name != "")
        $name = $model->name;
    else $name = "Unknown";
   
    $this->title = $name;
    $this->params['breadcrumbs'][] = ['label' => $name];
}
?>

<body>
    <div id="card1" class="container-fluid">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Patient Admission Summary');?></h3>
                <div class="d-flex justify-content-end">
                    <?php
                    if(!empty($model))
                        echo "<div>".Patient_information::getBalance($model->patient_uid)."&nbsp&nbsp&nbsp&nbsp&nbsp".
                        Patient_information::getUnclaimedBalance($model->patient_uid)."&nbsp&nbsp&nbsp</div>";
                    ?>
                    <div class="card-tools">
                        <!-- Collapse Button -->
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
        <?php 
            if(!empty($model))
            {
                $dataProvider1 = new ActiveDataProvider([
                    'query'=> Patient_admission::find()->where(['patient_uid'=>$model->patient_uid])
                    ->orderBy(['entry_datetime' => SORT_DESC, 'rn' => SORT_DESC]),
                    'pagination'=>['pageSize'=>10],
                ]);
                
                echo $this->render('/patient_admission/index', ['dataProvider'=>$dataProvider1]);
        ?>
                <div class="form-group">
                    <br />
                    <?= Html::a(Yii::t('app','Add New Admission'),['site/index', 'id' => $model->patient_uid,'type' => 'Normal'], ['class' => 'btn btn-outline-primary align-self-start']) ?>
                    &nbsp;&nbsp;
                    <?= Html::a(Yii::t('app','Add New Labor Admission'),['site/index', 'id' => $model->patient_uid, 'type' => 'Labor'], ['class' => 'btn btn-outline-primary align-self-start']) ?>

                </div>
        <?php
            } 
            else echo Yii::t('app','Patient admission record is not founded');
        ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <a name="patient">
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
                <div class="card-body">
             <!-- This is the form that shows patient information which can directly updating-->
            <?php
                    if(!empty($model))
                    {
            ?>
                        <?= $this->render('/patient_information/update', [
                        'model' => $model]) ?>
            <?php   } 
                    else echo Yii::t('app','Patient record is not founded');
            ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </a>

        <a name='nok'>
            <div class="card">
                <div class="card-header text-white bg-primary">
                    <h3 class="card-title"><?php echo Yii::t('app','Next of kin Information');?></h3>
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
                    $dataProvider2 = new ActiveDataProvider([
                        'query'=> Patient_next_of_kin::find()->where(['patient_uid'=>$model->patient_uid]),
                        'pagination'=>['pageSize'=>3],
                        ]);
                    echo $this->render('/patient_next_of_kin/index', ['dataProvider'=>$dataProvider2]);
            ?>
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
                            onclick="showDiv();"><?php echo Yii::t('app','Add Next of kin');?></button>
                        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
                            onclick="hiddenForm();"><?php echo Yii::t('app','Cancel');?></button>
                    </div>
            <?php
                    $model_nok = new Patient_next_of_kin();
                    echo $this->render('/patient_next_of_kin/_form', ['model' => $model_nok, 'value' => $model->patient_uid]);
                }
                else echo Yii::t('app','Next Of Kin record is not founded');
            ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </a>
    </div>

<script>
    function hiddenForm() {
        document.getElementById("NOk_Div").style.display = "none";
    }

    function showDiv() {
        document.getElementById('NOk_Div').style.display = "block";
    }
</script>