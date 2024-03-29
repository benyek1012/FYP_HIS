<?php

use app\models\Lookup_ward;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_wardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Ward Codes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-ward-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app','Create Lookup Ward'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>-->

    <?php if(Yii::$app->session->hasFlash('error_ward')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('error_ward') ?>
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
            $model = new Lookup_ward();
            echo $this->render('_form', ['model' => $model, 'value' => $model->ward_uid]);
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
            //         return Url::toRoute([$action, 'ward_uid' => $model->ward_uid]);
            //     }
            // ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'ward_code',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_ward/ward']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'ward_name',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_ward/ward']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'sex',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_ward/ward']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'min_age',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_ward/ward']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'max_age',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_ward/ward']],
                // ]
            ],
        ],
    ]); ?>

</div>

<script>

function showForm() {
    document.getElementById("LOW_div").style.display = "block";
    }

function hiddenForm() {
    document.getElementById("LOW_div").style.display = "none";
}

// Fade the flash message by 5 sec
window.setTimeout("document.getElementById('flashError').style.display='none';", 5000); 
</script>