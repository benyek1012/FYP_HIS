<?php

use yii\helpers\Html;
use app\models\Lookup_general;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_generalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="lookup-general-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> 

    <p>
        <?= Html::a('Create Lookup General', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <div class="form-group">
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="showForm();">Create</button>
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="hiddenForm();">Cancel</button>
    </div>

    <?php
        //if(!empty($model)){
        //$model_lookup = new Lookup_general();
        //echo $this->render('/lookup_general/_form', ['model' => $model_lookup, 'value' => $model->lookup_general_uid]);
        //}    
    //?>     
    <div id="lookup_form">
        <?php
            $model = new Lookup_general();
            echo $this->render('_form', ['model' => $model, 'value' => $model->lookup_general_uid]);
        ?>
    </div>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{delete}',
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
