<?php

use app\models\Patient_information;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */

$temp2 = Patient_information::findOne(['patient_uid'=> Yii::$app->request->get('id')]);

$this->title = 'Create '.Yii::$app->request->get('type').' Patient Admission';
$this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/index', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="patient-admission-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
