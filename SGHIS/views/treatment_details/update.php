<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment_details */

$this->title = 'Update Treatment Details: ' . $model->treatment_details_uid;
$this->params['breadcrumbs'][] = ['label' => 'Treatment Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->treatment_details_uid, 'url' => ['view', 'treatment_details_uid' => $model->treatment_details_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="treatment-details-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
