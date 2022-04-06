<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */

$this->title = 'Home Page';
$this->params['breadcrumbs'] = [['label' => $this->title]];
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

            <div class="patient-information-view">

                <h1><?= Html::encode($model->name) ?></h1>

                <p>
                    <?= Html::a('Update', ['update', 'patient_uid' => $model->patient_uid], ['class' => 'btn btn-outline-primary']) ?>
                </p>

                <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'patient_uid',
            'first_reg_date',
            'nric',
            'nationality',
            'name',
            'sex',
            'phone_number',
            'email:email',
            'address1',
            'address2',
            'address3',
            'job',
        ],
    ]) ?>
            </div>

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
      //   echo $this->render('/patient_next_of_kin/view', ['model'=>$NOK]);
      
      ?>
            <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;">Add
                Waris</button>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

</div>