<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */

$this->title = 'Update Bill: ' . $model->bill_uid;
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bill_uid, 'url' => ['view', 'bill_uid' => $model->bill_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bill-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
