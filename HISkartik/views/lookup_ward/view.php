<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_ward */

$this->title = $model->ward_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Wards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lookup-ward-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ward_uid' => $model->ward_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ward_uid' => $model->ward_uid], [
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
            'ward_uid',
            'ward_code',
            'ward_name',
            'sex',
            'min_age',
            'max_age',
        ],
    ]) ?>

</div>
