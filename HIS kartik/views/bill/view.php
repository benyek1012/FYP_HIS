<?php

use yii\helpers\Html;
use app\models\Patient_information;
use app\models\Patient_admission;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */

$temp2 = Patient_admission::findOne(['rn'=> $model->rn]);
$temp3 = Patient_information::findOne(['patient_uid'=> $temp2->patient_uid]);

$this->title = $model->rn. " Bill";
if($temp3->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp3->name, 'url' => ['site/index', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/index', 'id' => $temp3->patient_uid]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bill-view">

    <p>
        <?= Html::a('Update', ['update', 'bill_uid' => $model->bill_uid, 'rn' => $model->rn], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'bill_uid' => $model->bill_uid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          //  'bill_uid',
            //'rn',
            'status_code',
            'status_description',
            'class',
            'daily_ward_cost',
            'department_code',
            'department_name',
            'is_free',
            'collection_center_code',
            'nurse_responsilbe',
            'bill_generation_datetime',
            'generation_responsible_uid',
            'bill_generation_billable_sum_rm',
            'bill_generation_final_fee_rm',
            'description',
            'bill_print_responsible_uid',
            'bill_print_datetime',
            'bill_print_id',
        ],
    ]) ?>

</div>
