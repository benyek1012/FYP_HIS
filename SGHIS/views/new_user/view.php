<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Newuser */

$this->title = $model->user_uid;
$this->params['breadcrumbs'][] = ['label' => 'Newusers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="newuser-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'user_uid' => $model->user_uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'user_uid' => $model->user_uid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user_uid',
            'username',
            'user_password',
            'role_cashier',
            'role_clerk',
            'role_admin',
            'role_guest_print',
            'Case_Note',
            'Registration',
            'Charge_Sheet',
            'Sticker_Label',
            'retire',
            'authKey',
        ],
    ]) ?>

</div>
