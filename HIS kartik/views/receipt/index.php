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
    $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/index', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/index', 'id' => $temp2->patient_uid]];
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
               'body' => '<b>'.Yii::t('app','Sum of Deposit').'</b> : '.Yii::$app->formatter->asCurrency(Bill::getSumDeposit(Yii::$app->request->get('rn'))).
               '<br/><b>'.Yii::t('app','Billable Total').'</b>: '.Patient_admission::get_billable_sum(Yii::$app->request->get('rn')).
               '<br/><b>'.Yii::t('app','Amount Due').'</b>: '.Yii::$app->formatter->asCurrency(Bill::getAmtDued(Yii::$app->request->get('rn'))).
               '<br/><b>'.Yii::t('app','Unclaimed Balance').'</b>: '.Yii::$app->formatter->asCurrency(Bill::getUnclaimed(Yii::$app->request->get('rn')))
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
        'emptyText' => Yii::t('app','Payment record is not founded'),
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'rn',
                'format' => 'raw',
                'value'=>function ($data) {
                    return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                },
            ],
            
            'receipt_type',

            [
                'attribute' => 'receipt_content_sum',
                'value'=>function ($data) {
                    if($data['receipt_type'] == 'bill' || $data['receipt_type'] == 'deposit')
                        return '+'.$data['receipt_content_sum'];
                    else return '-'.$data['receipt_content_sum'];
                },
            ],
          //  'receipt_content_bill_id',
            //'receipt_content_description',
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
            'receipt_content_payer_name',
            'receipt_content_payment_method',
            //'card_no',
            //'cheque_number',
            [
                'attribute' => 'receipt_responsible',
                'value'=>function ($data) {
                    $model_User = NewUser::findOne(['user_uid' => $data['receipt_responsible']]);
                    return $model_User->username;
                },
            ],
            'receipt_serial_number',
            [
                'class' => ActionColumn::className(),
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'receipt_uid' => $model->receipt_uid, 'rn' => Yii::$app->request->get('rn')]);
                 }
            ],
        ],
    ]); ?>

    <?php 
    //  $dataProvider2 = new ActiveDataProvider([
    //     'query'=> Bill::find()->where(['rn'=>Yii::$app->request->get('rn')]),
    //     'pagination'=>['pageSize'=>3],
    //     ]);
    // echo $this->render('/bill/index', ['dataProvider'=>$dataProvider2]);
    
    ?>


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