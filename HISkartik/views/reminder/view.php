<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Reminder */

$this->title = $model->batch_datetime;
$this->params['breadcrumbs'][] = ['label' => 'Reminders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="reminder-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'batch_datetime' => $model->batch_datetime], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'batch_datetime' => $model->batch_datetime], [
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
            'batch_datetime',
            'reminder1',
            'reminder2',
            'reminder3',
            'responsible',
        ],
    ]) ?>

</div>
