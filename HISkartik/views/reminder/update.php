<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reminder */

$this->title = 'Update Batch: ' . $model->batch_date;
$this->params['breadcrumbs'][] = ['label' => 'Reminders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->batch_date, 'url' => ['view', 'batch_date' => $model->batch_date]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="reminder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
