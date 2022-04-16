<?php

use app\models\Patient_admission;
use app\models\Patient_information;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */

$temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
$temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

$this->title = 'Create Payment';
$this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/index', 'id' => $temp2->patient_uid]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
