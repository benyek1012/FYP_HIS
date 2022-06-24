<?php

use app\models\Patient_admission;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\controllers\Patient_informationController;
use app\models\Patient_information;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_admissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Patient Admission');
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="patient-admission-index">
    
    <!-- This is the gridview that shows patient admission summary-->   
    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Patient admission record is not founded'),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'name',
                    'value' => function($data){
                        return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->name;
                    },
                ],
               
                // [
                //     'format' => 'raw',
                //     'attribute'=>'name',
                //     'value' => function($model) {
                //         foreach ($model->patient_information as $patient_information) {
                //             $name[] =  $patient_information->name;
                //         }
                //         return implode("\n", $name);
                //     },
                // ],
                [
                    'attribute' => 'nric',
                    'format' => 'raw',
                    'value' => function($data){
                        $ic = ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->nric;
                        return  Html::a($ic, \yii\helpers\Url::to(['/site/admission', 'id' => $data['patient_uid'], '#' => 'patient']));
                    },
                ],
                [
                    'attribute' => 'race',
                    'value' => function($data){
                        return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->race;
                    },
                ],
                [
                     'attribute' => 'sex',
                    'value' => function($data){
                        return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->sex;
                    },
                ],
                [
                    'attribute' => 'rn',
                    'format' => 'raw',
                    'value'=>function ($data) {
                        return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                    },
                ],
                [
                    'attribute' => 'entry_datetime',
                    "format"=>"raw",
                ],
                [
                    'attribute' => 'billable_sum',
                    'label' => Yii::t('app','Billable Total').' (RM)',
                ],
                [
                    'attribute' => 'final_fee',
                    'label' => Yii::t('app','Amount Due').' / '.Yii::t('app','Unclaimed Balance').' (RM)',
                ],
            ],
    ]) ?>

</div>

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


<br/>

<div id="card1" class="container-fluid">
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
                    ->orderBy(['entry_datetime' => SORT_DESC, 'rn' => SORT_DESC]),
                    'pagination'=>['pageSize'=>10],
                ]);
                
                echo $this->render('/patient_admission/index', ['dataProvider'=>$dataProvider1]);
        ?>
                <div class="form-group">
                    <br />
                    <?= Html::a(Yii::t('app','Add New Admission'),['site/admission', 'id' => $model->patient_uid,'type' => 'Normal'], ['class' => 'btn btn-outline-primary align-self-start']) ?>
                    &nbsp;&nbsp;
                    <?= Html::a(Yii::t('app','Add New Labor Admission'),['site/admission', 'id' => $model->patient_uid, 'type' => 'Labor'], ['class' => 'btn btn-outline-primary align-self-start']) ?>

                </div>
        <?php
            } 
            else echo Yii::t('app','Patient admission record is not found');
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
                    else echo Yii::t('app','Patient record is not found');
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