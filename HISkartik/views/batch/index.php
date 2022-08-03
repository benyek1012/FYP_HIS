<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\models\Batch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Batches');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-index">
    <div class="card card-outline card-info">
        <!-- /.card-header -->
        <div class="card-body">
            <?php echo Yii::t('app','This is the testing page that user can upload CSV file and insert into database table directly.')."<br/>".
            Yii::t('app','Currently, the CSV file can be traced with batch # and insert into lookup ward table.');?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <br/>
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