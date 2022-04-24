<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_treatment */

$this->title = 'Update Lookup Treatment: ' . $model->treatment_uid;
$this->params['breadcrumbs'][] = ['label' => 'Lookup Treatments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->treatment_uid, 'url' => ['view', 'treatment_uid' => $model->treatment_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lookup-treatment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
