<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */

$this->title = 'Create Patient Admission';
$this->params['breadcrumbs'][] = ['label' => 'Patient Admissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-admission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
