<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="article-view">
    <h1><?php echo Html::encode($model->title) ?></h1>
    <p class="text-muted">
        <small>
            Created at: <b><?php echo Yii::$app->formatter->asRelativeTime($model->created_at) ?></b>
            By: <b><?php echo $model->createdBy->username ?></b>
        </small>
    </p>
    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id === $model->created_by): ?>
    <p>
        <?php echo Html::a('Update', ['update', 'slug' => $model->slug], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'slug' => $model->slug], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
    </p>
    <?php endif ?>

    <div>

        <div>
            <?php echo Html::encode($model->body); ?>
        </div>
    </div>

</div>