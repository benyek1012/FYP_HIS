<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_general */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Generals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lookup-general-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lookup_general_uid' => $model->lookup_general_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lookup_general_uid' => $model->lookup_general_uid], [
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
            'lookup_general_uid',
            'code',
            'category',
            'name',
            'long_description',
            'recommend',
        ],
    ]) ?>

</div>
