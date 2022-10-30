<?php


use app\models\Patient_admission;
use app\models\Patient_information;


/* @var $this yii\web\View */
/* @var $model app\models\Bill */

$temp2 = Patient_admission::findOne(['rn'=> $model->rn]);
$temp3 = Patient_information::findOne(['patient_uid'=> $temp2->patient_uid]);

$this->title = Yii::t('app','Print').' Bill: ' . $model->rn;
if($temp3->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp3->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp3->patient_uid]];
$this->params['breadcrumbs'][] =  Yii::t('app','Print');
?>
<div class="bill-generate">

    <?= $this->render('_form', [
        'model' => $model,
        'modelWard' => $modelWard,
        'modelTreatment' => $modelTreatment,
        'print_readonly' => true,
        'model_cancellation' => $model_cancellation,
        'modelFPP' => $modelFPP,
        'modelInpatient' => $modelInpatient,
    ]) ?>

</div>
