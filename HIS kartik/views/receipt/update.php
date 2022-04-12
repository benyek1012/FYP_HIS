<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */

$this->title = 'Update Receipt: ' . $model->receipt_uid;
$this->params['breadcrumbs'][] = ['label' => 'Receipts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->receipt_uid, 'url' => ['view', 'receipt_uid' => $model->receipt_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="receipt-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
