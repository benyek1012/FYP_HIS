<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Reminder;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReminderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Reminders');
$this->params['breadcrumbs'][] = $this->title;


?>


<div class="reminder-index">

    <?php if(Yii::$app->session->hasFlash('msg')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('msg') ?>
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
            $model = new reminder();
            echo $this->render('_form', ['model' => $model, 'value' => $model->batch_uid]);
        ?>
    </div>

    <!-- <h1><?= Html::encode($this->title) ?></h1> 

    <p>
        <?= Html::a('Create Batch', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

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

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'batch_datetime',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'reminder1',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],
            
            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'reminder2',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'reminder3',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'responsible',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/reminder/remind']],
                ]
            ],

            //'batch_datetime',
            //'reminder1',
            //'reminder2',
            //'reminder3',
            //'responsible',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Reminder $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'batch_uid' => $model->batch_uid]);
                 }
            ],
        ],
    ]); ?>


</div>

<script>

function showForm() {
    document.getElementById("RM_div").style.display = "block";
}

function hiddenForm() {
    document.getElementById("RM_div").style.display = "none";
}
    // Fade the flash message by 5 sec
window.setTimeout("document.getElementById('flashError').style.display='none';", 5000); 

</script>