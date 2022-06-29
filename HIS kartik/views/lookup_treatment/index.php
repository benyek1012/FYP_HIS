<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\models\Lookup_treatment;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_treatmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Treatment Codes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-treatment-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lookup Treatment', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <!-- If the flash message existed, show it  -->
    <?php if(Yii::$app->session->hasFlash('error_treatment')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('error_treatment') ?>
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
            $model = new Lookup_treatment();
            echo $this->render('_form', ['model' => $model, 'value' => $model->treatment_uid]);
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
                    return Url::toRoute([$action, 'treatment_uid' => $model->treatment_uid]);
                 }
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'treatment_code',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_treatment/treatment']],
                ]
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'treatment_uid',
                'visible' => false,
                'hidden' => true,
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'treatment_name',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_treatment/treatment']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_1_cost_per_unit',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_treatment/treatment']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_2_cost_per_unit',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_treatment/treatment']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'class_3_cost_per_unit',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/lookup_treatment/treatment']],
                ]
            ],
        ],
    ]); ?>

</div>

<script>

function showForm() {
    document.getElementById("LOT_div").style.display = "block";
    }

function hiddenForm() {
    document.getElementById("LOT_div").style.display = "none";
}

// Fade the flash message by 5 sec
window.setTimeout("document.getElementById('flashError').style.display='none';", 5000); 
</script>
