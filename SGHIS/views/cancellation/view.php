<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Cancellation */

$this->title = $model->cancellation_uid;
$this->params['breadcrumbs'][] = ['label' => 'Cancellations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cancellation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cancellation_uid' => $model->cancellation_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cancellation_uid' => $model->cancellation_uid], [
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
            'cancellation_uid',
            'table',
            'reason',
            'replacement_uid',
        ],
    ]) ?>

</div>
