<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cancellation */

$this->title = 'Create Cancellation';
$this->params['breadcrumbs'][] = ['label' => 'Cancellations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cancellation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
