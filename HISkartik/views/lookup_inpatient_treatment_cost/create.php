<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Lookup_inpatient_treatment_cost $model */

$this->title = 'Create Lookup Inpatient Treatment Cost';
$this->params['breadcrumbs'][] = ['label' => 'Lookup Inpatient Treatment Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-inpatient-treatment-cost-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
