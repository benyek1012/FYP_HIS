<?php

use app\models\Patient_admission;
use app\models\Patient_information;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */

$temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

$this->title = Yii::t('app','Create Bill').' : '.Yii::$app->request->get('rn');
if($temp2->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = Yii::t('app','Create Bill');
?>
<div class="bill-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelWard' => $modelWard,
        'modelTreatment' => $modelTreatment,
        'modelFPP' => $modelFPP,
        'model_cancellation' => $model_cancellation,
        'modelInpatient' => $modelInpatient,
    ]) ?>

</div>
