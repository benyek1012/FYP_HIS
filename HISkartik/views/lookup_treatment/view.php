<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_treatment */

$this->title = $model->treatment_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Treatments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lookup-treatment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'treatment_uid' => $model->treatment_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'treatment_uid' => $model->treatment_uid], [
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
            'treatment_uid',
            'treatment_code',
            'treatment_name',
            'class_1_cost_per_unit',
            'class_2_cost_per_unit',
            'class_3_cost_per_unit',
            'class_Daycare_FPP_per_unit'
        ],
    ]) ?>

</div>
