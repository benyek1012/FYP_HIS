<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use app\models\New_user;
use app\models\Bill;
use app\models\Patient_admission;
use app\models\Patient_information;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

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
        <?= Html::a(Yii::t('app','Create Payment'), ['create', 'rn' =>  Yii::$app->request->get('rn')], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Payment record is not found'),
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
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
                    $tag = Html::tag('span', !empty($data['receipt_type']) ?  Yii::t('app','Receipt') :  Yii::t('app','Bill') , [
                        'class' => 'badge badge-' . (!empty($data['receipt_type']) ? 'success' : 'primary')
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