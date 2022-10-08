<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pekeliling_import */

$this->title = 'Update Pekeliling Import: ' . $model->pekeliling_uid;
$this->params['breadcrumbs'][] = ['label' => 'Pekeliling Imports', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pekeliling_uid, 'url' => ['view', 'pekeliling_uid' => $model->pekeliling_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pekeliling-import-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
