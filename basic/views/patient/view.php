<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Patient */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="patient-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'patient_uid' => $model->patient_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'patient_uid' => $model->patient_uid], [
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
            'patient_uid',
            'first_reg_date',
            'nric',
            'nationality',
            'name',
            'sex',
            'phone_number',
            'email:email',
            'address1',
            'address2',
            'address3',
            'job',
        ],
    ]) ?>

</div>
