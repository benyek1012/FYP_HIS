<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Fpp */

$this->title = 'Update Fpp: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Fpps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'kod' => $model->kod]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fpp-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
