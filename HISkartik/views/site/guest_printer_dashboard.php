
<?php

use app\controllers\SiteController;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use yii\data\ActiveDataProvider;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = Yii::t('app','Guest Printer Dashboard');                 
$model = Patient_information::findOne(Yii::$app->request->get('id'));
if(empty($model))
{
    $this->title = Yii::t('app','Admission');
    $this->params['breadcrumbs'][] = Yii::t('app','Admission');
}
else
{
    if($model->name != "")
        $name = $model->name;
    else $name = "Unknown";
   
    $this->title = $name;
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Admission'), 'url' => ['site/admission']]; 
    $this->params['breadcrumbs'][] = ['label' => $name];
}

if(!empty($model)){
    $urlNormal = Url::toRoute(['patient_admission/create', 'id' => $model->patient_uid, 'type' => 'Normal']);
    $urlLabor = Url::toRoute(['patient_admission/create', 'id' => $model->patient_uid, 'type' => 'Labor']);
    $urlPatientAdmission = Url::toRoute(['patient_admission/update']);
}
?>

<body>
    <div id="card1" class="container-fluid">

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
                    else echo Yii::t('app','Patient record is not found');
            ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </a>
        
        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Patient Admission Summary');?></h3>
                <div class="d-flex justify-content-end">
                    <?php
                    if(!empty($model))
                        echo "<div>".(new Patient_information())  -> getBalance($model->patient_uid)."&nbsp&nbsp&nbsp&nbsp&nbsp".
                        (new Patient_information())  -> getUnclaimedBalance($model->patient_uid)."&nbsp&nbsp&nbsp</div>";
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
                    ->orderBy(['entry_datetime' => SORT_DESC]),
                    'pagination'=>['pageSize'=>5],
                ]);
                
                echo $this->render('/patient_admission/index', ['dataProvider'=>$dataProvider1]);
        ?>
        <?php
            } 
            else echo Yii::t('app','Patient admission record is not found');
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

function addNormal(url, urlPatientAdmission) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange  = function() {
        if(xhttp.readyState == 4 && xhttp.status == 200){
            if(this.responseText == false){
                confirmAction(url);
                
            }
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}

<?php if( Yii::$app->language == "en"){ ?>
// The function below will start the confirmation dialog
function confirmAction(url) {
    var answer = confirm("Are you sure to create patient admission?");
    if (answer) {
        // window.location.href = window.location + '&confirm=t';
        window.location.href = url + '&confirm=t';
    } else {
        // window.location.href = history.back();
    }
}
<?php }else{?>

function confirmAction(url) {
    var answer = confirm("Adakah anda pasti untuk membuat pendaftaran pesakit?");
    if (answer) {
        // window.location.href = window.location + '&confirm=t';
        window.location.href = url + '&confirm=t';
    } else {
        // window.location.href = history.back();
    }
}
<?php } ?>
</script>