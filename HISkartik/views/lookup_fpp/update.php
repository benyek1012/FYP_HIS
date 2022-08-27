<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_fpp */

$this->title = 'Update Lookup Fpp: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Fpps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'kod' => $model->kod]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-fpp-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
