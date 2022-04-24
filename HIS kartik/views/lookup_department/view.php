<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_department */

$this->title = $model->department_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lookup-department-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'department_uid' => $model->department_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'department_uid' => $model->department_uid], [
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
            'department_uid',
            'department_code',
            'department_name',
            'phone_number',
            'address1',
            'address2',
            'address3',
        ],
    ]) ?>

</div>
