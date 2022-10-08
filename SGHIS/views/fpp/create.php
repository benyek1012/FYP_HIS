<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Fpp */

$this->title = 'Create Fpp';
$this->params['breadcrumbs'][] = ['label' => 'Fpps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fpp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
