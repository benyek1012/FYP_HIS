<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use app\models\NewUser;
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
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'receipt_content_datetime_paid',
                "format"=>"raw",
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
            ],
            [
                'attribute' => 'receipt_serial_number',
                "format"=>"raw",
                'value'=>function ($data) {
                    $tag = Html::tag ( 'span' , $data['receipt_serial_number'] , [
                        // title
                        'title' => $data['receipt_content_description'] ,
                        'data-placement' => 'top' ,
                        'data-toggle'=>'tooltip',
                        'style' => 'white-space:pre;'
                    ] );
                    return $tag;
                },
            ],
            [
                'attribute' => 'receipt_content_sum',
                'value'=>function ($data) {
                    if($data['receipt_type'] == 'bill' || $data['receipt_type'] == 'deposit')
                        return '+'.$data['receipt_content_sum'];
                    else return '-'.$data['receipt_content_sum'];
                },
            ],
            // [
            //     'attribute' => 'rn',
            //     'format' => 'raw',
            //     'value'=>function ($data) {
            //         return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
            //     },
            // ],
            [
                'attribute'=>'receipt_type',
                'filter'=> array(
                    'deposit'=> Yii::t('app','Deposit'),
                    'bill'=> Yii::t('app','Bill'),
                    'refund'=> Yii::t('app','Refund'),
                ),
            ],
            //'receipt_content_description',
            [
                'attribute'=>'receipt_content_payment_method',
                'filter'=> array(
                    'cash'=> Yii::t('app','Cash'),
                    'card'=> Yii::t('app','Debit/Credit Card'),
                    'cheque'=> Yii::t('app','Cheque Numbers'),
                ),
            ],
            'receipt_content_payer_name',
            [
                'attribute' => 'receipt_responsible',
                'value'=>function ($data) {
                    $model_User = NewUser::findOne(['user_uid' => $data['receipt_responsible']]);
                    return $model_User->getName();
                },
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'receipt_uid' => $model->receipt_uid, 'rn' => Yii::$app->request->get('rn')]);
                 }
            ],
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