<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_ward */

$this->title = 'Update Lookup Ward: ' . $model->ward_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Wards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ward_uid, 'url' => ['view', 'ward_uid' => $model->ward_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-ward-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
