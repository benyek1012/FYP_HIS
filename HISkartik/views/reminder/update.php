<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reminder */

$this->title = 'Update Batch: ' . $model->batch_datetime;
$this->params['breadcrumbs'][] = ['label' => 'Reminders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->batch_datetime, 'url' => ['view', 'batch_datetime' => $model->batch_datetime]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="reminder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
