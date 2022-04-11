<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */

$this->title = 'Update Patient Admission: ' . $model->rn;
$this->params['breadcrumbs'][] = ['label' => 'Patient Admissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->rn, 'url' => ['view', 'rn' => $model->rn]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="patient-admission-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
