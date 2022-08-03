<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Batch */

$this->title = Yii::t('app','Create Batch');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Batches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
