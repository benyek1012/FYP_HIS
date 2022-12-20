<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use app\models\New_user;
use app\models\Bill;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Receipt;
use app\models\Cancellation;
use app\models\SerialNumber;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn'])
    $this->title = Yii::t('app','Other Payments');
else
    $this->title = Yii::t('app','Payments');


if($temp2->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-index">
    <div class="row">
        <div class="col-lg-12">
            <?php 
            if(!empty(Yii::$app->request->get('rn'))){
        ?>
            <?= \hail812\adminlte\widgets\Callout::widget([
                'type' => 'info',
               // 'head' => 'I am a danger callout!',
               'body' => '<b>'.Yii::t('app','Billable Total').'</b>: '.(new Patient_admission()) -> get_billable_sum(Yii::$app->request->get('rn')).
               '<br/><b>'.Yii::t('app','Amount Due').'</b>: '.Yii::$app->formatter->asCurrency((new Bill()) -> getAmtDued(Yii::$app->request->get('rn'))).
               '<br/><b>'.Yii::t('app','Unclaimed Balance').'</b>: '.Yii::$app->formatter->asCurrency((new Bill()) -> getUnclaimed(Yii::$app->request->get('rn')))
            ]) ?>
            <?php } ?>
        </div>
    </div>

    <p>
        <?php
            if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn'])
            {
                echo Html::a(Yii::t('app','Create Other Payment'),
                    ['create', 'rn' =>  Yii::$app->request->get('rn')], ['class' => 'btn btn-success']);
            }
            else
                echo Html::a(Yii::t('app','Create Payment'),
                    ['create', 'rn' =>  Yii::$app->request->get('rn')], ['class' => 'btn btn-success']);
        ?>
    </p>


    <!-- If the flash message existed, show it  -->
    <!-- <?php if(Yii::$app->session->hasFlash('msg')):?>
    <div id="flashError">
    <?= Yii::$app->session->getFlash('msg') ?>
    </div>
    <?php endif; ?> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if(Yii::$app->session->hasFlash('cancellation_error')):?>
    <div id="flashError">
        <?= Yii::$app->session->getFlash('cancellation_error') ?>
    </div>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Payment record is not found'),
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'expandTitle' => Yii::t('app', 'Cancellation'),
                'collapseTitle' => Yii::t('app', 'Cancellation'),
                'expandAllTitle' => Yii::t('app', 'Cancellation'),
                'collapseAllTitle' => Yii::t('app', 'Cancellation'),
                'expandIcon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>',
                'collapseIcon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>',
                'value' => function($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detailRowCssClass' => GridView::TYPE_LIGHT,
                'disabled' => function($data) {
                    // $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);
                    $model_receipt = Receipt::findOne(['receipt_uid' => $data['receipt_uid']]);

                    if(!empty($model_receipt)){
                        $model_receipt->receipt_serial_number = SerialNumber::getSerialNumber("receipt");
                        $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_receipt->receipt_uid]);
                        $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn'), 'deleted' => 0]);
                        $model_bill_del = Bill::findAll(['rn' => Yii::$app->request->get('rn'), 'deleted' => 1]);

                        // Receipt Cancelled
                        if(!empty($model_cancellation)){
                            return true;
                        }
                        // else if(!empty($model_bill) && $model_receipt->receipt_type != 'bill'){
                        //     return true;
                        // }
                        // else if(!empty($model_bill_del) && $model_receipt->receipt_type == 'bill'){
                        //     foreach($model_bill_del as $bill){
                        //         if($bill->bill_print_id == $model_receipt->receipt_content_bill_id){
                        //             return true;
                        //         }
                        //     }
                        // }
                        else if(!empty($model_receipt->receipt_type)){
                            return false;
                        }
                    }
                    // Bill
                    else{
                        return true;
                    }

                },
                'detail' => function($data, $index) {
                    // $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);\
                    $model_receipt = Receipt::findOne(['receipt_uid' => $data['receipt_uid']]);
                    if(!empty($model_receipt)){
                        $model_receipt->receipt_serial_number = SerialNumber::getSerialNumber("receipt");

                        $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_receipt->receipt_uid]);

                        if(!empty($model_cancellation)){
                            return null;
                        }
                        else if(!empty($model_receipt->receipt_type)){
                            if($model_receipt->receipt_type == 'bill'){
                                $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn'), 'deleted' => 0]);
                
                                return Yii::$app->controller->renderPartial('create', [
                                    'model' => $model_receipt,
                                    'cancellation' => true,
                                    'model_bill' => $model_bill,
                                    'index' => $index,
                                ]);
                            }   
                            else{
                                return Yii::$app->controller->renderPartial('create', [
                                    'model' => $model_receipt,
                                    'cancellation' => true,
                                    'model_bill' => null,
                                    'index' => $index,
                                ]);
                            }
                        }
                    }
                    // Bill
                    else{
                        return null;
                    }
                },
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
                "format"=>"raw",
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    $tag = Html::tag ( 'span' , 
                        !empty($data['receipt_serial_number']) ? $data['receipt_serial_number'] : '(not printed)'
                        , [
                            // title
                            'title' => $data['receipt_content_description'] ,
                            'data-placement' => 'top' ,
                            'data-toggle'=>'tooltip',
                            'style' => 'white-space:pre;'
                        ] );
                    return $tag;
                },
                'label' => 'ID'
            ],
            [
                'attribute' => 'receipt_content_sum',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    if($data['receipt_type'] == 'bill' || $data['receipt_type'] == 'deposit' || $data['receipt_type'] == 'exception')
                        return '- '.Yii::$app->formatter->asCurrency($data['receipt_content_sum']);
                    else return '+ '.Yii::$app->formatter->asCurrency($data['receipt_content_sum']);
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
                'attribute'=>'receipt_content_payment_method',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value'=>function ($data) {
                    if($data['receipt_content_payment_method'] == 'cash')
                        return Yii::t('app','Cash');
                    else if($data['receipt_content_payment_method'] == 'card')
                        return Yii::t('app','Debit/Credit Card');
                    else if($data['receipt_content_payment_method'] == 'cheque')
                        return Yii::t('app','Cheque Numbers');
                },
                // 'filter'=> array(
                //     'cash'=> Yii::t('app','Cash'),
                //     'card'=> Yii::t('app','Debit/Credit Card'),
                //     'cheque'=> Yii::t('app','Cheque Numbers'),
                // ),
                'label' => Yii::t('app','Payment Method')
            ],
            [
                'attribute'=> 'receipt_content_payer_name',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'label' => Yii::t('app','Payer Name')
            ],

            [
                'attribute'=> 'kod_akaun',
                'headerOptions'=>['style'=>'max-width: 100px;'],
                'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                'value' => function($data){
                    // $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);
                    $model_receipt = Receipt::findOne(['receipt_uid' => $data['receipt_uid']]);

                    if(!empty($model_receipt)){
                        return $model_receipt->kod_akaun;
                    }
                    else{
                        return null;
                    }
                },
                'label' => Yii::t('app','Account Code')
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
                    // $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);
                    $model_receipt = Receipt::findOne(['receipt_uid' => $data['receipt_uid']]);
                    $model_bill = Bill::findOne(['bill_generation_datetime' => $data['receipt_content_datetime_paid']]);

                    if(!empty($model_receipt)){
                        $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_receipt->receipt_uid]);

                        foreach($model_cancellation as $model_cancellation){
                            $model_new_receipt = Receipt::findOne(['receipt_uid' => $model_cancellation->replacement_uid]);
                        }

                        // echo '<pre>';
                        // var_dump($model_new_receipt);
                        // echo '</pre>';
                        // exit;

                        if(!empty($model_cancellation)){
                            $type = 'Receipt Cancelled';
                            if(!empty($model_new_receipt->receipt_serial_number)){
                                $title = $model_new_receipt->receipt_serial_number;
                            }
                            else{
                                $title = '';
                            }
                            
                            $class = 'badge-danger';
                        }
                        else if(!empty($model_receipt->receipt_type)){
                            $type = 'Receipt';
                            $title = $model_receipt->receipt_content_description;
                            $class = 'badge-success';
                        }
                    }
                    // Bill
                    // else{
                    //     $type = 'Bill';
                    //     $title = '';
                    //     $class = 'badge-primary';
                    // }
                    else if(!empty($model_bill)){
                        if($model_bill->deleted == 1){
                            $type = 'Bill Cancelled';
                            $title = '';
                            $class = 'badge-danger';
                        }
                        else{
                            $type = 'Bill';
                            $title = $model_bill->description;
                            $class = 'badge-primary';
                        }
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