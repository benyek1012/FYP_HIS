<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reminder */

$this->title = 'Update Reminder: ' . $model->batch_uid;
$this->params['breadcrumbs'][] = ['label' => 'Reminders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->batch_uid, 'url' => ['view', 'batch_uid' => $model->batch_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="reminder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
