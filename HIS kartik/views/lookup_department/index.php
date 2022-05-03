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

$this->title = Yii::t('app','Lookup Departments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-department-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lookup Department', ['create'], ['class' => 'btn btn-success']) ?>
    </p>-->

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
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'department_uid' => $model->department_uid]);
                 }
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'department_code',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_department/department']],
                ]
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'department_uid',
                'visible' => false,
                'hidden' => true,
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'department_name',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_department/department']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'phone_number',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_department/department']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address1',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_department/department']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address2',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_department/department']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address3',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_department/department']],
                ]
            ],
        ],
    ]); ?>

<script>

function showForm() {
    document.getElementById("LOD_div").style.display = "block";
    }

function hiddenForm() {
    document.getElementById("LOD_div").style.display = "none";
}
</script>


</div>
