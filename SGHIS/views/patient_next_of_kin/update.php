<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_next_of_kin */

$this->title = 'Update Patient Next of Kin: ' . $model->nok_uid;
$this->params['breadcrumbs'][] = ['label' => 'Patient Next of Kins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nok_uid, 'url' => ['view', 'nok_uid' => $model->nok_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="patient-next-of-kin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
