<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_general */

$this->title = 'Update Lookup General: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Generals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'lookup_general_uid' => $model->lookup_general_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-general-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
