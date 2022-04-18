<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-index">

    <p>
        <?= Html::a('Create Payment', ['create', 'rn' =>  Yii::$app->request->get('rn')], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'rn',
            'receipt_type',
            'receipt_content_sum',
          //  'receipt_content_bill_id',
            //'receipt_content_description',
            'receipt_content_date_paid',
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
                    return Url::toRoute([$action, 'receipt_uid' => $model->receipt_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
