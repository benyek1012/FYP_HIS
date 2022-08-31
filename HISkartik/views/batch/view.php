<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Batch */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Batches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="batch-view">

    <?=$model->error;?>

    <?php if(!empty($model->error)){
        ?>
        <p>
        <br/>
        <?= Html::a('Download', ['batch/export', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
         </p>
        <?php
    }
    ?>

</div>
