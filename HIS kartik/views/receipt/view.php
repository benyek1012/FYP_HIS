<?php

//use yii\widgets\DetailView;
use kartik\detail\DetailView;
use app\models\Patient_information;
use app\models\Patient_admission;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */


$temp = Patient_admission::findOne(['rn'=> $model->rn]);
$temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

$this->title = $model->rn.Yii::t('app',' Payment');
if($temp2->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/index', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "User", 'url' => ['site/index', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index', 'rn'=> $model->rn]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="receipt-view">

    <?= DetailView::widget([
        'model' => $model,
        'hover'=>true,
        'attributes' => [
           // 'receipt_uid',
            'rn',
            'receipt_type',
            'receipt_content_sum',
            'receipt_content_bill_id',
            'receipt_content_description',
            'receipt_content_date_paid',
            'receipt_content_payer_name',
            'receipt_content_payment_method',
            'card_no',
            'cheque_number',
            'receipt_responsible',
            'receipt_serial_number',
        ],
    ]) ?>

</div>
