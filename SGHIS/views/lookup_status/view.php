<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_status */

$this->title = $model->status_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lookup-status-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'status_uid' => $model->status_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'status_uid' => $model->status_uid], [
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
            'status_uid',
            'status_code',
            'status_description',
            'class_1a_ward_cost',
            'class_1b_ward_cost',
            'class_1c_ward_cost',
            'class_2_ward_cost',
            'class_3_ward_cost',
            //'class_Daycare_FPP_ward_cost'
        ],
    ]) ?>

</div>
