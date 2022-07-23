<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Ward */

$this->title = 'Update Ward: ' . $model->ward_uid;
$this->params['breadcrumbs'][] = ['label' => 'Wards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ward_uid, 'url' => ['view', 'ward_uid' => $model->ward_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ward-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
