<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\models\Batch;
use yii\helpers\StringHelper;
use app\models\New_user;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Batches');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-index">
    <div class="card card-outline card-info">
        <!-- /.card-header -->
        <div class="card-body">
            <?php echo Yii::t('app','This is the testing page that user can upload CSV file and insert into database table directly.')."<br/>".
            Yii::t('app','Currently, the CSV file can be traced with batch # and insert into lookup ward table.');?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <br />
    <div id="lookup_form">
        <!-- If the flash message existed, show it  -->
        <?php if(Yii::$app->session->hasFlash('msg')):?>
        <div id="flashError">
            <?= Yii::$app->session->getFlash('msg') ?>
        </div>
        <?php endif; ?>
        <?php
            $model = new Batch();
            echo $this->render('_form', ['model' => $model]);
        ?>
    </div>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'upload_datetime',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $date = new DateTime($data['upload_datetime']);
                    $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                        // title
                        'title' => $date->format('Y-m-d H:i A') ,
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;'
                    ] );
                    return $tag;
                },
            ],
            [
                'attribute' => 'approval1_responsible_uid',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $model_User = New_user::findOne(['user_uid' => $data['approval1_responsible_uid']]);
                    if(!empty($model_User))
                        return $model_User->getName();
                },
            ],
            [
                'attribute' => 'approval2_responsible_uid',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $model_User = New_user::findOne(['user_uid' => $data['approval2_responsible_uid']]);
                    if(!empty($model_User))
                        return $model_User->getName();
                },
            ],
            [
                'attribute' => 'file_import',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'format' => 'raw',
                'value'=>function ($data){ 
                    $array = explode("/", $data['file_import']);
                    return Html::a($array[1], \yii\helpers\Url::to(['batch/download', 'id'=>$data->id]));
                },
            ],
            [
                'attribute' => 'error',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                "format"=>"raw",
                'value'=>function ($data) {
                    return empty($data['error'])
                    ? $data['error'] :  
                        Html::tag ( 'span' , StringHelper::truncateWords($data['error'], 2) , [
                        // title
                        'title' => $data['error'] ,
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;'
                    ] );
                },
            ],
            [
                'attribute' => 'scheduled_datetime',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                "format"=>"raw",
                'value'=>function ($data) {
                    if(empty($data['scheduled_datetime'])) return $data['scheduled_datetime'];
                    else
                    {
                        $date = new DateTime($data['scheduled_datetime']);
                        $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                            // title
                            'title' => $date->format('Y-m-d H:i A') ,
                            'data-placement' => 'top' ,
                            'data-toggle'=>'tooltip',
                            'style' => 'white-space:pre;'
                        ] );
                        return $tag;
                    }
                },
            ],
            [
                'attribute' => 'executed_datetime',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                "format"=>"raw",
                'value'=>function ($data) {
                    if(empty($data['executed_datetime'])) return $data['executed_datetime'];
                    else
                    {
                        $date = new DateTime($data['executed_datetime']);
                        $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                            // title
                            'title' => $date->format('Y-m-d H:i A') ,
                            'data-placement' => 'top' ,
                            'data-toggle'=>'tooltip',
                            'style' => 'white-space:pre;'
                        ] );
                        return $tag;
                    }
                },
            ],
            [
                'attribute' => 'execute_responsible_uid',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $model_User = New_user::findOne(['user_uid' => $data['execute_responsible_uid']]);
                    if(!empty($model_User))
                        return $model_User->getName();
                },
            ],
            [
                'attribute' => 'lookup_type',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    switch ($data['lookup_type']) {
                        case "status":
                            return Yii::t('app','Status Lookup');
                            break;
                        case "treatment":
                            return Yii::t('app','Treatment Codes');
                            break;
                        case "ward":
                            return Yii::t('app','Ward Codes');
                            break;
                        case "department":
                            return  Yii::t('app','Department Codes');
                            break;
                        case "fpp":
                            return Yii::t('app','Full Paying Patient');
                            break;
                    }
                }
            ],
            [
                'attribute' => 'update_type',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    switch ($data['update_type']) {
                        case "insert":
                            return Yii::t('app','Insert / Update');
                            break;
                        case "delete":
                            return Yii::t('app','Delete old codes');
                            break;
                    }
                }
            ],
            [
                'class' => ActionColumn::className(),
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'template' => '{my_button}{my_button2}', 
                'buttons' => [
                    'my_button' => function ($url, $model, $key) {
                        return ( empty($model['error']) && empty($model->execute_responsible_uid) && 
                                    !empty($model->approval1_responsible_uid) && !empty($model->approval2_responsible_uid))
                        ?  Html::a('Execute', ['batch/execute', 'id'=>$model->id], ['class' => 'btn btn-success btn-xs'])
                        : Html::a('Execute', ['batch/execute', 'id'=>$model->id], ['class' => 'btn btn-danger btn-xs disabled']);
                    },
                    'my_button2' => function ($url, $model, $key) {
                        $flag = true;
                        if($model['approval1_responsible_uid'] == Yii::$app->user->identity->id 
                            || $model['approval2_responsible_uid'] == Yii::$app->user->identity->id ) 
                            $flag = false;
                    
                        if(!empty($model['approval1_responsible_uid']) && !empty($model['approval2_responsible_uid']))
                        $flag = false;

                        return ( empty($model['error']) && empty($model->execute_responsible_uid) && $flag)
                        ?  Html::a('Approve', ['batch/approve', 'id'=>$model->id], ['class' => 'btn btn-success btn-xs'])
                        : Html::a('Approve', ['batch/approve', 'id'=>$model->id], ['class' => 'btn btn-danger btn-xs disabled']);
                    },
                ]
            ],
        ],
    ]); ?>


</div>

<?php
$js = <<< SCRIPT
/* To initialize BS3 tooltips set this below */
$(function () { 
    $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
        html:true
    });
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs ( $js );
?>