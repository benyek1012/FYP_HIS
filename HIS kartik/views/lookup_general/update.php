<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_general */

$this->title = 'Update Lookup General: ' . $model->code;
$this->params['breadcrumbs'][] = ['label' => 'Maintainance: General Lookup', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lookup_general_uid, 'url' => ['view', 'lookup_general_uid' => $model->lookup_general_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-general-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
