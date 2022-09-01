<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pekeliling_import */

$this->title = $model->pekeliling_uid;
$this->params['breadcrumbs'][] = ['label' => 'Pekeliling Imports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pekeliling-import-view">

<?=$model->error;?>

<?php if(!empty($model->error)){
    ?>
    <p>
    <br/>
    <?= Html::a('Download', ['pekeliling_import/export', 'id' => $model->pekeliling_uid], ['class' => 'btn btn-info']) ?>
     </p>
    <?php
}
?>

</div>
