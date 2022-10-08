<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cancellation */

$this->title = 'Update Cancellation: ' . $model->cancellation_uid;
$this->params['breadcrumbs'][] = ['label' => 'Cancellations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cancellation_uid, 'url' => ['view', 'cancellation_uid' => $model->cancellation_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cancellation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
