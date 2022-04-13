<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */

$session = Yii::$app->session;
$temp_id = $session['patient_id'];
$temp_name = $session['patient_name'];

$this->title = 'Create Patient Admission';
$this->params['breadcrumbs'][] = ['label' => $temp_name, 'url' => ['site/index', 'id' => $temp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="patient-admission-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
