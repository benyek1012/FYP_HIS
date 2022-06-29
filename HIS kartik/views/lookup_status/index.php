<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\models\Lookup_status;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_statusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Status Lookup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-status-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app','Create Lookup Status'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php if(Yii::$app->session->hasFlash('error_status')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('error_status') ?>
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
            $model = new Lookup_status();
            echo $this->render('_form', ['model' => $model, 'value' => $model->status_uid]);
        ?>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'status_uid' => $model->status_uid]);
                 }
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'status_code',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_status/status']],
                ]
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'status_uid',
                'visible' => false,
                'hidden' => true,
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'status_description',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_status/status']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_1a_ward_cost',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_status/status']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_1b_ward_cost',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_status/status']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_1c_ward_cost',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_status/status']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_2_ward_cost',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_status/status']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_3_ward_cost',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_status/status']],
                ]
            ],
        ],
    ]); ?>


</div>

<script>

function showForm() {
    document.getElementById("LOS_div").style.display = "block";
    }

function hiddenForm() {
    document.getElementById("LOS_div").style.display = "none";
}

// Fade the flash message by 5 sec
window.setTimeout("document.getElementById('flashError').style.display='none';", 5000); 
</script>
