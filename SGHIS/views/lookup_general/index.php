<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Lookup_general;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_generalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','General Lookup');
$this->params['breadcrumbs'][] = $this->title;

$rows_relationship = (new \yii\db\Query())
->select('category')
->from('lookup_general')
->all();

$relationship = array();
foreach($rows_relationship as $row_relationship){
    $relationship[$row_relationship['category']] = $row_relationship['category'];
} 

// removes duplicate values from an array
$relationship = array_unique($relationship);
?>
<div class="lookup-general-index">

    <!-- If the flash message existed, show it  -->
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
            $model = new Lookup_general();
            echo $this->render('_form', ['model' => $model, 'value' => $model->lookup_general_uid]);
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
            
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lookup_general_uid' => $model->lookup_general_uid]);
                 }
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'code',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'category',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'filter'=> $relationship,
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'name',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'long_description',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'recommend',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],
        ],
    ]); ?>



</div>


<script>
function showForm() {
    document.getElementById("LOK_div").style.display = "block";
}

function hiddenForm() {
    document.getElementById("LOK_div").style.display = "none";
}

// Fade the flash message by 5 sec
window.setTimeout("document.getElementById('flashError').style.display='none';", 5000); 

</script>