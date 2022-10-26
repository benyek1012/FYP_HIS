<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Inpatient_treatment $model */

$this->title = 'Create Inpatient Treatment';
$this->params['breadcrumbs'][] = ['label' => 'Inpatient Treatments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inpatient-treatment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
