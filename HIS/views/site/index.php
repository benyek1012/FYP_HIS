<?php
use app\controllers\Patient_informationController;
use app\models\Patient_next_of_kin;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Home Page';
//$this->params['breadcrumbs'] = ['label' => $this->title];

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
            ?>
            <!-- This is the gridview that shows patient admission summary-->
                <?= GridView::widget([
                    'dataProvider' => Patient_informationController::getNOKProvider($model),
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                            'nok_name',
                            'nok_relationship',
                            'nok_phone_number',
                            'nok_email',
                        ],
                ]) ?>
            <?php } ?>
            <!-- <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;">Add
                Waris</button> -->
                <!-- <?php $form = ActiveForm::begin([]); ?>
                    <div class="form-group">
                        <?= Html::submitButton('Add Waris', ['class' => 'btn btn-outline-primary align-self-start', 'name' => 'buttonAddWaris', 'value' => Yii::$app->request->get("pid")]) ?>
                    </div>
                <?php ActiveForm::end(); ?> -->
                <?php
                // if(isset($_POST['buttonAddWaris'])) {
                //     $model_nok = new Patient_next_of_kin();
                //     echo $this->render('/patient_next_of_kin/_form', ['model' => $model_nok, 'pid' => $model->patient_uid]);
                // }
                ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

</div>