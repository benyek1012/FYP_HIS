<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\Pekeliling_import;
use yii\helpers\StringHelper;
use app\models\New_user;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Pekeliling_importSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pekeliling Imports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pekeliling-import-index">

    <div class="card card-outline card-info">
        <!-- /.card-header -->
        <div class="card-body">
            <?php echo Yii::t('app','This is a page which user can upload CSV file that could insert into database table directly.')."<br/>".
            Yii::t('app','There are five lookup tables available for users to perform pekeliling imports.')."<br/>".
            Yii::t('app','There are two types of updates: insert or replace existing row / delete all existing data and reinsert.')."<br/>".
            Yii::t('app','Requires 2 admins to approve an error free file and can perform pekeliling execution.')."<br/><br/>",
            Yii::t('app','Below is a button with which users can export data from the lookup ward database to a CSV file.')."<br/>"
            ;?>
             <?= Html::a(Yii::t('app', 'Export'), ['/pekeliling_import/export2'], ['class'=>"btn btn-xs btn-success"]) ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <br />

    <div id="lookup_form">
        <?php
            $model = new Pekeliling_import();
            echo $this->render('_form', ['model' => $model]);
        ?>
    </div>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model) {
            $urlError = Url::toRoute(['pekeliling_import/upload', 'id' => $model['pekeliling_uid']]);
            return [
                'onclick' => "showErrorMsg('{$urlError}');",
                'style' => "cursor:pointer"
            ];
        },
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
                    $path = Yii::$app->basePath . '/web/' . $data->file_import;
                    $array = explode("/", $data['file_import']);
                    if (file_exists($path)) 
                        return Html::a($array[1], \yii\helpers\Url::to(['pekeliling_import/download', 
                            'id'=>$data['pekeliling_uid']]));
                    else return $array[1];
                },
            ],
            [
                'attribute' => 'error',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                "format"=>"raw",
                'value'=>function ($data) {
                    return empty($data['error'])
                    ?  Yii::t('app', 'No Error') :  
                        Html::tag ( 'span' , StringHelper::truncateWords($data['error'], 2) , [
                        // title
                        'title' => Yii::t('app', 'Click row to show whole error'),
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;',
                        'class' => "text-danger font-weight-bold"
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
                            'style' => 'white-space:pre;',
                            'class' => "text-success"
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
                        $check_file_existed = false;
                        $path = Yii::$app->basePath . '/web/' . $model->file_import;
                        if (file_exists($path)) 
                            $check_file_existed = true;

                        return ( empty($model['error']) && empty($model->execute_responsible_uid) && 
                                    !empty($model->approval1_responsible_uid) && 
                                    !empty($model->approval2_responsible_uid) && $check_file_existed)
                        ?  Html::a(Yii::t('app','Execute'), ['pekeliling_import/execute', 'id'=>$model->pekeliling_uid], ['class' => 'btn btn-success btn-xs'])
                        : Html::a(Yii::t('app','Execute'), ['pekeliling_import/execute', 'id'=>$model->pekeliling_uid], ['class' => 'btn btn-secondary btn-xs disabled']);
                    },
                    'my_button2' => function ($url, $model, $key) {
                        $check_file_existed = false;
                        $path = Yii::$app->basePath . '/web/' . $model->file_import;
                        if (file_exists($path)) 
                            $check_file_existed = true;

                        $flag = true;
                        if($model['approval1_responsible_uid'] == Yii::$app->user->identity->id 
                            || $model['approval2_responsible_uid'] == Yii::$app->user->identity->id ) 
                            $flag = false;
                    
                        if(!empty($model['approval1_responsible_uid']) && !empty($model['approval2_responsible_uid']))
                        $flag = false;

                        return ( empty($model['error']) && empty($model->execute_responsible_uid) && $flag && $check_file_existed)
                        ?  Html::a(Yii::t('app','Approve'), ['pekeliling_import/approve', 'id'=>$model->pekeliling_uid], ['class' => 'btn btn-success btn-xs'])
                        : Html::a(Yii::t('app','Approve'), ['pekeliling_import/approve', 'id'=>$model->pekeliling_uid], ['class' => 'btn btn-xs btn-secondary disabled']);
                    },
                ]
            ],
        ],
    ]); ?>


<div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Error Message');?></h3>
            <div class="d-flex justify-content-end">
                <div class="card-tools">
                    <!-- Collapse Button -->
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                            class="fas fa-minus"></i></button>
                </div>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body" id="patient-admission-summary">
            <?php 
                    if(empty($model))
                    {
                        echo Yii::t('app','Error is not found');        
                    }
                   
                ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

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

<script>
function showErrorMsg(url) {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange  = function() {
        if(xhttp.readyState == 4 && xhttp.status == 200){
            document.getElementById("patient-admission-summary").innerHTML = this.responseText;
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
    
}
</script>