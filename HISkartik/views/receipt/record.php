<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use app\models\New_user;
use app\models\Receipt;
use app\models\Bill;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Cancellation;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
if(!empty($temp))
    $temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);
else $temp2 = Patient_information::findOne(['patient_uid'=> Yii::$app->request->get('id')]);

$this->title = Yii::t('app','Transaction Records');
if($temp2->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="receipt-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Payment record is not found'),
      //  'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'rn',
                'format' => 'raw',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);

                    if(!empty($model_receipt)){
                        $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_receipt->receipt_uid]);

                        foreach($model_cancellation as $model_cancellation){
                            $model_new_receipt = Receipt::findOne(['receipt_uid' => $model_cancellation->replacement_uid]);
                        }

                        if(!empty($model_cancellation)){
                            return $data['rn'];
                        }
                        else if(!empty($model_receipt->receipt_type)){
                            return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                        }
                    }
                    // Bill
                    else{
                        return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                    }
                },
                'label' => Yii::t('app','Registration Number (R/N)')
            ],
            [
                'attribute' => 'receipt_content_datetime_paid',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $date = new DateTime($data['receipt_content_datetime_paid']);
                    $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                        // title
                        'title' => $date->format('Y-m-d H:i A') ,
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;'
                    ] );
                    return $tag;
                },
                'label' => Yii::t('app', 'Datetime Paid')
            ],
            [
                'attribute' => 'receipt_serial_number',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => 'ID'
            ],   
            [
                'attribute' => 'receipt_content_sum',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    if($data['receipt_type'] == 'bill' || $data['receipt_type'] == 'deposit')
                        return '+ '.Yii::$app->formatter->asCurrency($data['receipt_content_sum']);
                    else return '- '.Yii::$app->formatter->asCurrency($data['receipt_content_sum']);
                },
                'label' => Yii::t('app','Total')
            ],
            [
                'attribute' => 'receipt_type',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Type'),
                'value'=>function ($data) {
                    if($data['receipt_type'] == "bill")
                        return  Yii::t('app','Bill Payment');
                    else if($data['receipt_type'] == "deposit")
                        return  Yii::t('app','Deposit Payment');
                    else if($data['receipt_type'] == "refund")
                        return  Yii::t('app','Refund');
                    else if($data['receipt_type'] == 'exception')
                        return Yii::t('app','Exception');
                }
            ],  
            [
                'attribute' => 'receipt_content_payment_method',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Payment Method'),
                'value'=>function ($data) {
                    if($data['receipt_content_payment_method'] == 'cash')
                        return Yii::t('app','Cash');
                    else if($data['receipt_content_payment_method'] == 'card')
                        return Yii::t('app','Debit/Credit Card');
                    else if($data['receipt_content_payment_method'] == 'cheque')
                        return Yii::t('app','Cheque Numbers');
                },
            ],   
            [
                'attribute' =>  'receipt_content_payer_name',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Payer Name')
            ],   
            [
                'attribute' => 'receipt_responsible',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $model_User = New_user::findOne(['user_uid' => $data['receipt_responsible']]);
                    if(!empty($model_User) && !empty($data['receipt_type']))
                        return $model_User->getName();
                    else
                    {
                        
                        $name = '';
                        $bill = Bill::findOne(['rn' => $data['rn'], 'deleted' => 0]);
                        if(!empty($bill->bill_print_responsible_uid))
                        {
                            $model_User = New_user::findOne(['user_uid' => $bill->bill_print_responsible_uid]);
                            if(!empty($model_User))
                                $name = ' , '.$model_User->getName();
                        }
                        return $model_User->getName() . $name;
                    }
                },
                'label' => Yii::t('app','Responsible')
            ],
        
            [
                'attribute' => 'Type',
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);

                    if(!empty($model_receipt)){
                        $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_receipt->receipt_uid]);

                        foreach($model_cancellation as $model_cancellation){
                            $model_new_receipt = Receipt::findOne(['receipt_uid' => $model_cancellation->replacement_uid]);
                        }

                        if(!empty($model_cancellation)){
                            $type = 'Receipt Cancelled';
                            $title = $model_new_receipt->receipt_serial_number;
                            $class = 'badge-danger';
                        }
                        else if(!empty($model_receipt->receipt_type)){
                            $type = 'Receipt';
                            $title = '';
                            $class = 'badge-success';
                        }
                    }
                    // Bill
                    else{
                        $type = 'Bill';
                        $title = '';
                        $class = 'badge-primary';
                    }

                    $tag = Html::tag('span', Yii::t('app', $type) , [
                        'class' => 'badge ' . $class,
                        'title' => $title,
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;'
                    ]);
                    return $tag;

                },
                'label' => Yii::t('app','Type')
            ],

            // [
            //     'class' => ActionColumn::className(),
            //     'template' => '{view}',
            //     'urlCreator' => function ($action, $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'receipt_uid' => $model->receipt_uid, 'rn' => Yii::$app->request->get('rn')]);
            //      }
            // ],
        ],
    ]); ?>


    <!-- Temporary hide the print button
     <?php 
        if(!empty(Receipt::findOne(['rn' => Yii::$app->request->get('rn')])))
        {
            $form = kartik\form\ActiveForm::begin([
                'id' => 'print-record-form',
            ]); ?>
    <?= Html::submitButton(Yii::t('app','Print'), ['class' => 'btn btn-success']) ?>
    <?php kartik\form\ActiveForm::end(); 
        } ?> -->

</div>

<?php
    $js = <<<SCRIPT
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