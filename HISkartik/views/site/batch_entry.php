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


?>

<div class="patient-admission-form"> 
    <!-- If the flash message existed, show it  -->
    <?php if(Yii::$app->session->hasFlash('msg')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('msg') ?>
        </div>
    <?php endif; ?>

    <?php
     $dataProvider1 = new ActiveDataProvider([
        'query'=> Patient_admission::find()
        ->orderBy(['entry_datetime' => SORT_DESC,'rn' => SORT_DESC]),
        'pagination'=>['pageSize'=>5],
    ]);
    
    echo $this->render('/patient_admission/index', ['dataProvider'=>$dataProvider1]);

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
            <?= $form->field($model, 'type')->dropDownList($type, ['prompt'=>'Please select admission type','maxlength' => true]) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'startrn')->textInput(['value' => 0])->label(yii::t('app',"Start RN :")) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'endrn')->textInput(['value' => 0])->label(yii::t('app',"End RN :")) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Submit'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
<br/>
