<?php

use app\models\New_user;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewuserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','User Management');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newuser-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Newuser', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php if(Yii::$app->session->hasFlash('error_user')):?>
        <div id = "flashError">
            <?= Yii::$app->session->getFlash('error_user') ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="showForm();"><?php echo Yii::t('app','Create');?></button>
        <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;"
            onclick="hiddenForm();"><?php echo Yii::t('app','Cancel');?></button>
    </div>

    <div id="user_form">
        <?php
            // $model = new New_user();
            echo $this->render('_form', ['model' => $model, 'value' => $model->user_uid]);
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
                'headerOptions' => ['style' => 'width:70px'],
                'template' => '{delete} {update}', // {change_password}
                // 'buttons' => [
                //     'change_password' => function ($url, $model, $key) {
                //         return Html::a('<span class="glyphicon glyphicon glyphicon-picture" aria-hidden="true"></span>', ['update', 'user_uid' => $model->user_uid]);
                //     },
                // ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'user_uid' => $model->user_uid]);
                 }
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'username',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/new_user/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'user_password',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'visible' => false,
                'hidden' => true,
                'editableOptions' =>  [                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/new_user/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'role_cashier',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => [0,1],                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/new_user/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'role_clerk',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => [0,1],                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/new_user/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'role_admin',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => [0,1],                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/new_user/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'role_guest_print',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => [0,1],                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/new_user/user']],
                ]
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'retire',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'editableOptions' =>  [
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => [0,1],                
                    'asPopover' => false,
                    'formOptions' => ['action' => ['/new_user/user']],
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


</div>

<script>

function showForm() {
    document.getElementById("user_div").style.display = "block";
}

function hiddenForm() {
    document.getElementById("user_div").style.display = "none";
}

// Fade the flash message by 5 sec
window.setTimeout("document.getElementById('flashError').style.display='none';", 5000); 
</script>
