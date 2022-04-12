<?php

use app\controllers\Patient_informationController;
use app\models\Patient_next_of_kin;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\data\ActiveDataProvider;
use kartik\editable\Editable;

$this->title = 'Home Page';
//$this->params['breadcrumbs'] = ['label' => $this->title];

?>

<body onload="hiddenForm()">

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
?>
<!-- This is the gridview that shows patient admission summary-->
            <?= GridView::widget([
                'dataProvider' => Patient_informationController::getProvider($model),
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
                        [
                            'class' => ActionColumn::className(),
                            'urlCreator' => function ($action, $model) {
                                return Url::toRoute(['patient_admission/'.$action.'?rn='.$model['rn']]);
                             }
                        ],
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
<?php   } else echo "Patient is not selected"; ?>
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
            ?>
            <!-- This is the gridview that shows patient admission summary-->
                <?= kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    // 'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                            'class' => '\kartik\grid\EditableColumn',
                            'attribute' => 'nok_name',
                            'editableOptions' => [                
                                'asPopover' => false,
                            ],
                        ],

                        [
                            'class' => '\kartik\grid\EditableColumn',
                            'attribute' => 'nok_relationship',
                            'editableOptions' => [
                                'size' => 'md',
                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                'data' => [
                                    'father'=>'Father',
                                    'monther'=>'Monther',
                                    'couple' => 'Couple',
                                    'brother' => 'Brother',
                                    'sister' => 'Sister',
                                    'other' => 'Other'
                                ],
                            ],
                        ],

                        [
                            'class' => '\kartik\grid\EditableColumn',
                            'attribute' => 'nok_phone_number',
                        ],

                        [
                            'class' => '\kartik\grid\EditableColumn',
                            'attribute' => 'nok_email',
                            'editableOptions' => [                
                                'asPopover' => false,
                            ],
                        ],

                        [
                            'class' => ActionColumn::className(),
                            'template' => '{view}',
                            'urlCreator' => function ($action,  $model) {
                                return Url::toRoute(['patient_next_of_kin/'.$action.'?nok_uid='.$model['nok_uid']]);
                            }
                        ],
                    ],
                ]); ?>
            <?php } ?>

            <div class="form-group">
                <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;" onclick="showForm();">Add Waris</button>
                <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;" onclick="hiddenForm();">Cancel</button>
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
    function showForm() {
        document.getElementById("patient-next-of-kin-form").style.visibility = "visible";
    }

    function hiddenForm() {
        document.getElementById("patient-next-of-kin-form").style.visibility = "hidden";
    }
</script>