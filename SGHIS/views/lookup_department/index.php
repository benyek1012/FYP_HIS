<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\models\Lookup_department;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_departmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app','Department Codes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-department-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lookup Department', ['create'], ['class' => 'btn btn-success']) ?>
    </p>-->

    <!-- If the flash message existed, show it  -->
    <?php if(Yii::$app->session->hasFlash('error_department')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('error_department') ?>
        </div>
    <?php endif;?>

    <div class="form-group">
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="showForm();"><?php echo Yii::t('app','Create');?></button>
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="hiddenForm();"><?php echo Yii::t('app','Cancel');?></button>
    </div>

    <div id="lookup_form">
        <?php
            $model = new Lookup_department();
            echo $this->render('_form', ['model' => $model, 'value' => $model->department_uid]);
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
            //         return Url::toRoute([$action, 'department_uid' => $model->department_uid]);
            //      }
            // ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'department_code',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_department/department']],
                // ]
            ],
            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'department_name',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_department/department']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'phone_number',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_department/department']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address1',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_department/department']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address2',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_department/department']],
                // ]
            ],

            [
                // 'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address3',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                // 'editableOptions' =>  [                
                //     'asPopover' => false,
                //     'formOptions' => ['action' => ['/lookup_department/department']],
                // ]
            ],
        ],
    ]); ?>


</div>

<script>

function showForm() {
    document.getElementById("LOD_div").style.display = "block";
    }

function hiddenForm() {
    document.getElementById("LOD_div").style.display = "none";
}

window.setTimeout("document.getElementById('flashError').style.display='none';", 5000);
</script>
