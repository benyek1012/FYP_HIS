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
            [
                'attribute' => 'receipt_responsible',
                "format"=>"raw",
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
            ],
            'receipt_serial_number',
            [
                'attribute' => 'Type',
                "format"=>"raw",
                'value'=>function ($data) {
                    $tag = Html::tag('span', !empty($data['receipt_type']) ? 'Receipt' : 'Bill' , [
                        'class' => 'badge badge-' . (!empty($data['receipt_type']) ? 'success' : 'primary')
                    ]);
                    return $tag;

                },
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