<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pekeliling_import */

$this->title = $model->pekeliling_uid;
$this->params['breadcrumbs'][] = ['label' => 'Pekeliling Imports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pekeliling-import-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'pekeliling_uid' => $model->pekeliling_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'pekeliling_uid' => $model->pekeliling_uid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pekeliling_uid',
            'upload_datetime',
            'approval1_responsible_uid',
            'approval2_responsible_uid',
            'file_import',
            'lookup_type',
            'error',
            'scheduled_datetime',
            'executed_datetime',
            'execute_responsible_uid',
            'update_type',
        ],
    ]) ?>

</div>
