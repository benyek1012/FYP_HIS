<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use app\models\Bill;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Payments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-index">

    <p>
        <?php 
        $info = Bill::findOne(['rn'=> Yii::$app->request->get('rn')]);
        if(!empty($info)){
        ?>
        <?= Html::a(Yii::t('app','Create Payment'), ['create', 'rn' =>  Yii::$app->request->get('rn')], ['class' => 'btn btn-success']) ?>
        <?php
        }
        ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'emptyText' => 'No Payment Founded!',
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'rn',
                'label' => 'Registeration Number ',
                'format' => 'raw',
                'value'=>function ($data) {
                    return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                },
            ],
            'receipt_type',
            'receipt_content_sum',
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
            'receipt_responsible',
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