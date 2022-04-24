<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_status */

$this->title = 'Update Lookup Status: ' . $model->status_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->status_uid, 'url' => ['view', 'status_uid' => $model->status_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-status-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
