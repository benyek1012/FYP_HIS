<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\models\Batch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Batches';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-index">

    <div id="lookup_form">
        <?php
            $model = new Batch();
            echo $this->render('_form', ['model' => $model]);
        ?>
    </div>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'batch',
            [
                'attribute' => 'file_import',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a($data->file_import, \yii\helpers\Url::to(['/lookup_ward/index', 'Lookup_wardSearch[sex]' => $data->batch]));
                }
            ],
         
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
