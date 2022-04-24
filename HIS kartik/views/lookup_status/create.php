<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_status */

$this->title = 'Create Lookup Status';
$this->params['breadcrumbs'][] = ['label' => 'Lookup Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-status-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
