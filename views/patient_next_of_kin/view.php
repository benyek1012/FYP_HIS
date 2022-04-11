<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_next_of_kin */

$this->title = $model->nok_uid;
$this->params['breadcrumbs'][] = ['label' => 'Patient Next Of Kins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="patient-next-of-kin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'nok_uid' => $model->nok_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'nok_uid' => $model->nok_uid], [
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
            'nok_uid',
            'patient_uid',
            'nok_name',
            'nok_relationship',
            'nok_phone_number',
            'nok_email:email',
        ],
    ]) ?>

</div>
