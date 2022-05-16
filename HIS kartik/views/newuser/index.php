<?php

use app\models\Newuser;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewuserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Newusers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newuser-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Newuser', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <div class="form-group">
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="showForm();"><?php echo Yii::t('app','Create');?></button>
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="hiddenForm();"><?php echo Yii::t('app','Cancel');?></button>
    </div>

    <div id="user_form">
        <?php
            $model = new Newuser();
            echo $this->render('_form', ['model' => $model, 'value' => $model->user_uid]);
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
                    return Url::toRoute([$action, 'user_uid' => $model->user_uid]);
                 }
            ],

            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'user_uid',
                'visible' => false,
                'hidden' => true,
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'username',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/newuser/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'user_password',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/newuser/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'role',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/newuser/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'retire',
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/newuser/user']],
                ]
            ],

            /*'user_uid',
            'username',
            'user_password',
            'role',
            'retire',
            //'authKey',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Newuser $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'user_uid' => $model->user_uid]);
                 }
            ],*/
        ],
    ]); ?>

<script>

function showForm() {
        document.getElementById("user_div").style.display = "block";
    }

    function hiddenForm() {
        document.getElementById("user_div").style.display = "none";
    }
</script>


</div>
