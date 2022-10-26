<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Lookup_inpatient_treatment_cost $model */

$this->title = 'Update Lookup Inpatient Treatment Cost: ' . $model->inpatient_treatment_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Inpatient Treatment Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->inpatient_treatment_uid, 'url' => ['view', 'inpatient_treatment_uid' => $model->inpatient_treatment_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-inpatient-treatment-cost-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
