<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment_details */

$this->title = 'Create Treatment Details';
$this->params['breadcrumbs'][] = ['label' => 'Treatment Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="treatment-details-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
