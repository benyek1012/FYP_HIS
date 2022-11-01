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
use app\models\ReceiptSearch;

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

$modelBill = (new \yii\db\Query())
->select('bill.rn')
->from('bill')
->join('INNER JOIN', 'patient_admission', 'patient_admission.rn = bill.rn')
->join('INNER JOIN', 'patient_information', 'patient_information.patient_uid = patient_admission.patient_uid')
->where(['patient_information.patient_uid' => Yii::$app->request->get('id')])
->andWhere('bill.bill_generation_datetime is not null')
->distinct();

$modelReceipt = (new \yii\db\Query())
->select('receipt.rn')
->from('receipt')
->join('INNER JOIN', 'patient_admission', 'patient_admission.rn = receipt.rn')
->join('INNER JOIN', 'patient_information', 'patient_information.patient_uid = patient_admission.patient_uid')
->where(['patient_information.patient_uid' => Yii::$app->request->get('id')])
->union($modelBill)
->distinct()
->all();
?>

<div class="receipt-index">

<?php
foreach($modelReceipt as $model){
    $dataProvider = $searchModel->transactionRecords($model["rn"]);
?>
    <div id="card1" class="container-fluid">
        <div class="card" id="transaction-div">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo $model["rn"]; ?></h3>
                <div class="d-flex justify-content-end">
                    <?php echo "<div>".(new Patient_information())  -> getBalanceRN($model["rn"])."&nbsp&nbsp&nbsp&nbsp&nbsp".
                        (new Patient_information())  -> getUnclaimedBalanceRN($model["rn"])."&nbsp&nbsp&nbsp&nbsp&nbsp".
                        (new Bill()) -> calculateBillableRN($model["rn"])."&nbsp&nbsp&nbsp</div>";
                    ?>
                    <div class="card-tools">
                        <!-- Collapse Button -->
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body" id="transaction-div">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'showOnEmpty' => false,
                    'emptyText' => Yii::t('app','Payment record is not found'),
                //  'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        // [
                        //     'attribute' => 'rn',
                        //     'format' => 'raw',
                        //     'headerOptions'=>['style'=>'max-width: 100px;'],
                        //     'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                        //     'value'=>function ($data) {
                        //         // $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);

                        //         // if(!empty($model_receipt)){
                        //         //     $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_receipt->receipt_uid]);

                        //         //     if(!empty($model_cancellation)){
                        //         //         return $data['rn'];
                        //         //     }
                        //         //     else if(!empty($model_receipt->receipt_type)){
                        //         //         return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                        //         //     }
                        //         // }
                        //         // // Bill
                        //         // else{
                        //         //     return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                        //         // }

                        //         return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                        //     },
                        //     'label' => Yii::t('app','Registration Number (R/N)')
                        // ],
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
                                // $model_receipt = Receipt::findOne(['receipt_serial_number' => $data['receipt_serial_number']]);
                                $model_receipt = Receipt::findOne(['receipt_uid' => $data['receipt_uid']]);
                                // $model_bill = Bill::findOne(['final_ward_datetime' => $data['receipt_content_datetime_paid']]);
                                $model_bill = Bill::findOne(['bill_generation_datetime' => $data['receipt_content_datetime_paid']]);

                                if(!empty($model_receipt)){
                                    $model_cancellation = Cancellation::findAll(['cancellation_uid' => $model_receipt->receipt_uid]);

                                    foreach($model_cancellation as $model_cancellation){
                                        $model_new_receipt = Receipt::findOne(['receipt_uid' => $model_cancellation->replacement_uid]);
                                    }

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
                                else if(!empty($model_bill)){
                                    if($model_bill->deleted == 1){
                                        $type = 'Bill Cancelled';
                                        $title = '';
                                        $class = 'badge-danger';
                                    }
                                    else{
                                        $type = 'Bill';
                                        $title = $model_bill->description;;
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
        </div>
    </div>
<?php
}
?>

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