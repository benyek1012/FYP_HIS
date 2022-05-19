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

$this->title = Yii::t('app','Lookup Generals');
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

    <!-- <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app','Create Lookup General'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

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
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'lookup_general_uid',
                'visible' => false,
                'hidden' => true,
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'category',
                'filter'=> $relationship,
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'name',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'long_description',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'recommend',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_general/lookup']],
                ]
            ],
        ],
    ]); ?>

<script>

function showForm() {
        document.getElementById("LOK_div").style.display = "block";
    }

    function hiddenForm() {
        document.getElementById("LOK_div").style.display = "none";
    }
</script>


</div>
