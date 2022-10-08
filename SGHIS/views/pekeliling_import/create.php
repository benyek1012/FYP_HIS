<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pekeliling_import */

$this->title = 'Create Pekeliling Import';
$this->params['breadcrumbs'][] = ['label' => 'Pekeliling Imports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pekeliling-import-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
