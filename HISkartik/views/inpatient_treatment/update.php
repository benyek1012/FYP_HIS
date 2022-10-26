<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Inpatient_treatment $model */

$this->title = 'Update Inpatient Treatment: ' . $model->inpatient_treatment_uid;
$this->params['breadcrumbs'][] = ['label' => 'Inpatient Treatments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->inpatient_treatment_uid, 'url' => ['view', 'inpatient_treatment_uid' => $model->inpatient_treatment_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inpatient-treatment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
