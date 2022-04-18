<?php

use yii\helpers\Html;
use app\models\Patient_information;
use app\models\Patient_admission;
use app\models\Receipt;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */

$temp2 = Patient_admission::findOne(['rn'=> $model->rn]);
$temp3 = Patient_information::findOne(['patient_uid'=> $temp2->patient_uid]);

$this->title = 'Update Payment: ' . $model->rn;
$this->params['breadcrumbs'][] = ['label' => $temp3->name, 'url' => ['site/index', 'id' => $temp3->patient_uid]];
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index', 'rn'=> $model->rn]];
$this->params['breadcrumbs'][] = 'Receipt Update';
?>
<div class="receipt-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
