<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Fpp */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Fpps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="fpp-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'kod' => $model->kod], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'kod' => $model->kod], [
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
            'kod',
            'name',
            'additional_details',
            'min_cost_per_unit',
            'max_cost_per_unit',
            'number_of_units',
            'total_cost',
        ],
    ]) ?>

</div>
