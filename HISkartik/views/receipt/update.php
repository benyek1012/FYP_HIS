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
if($temp3->name != "")
    $this->params['breadcrumbs'][] = ['label' => $temp3->name, 'url' => ['site/index', 'id' => $temp2->patient_uid]];
else 
    $this->params['breadcrumbs'][] = ['label' => "User", 'url' => ['site/index', 'id' => $temp3->patient_uid]];
    
if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn'])
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Other Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
else
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];

$this->params['breadcrumbs'][] = 'Receipt Update';
?>
<div class="receipt-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
