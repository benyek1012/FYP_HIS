<?php
use app\models\Patient_admission;
use app\models\Patient_information;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */

$temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Admission'), 'url' => ['site/admission']]; 
$this->title = Yii::t('app','Update Patient Admission') . " : ". yii::$app->request->get('rn');
if($temp2->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = Yii::t('app','Update Patient Admission');
?>
<div class="patient-admission-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelpatient' =>$modelpatient,
        'model_change_rn' => $model_change_rn
    ]) ?>

</div>