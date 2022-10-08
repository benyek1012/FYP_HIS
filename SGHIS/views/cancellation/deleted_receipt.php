<?php

use kartik\grid\GridView;
use app\models\New_user;
use app\models\Receipt;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="deleted-bill-index">
    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Receipt cancellation record is not founded'),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'rn',
                'format' => 'raw',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data, $key, $index){ 
                    $model_receipt = Receipt::findOne(['receipt_uid' => $data->cancellation_uid]);

                    if(!empty($model_receipt)){
                        // $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_admission->rn]);

                        // if(!empty($model_bill)){
                            return $model_receipt->rn;
                        // }
                        // else{
                        //     return ($index == 0) ?
                        //     '<b>'.Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']])).'</b>'
                        //     :  Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                        // }
                    }

                    // return ($index == 0) ?
                    // '<b>'.Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']])).'</b>'
                    // :  Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                },
            ],

            [
                'label' => 'receipt_type',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Type'),
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