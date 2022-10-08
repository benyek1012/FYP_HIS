<?php

use app\models\Fpp;
use app\models\Lookup_fpp;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_fppSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','FPP Lookup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-fpp-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app','Create Lookup FPP'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>-->

    <?php if(Yii::$app->session->hasFlash('error_fpp')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('error_fpp') ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="showForm();"><?php echo Yii::t('app','Create');?></button>
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="hiddenForm();"><?php echo Yii::t('app','Cancel');?></button>
    </div>

    <div id="lookup_form">
        <?php
            $model = new Lookup_fpp();
            echo $this->render('_form', ['model' => $model]);
        ?>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                // data-key in gridview
                'data' => ['key' => $index],
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            // [
            //     'class' => ActionColumn::className(),
            //     'template' => '{delete}',
            //     'urlCreator' => function ($action, $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'kod' => $model->kod]);
            //     }
            // ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'kod',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_fpp/fpp']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'name',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_fpp/fpp']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'min_cost_per_unit',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_fpp/fpp']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'max_cost_per_unit',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_fpp/fpp']],
                // ]
            ],
        ],
    ]); ?>

</div>

<script>

function showForm() {
    document.getElementById("LOF_div").style.display = "block";
    }

function hiddenForm() {
    document.getElementById("LOF_div").style.display = "none";
}

// Fade the flash message by 5 sec
window.setTimeout("document.getElementById('flashError').style.display='none';", 5000); 
</script>