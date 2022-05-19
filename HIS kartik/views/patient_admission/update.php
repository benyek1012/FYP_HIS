<?php
use app\models\Patient_admission;
use app\models\Patient_information;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */

$temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

$this->title = Yii::t('app','Update Patient Admission : ') . $model->rn;
if($temp2->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/index', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/index', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="patient-admission-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>