<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Newuser */

$this->title = 'Update Newuser: ' . $model->user_uid;
$this->params['breadcrumbs'][] = ['label' => 'Newusers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_uid, 'url' => ['view', 'user_uid' => $model->user_uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="newuser-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
