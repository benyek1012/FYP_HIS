<?php

use app\models\Patient_admission;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\controllers\Patient_informationController;
use app\models\Patient_information;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_admissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Patient Information By Entry Date');
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_search_date', ['model' => $searchModel]); 

$model = Patient_information::findOne(Yii::$app->request->get('id'));
?>

<?php if(!empty(Yii::$app->request->get('Patient_informationSearch'))){ ?>
<body>
    <br/>
    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Patient Result');?></h3>
            <div class="d-flex justify-content-end">
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
                    if(empty(Yii::$app->request->get('Patient_informationSearch')))
                    {
                        echo Yii::t('app','Patient admission record is not founded');        
                    } 
                    else{
                        ?>
                        <!-- This is the gridview that shows patient admission summary-->
                        <?= kartik\grid\GridView::widget([
                                'dataProvider' => $dataProvider,
                            // 'filterModel' => $searchModel,
                                'showOnEmpty' => false,
                                'hover' => true,
                                'striped' => false,
                                'condensed' => false,
                                'emptyText' => Yii::t('app','Patient admission record is not founded'),
                            
                                'rowOptions' => function($model) {
                                    // $url = Url::to([Yii::$app->controller->id.'/index', 'id' => $model['patient_uid']]);
                                    $urlPatientAdmission = Url::toRoute(['patient_admission/patient', 'id' => $model['patient_uid']]);
                                    $urlPatientInformation = Url::toRoute(['patient_information/patient', 'id' => $model['patient_uid']]);
                                    return [
                                        // 'onclick' => "window.location.href='{$url}'"
                                        'onclick' => "patientAdmission('{$urlPatientAdmission}'); patientInformation('{$urlPatientInformation}');",
                                        'style' => "cursor:pointer"
                                    ];
                                },
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                        [
                                            'attribute' => 'name',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value' => function($data){
                                                return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->name;
                                            },
                                            'label' => Yii::t('app','Name')
                                        ],
                                        [
                                            'attribute' => 'nric',
                                            'format' => 'raw',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value' => function($data){
                                                $ic = ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->nric;
                                                return  Html::a($ic, \yii\helpers\Url::to(['/site/admission', 'id' => $data['patient_uid'], '#' => 'patient']));
                                            },
                                            'label' => Yii::t('app','NRIC/Passport')
                                        ],
                                        [
                                            'attribute' => 'race',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value' => function($data){
                                                return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->race;
                                            },
                                            'label' => Yii::t('app','Race')
                                        ],
                                        [
                                            'attribute' => 'sex',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value' => function($data){
                                                return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->sex;
                                            },
                                            'label' => Yii::t('app','Sex')
                                        ],
                                        [
                                            'attribute' => 'rn',
                                            'format' => 'raw',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value'=>function ($data) {
                                                return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                                            },
                                        ],
                                        [
                                            'attribute' => 'entry_datetime',
                                            "format"=>"raw",
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value'=>function ($data) {
                                                $date = new DateTime($data['entry_datetime']);
                                                $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                                                    // title
                                                    'title' => $date->format('Y-m-d H:i A') ,
                                                    'data-placement' => 'top' ,
                                                    'data-toggle'=>'tooltip',
                                                    'style' => 'white-space:pre;'
                                                ] );
                                                return $tag;
                                            },
                                        ],
                                        [
                                            'attribute' => 'billable_sum',
                                            'label' => Yii::t('app','Billable Total').' (RM)',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value' => function($data){
                                                return  (new Patient_admission()) -> get_billable_sum($data->rn);
                                            },
                                        ],
                                        [
                                            'attribute' => 'amount_due',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value' => function($data){
                                                return ((new Patient_information())->getBalanceRM($data->patient_uid));
                                            },
                                            'label' => Yii::t('app','Amount Due').' (RM)',
                                        ],
                                        [
                                            'attribute' => 'unclaimed_balance',
                                            'headerOptions'=>['style'=>'max-width: 100px;'],
                                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                                            'value' => function($data){
                                                return ((new Patient_information())->getUnclaimedBalanceRM($data->patient_uid));
                                            },
                                            'label' => Yii::t('app','Unclaimed Balance').' (RM)',
                                        ],
                                    ],
                            ]) ?>
                    <?php }
               ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

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
        <div class="card-body" id="patient-admission-summary">
            <?php 
                    if(empty($model))
                    {
                        echo Yii::t('app','Patient admission record is not found');        
                    } 
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
        <div class="card-body" id="patient-information">
            <!-- This is the form that shows patient information which can directly updating-->
            <?php
                    if(empty($model))
                    {
                        echo Yii::t('app','Patient record is not found');
                    } 
            ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

<?php } ?>



<?php
$js = <<<SCRIPT
    /* To initialize BS3 tooltips set this below */
    $(function () { 
       $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
            html:true
        });
    });
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs ( $js );
?>

<script>
function patientAdmission(url) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange  = function() {
        if(xhttp.readyState == 4 && xhttp.status == 200){
            document.getElementById("patient-admission-summary").innerHTML = this.responseText;
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}

function patientInformation(url) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange  = function() {
        if(xhttp.readyState == 4 && xhttp.status == 200){
            document.getElementById("patient-information").innerHTML = this.responseText;
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}
</script>