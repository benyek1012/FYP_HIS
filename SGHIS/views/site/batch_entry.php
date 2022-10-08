<?php

use app\models\Patient_admission;
use app\models\Patient_admissionSearch;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app','Batch Entry');
$model = new Patient_admission();
$type = array( 'Normal' => 'Normal','Labor' => 'Labor');

$NormalID = "000001";
$LaborID = "900001";
// check RN Rules for starting on next year
$normal_latest_rn = Patient_admission::find()
->where(['type' => 'Normal'])
->orderBy('entry_datetime DESC')
->one();

// check RN Rules for starting on next year
$labor_latest_rn = Patient_admission::find()
->where(['type' => 'Labor'])
->orderBy('entry_datetime DESC')
->one();

if(empty($normal_latest_rn))
    $NormalID = date('Y')."/".$NormalID;

if(empty($labor_latest_rn)) 
    $LaborID = date('Y')."/".$LaborID;

?>

<div class="patient-admission-form"> 
    <!-- If the flash message existed, show it  -->
    <?php if(Yii::$app->session->hasFlash('msg')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('msg') ?>
        </div>
    <?php endif; ?>

    <a name="admission">
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
                 $dataProvider1 = new ActiveDataProvider([
                        'query'=> Patient_admission::find()
                         ->orderBy(['entry_datetime' => SORT_DESC,'rn' => SORT_DESC]),
                         'pagination'=>['pageSize'=>5],
                ]);
    
                echo $this->render('/patient_admission/index', ['dataProvider'=>$dataProvider1]);
                ?>
               </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
    </a>



    <a name="batch_entry">
            <div class="card">
                <div class="card-header text-white bg-primary">
                    <h3 class="card-title"><?php echo Yii::t('app','Batch Entry');?></h3>
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
                echo "<br/>";
                $form = kartik\form\ActiveForm::begin([
                'id' => 'patient-admission-form',
                'type' => 'vertical',
                'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
                ]); 
        ?>

        <div class="col-sm-6">
            <label><?php echo Yii::t('app', 'Latest Regular RN').': '; echo !empty($normal_latest_rn) ? $normal_latest_rn['rn'] : $NormalID ?></label>
        </div>

        <div class="col-sm-6">
            <label><?php echo Yii::t('app', 'Latest Labor RN').': '; echo !empty($labor_latest_rn) ? $labor_latest_rn['rn'] : $LaborID ?></label>
        </div>
        <br>

        <div class="col-sm-6">
            <?= $form->field($model, 'startrn')->textInput(['autocomplete' =>'off'])->label(yii::t('app',"Start RN :")) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'endrn')->textInput(['autocomplete' =>'off'])->label(yii::t('app',"End RN :")) ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Submit'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php kartik\form\ActiveForm::end(); ?>

        </div>
                <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </a>

</div>
<br/>
