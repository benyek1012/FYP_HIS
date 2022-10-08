<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_department */

$this->title = 'Update Lookup Department: ' . $model->department_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->department_uid, 'url' => ['view', 'department_uid' => $model->department_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-department-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
