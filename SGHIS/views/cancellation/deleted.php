<?php

use app\controllers\SiteController;
use app\models\Cancellation;
use app\models\Bill;
use app\models\New_user;
use app\models\Receipt;
use yii\data\ActiveDataProvider;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Deleted');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php /*
<body>
    <div id="card1" class="container-fluid">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Bill Cancellation');?></h3>
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
            <div class="card-body">
        <?php 
            if(!empty($model_bill))
            {
                $dataProvider1 = new ActiveDataProvider([
                    'query'=> Cancellation::find()->where(['table' => 'bill'])
                    // ->orderBy(['entry_datetime' => SORT_DESC])
                    // 'pagination'=>['pageSize'=>5],
                ]);

                echo $this->render('/cancellation/deleted_bill', ['dataProvider'=>$dataProvider1]);
        ?>
        <?php
            } 
            else echo Yii::t('app','Bill cancellation record is not found');
        ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Receipt Cancellation');?></h3>
                <div class="card-tools">
                    <!-- Collapse Button -->
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                            class="fas fa-minus"></i></button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <!-- This is the form that shows patient information which can directly updating-->
        <?php
            if(!empty($model_receipt))
            {
                $dataProvider1 = new ActiveDataProvider([
                    'query'=> Cancellation::find()->where(['table' => 'receipt'])
                    // ->orderBy(['entry_datetime' => SORT_DESC])
                    // 'pagination'=>['pageSize'=>5],
                ]);

                echo $this->render('/cancellation/deleted_receipt', ['dataProvider'=>$dataProvider1]);
        ?>
        <?php
            } 
            else echo Yii::t('app','Receipt cancellation record is not found');
        ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</body>
*/ ?>
<?php
$dataProvider = new ActiveDataProvider([
    'query'=> Cancellation::find()
    ->orderBy(['deleted_datetime' => SORT_DESC]),
    'pagination'=>['pageSize'=>15],
]);
?>
<div class="deleted-index">
    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions'=>function($data, $model){
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('+0800')); //GMT
            $todayDate =  $date->format('Y-m-d H:i A');

            $last3Days = Date('Y-m-d H:i A', strtotime('-3 days'));
            if($data['deleted_datetime'] >= $last3Days && $data['deleted_datetime'] <= $todayDate){
                return ['class' => 'bg-warning'];
            }
        },
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Cancellation record is not founded'),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'rn',
                'format' => 'raw',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data, $key, $index){ 
                    $model_bill = Bill::findOne(['bill_uid' => $data->cancellation_uid, 'deleted' => '1']);
                    $model_receipt = Receipt::findOne(['receipt_uid' => $data->cancellation_uid]);

                    if(!empty($model_bill)){
                            return $model_bill->rn;
                    }

                    if(!empty($model_receipt)){
                            return $model_receipt->rn;
                    }
                },
            ],

            [
                'label' => 'table',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Type'),
                'value'=>function ($data) {
                    return $data['table'];
                }
            ], 

            [
                'label' => 'receipt_type',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Payment Type'),
                'value'=>function ($data) {
                    $model_receipt = Receipt::findOne(['receipt_uid' => $data->cancellation_uid]);

                    if(!empty($model_receipt)){
                        if($model_receipt->receipt_type == "bill")
                            return  Yii::t('app','Bill Payment');
                        else if($model_receipt->receipt_type == "deposit")
                            return  Yii::t('app','Deposit Payment');
                        else if($model_receipt->receipt_type == "refund")
                            return  Yii::t('app','Refund');
                        else if($model_receipt->receipt_type == 'exception')
                            return Yii::t('app','Exception');
                    }
                    
                }
            ], 

            [
                'attribute' => 'deleted_datetime',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $date = new DateTime($data['deleted_datetime']);
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
                'label' => 'reason',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Reason'),
                'value'=>function ($data) {
                    return $data['reason'];
                }
            ], 

            [
                'attribute' => 'responsible_uid',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $model_User = New_user::findOne(['user_uid' => $data['responsible_uid']]);
                    if(!empty($model_User))
                        return $model_User->getName();
                },
                'label' => Yii::t('app','Responsible')
            ],
        ],
    ]);
?>
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