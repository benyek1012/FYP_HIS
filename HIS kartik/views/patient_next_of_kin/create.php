<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_next_of_kin */

$this->title = 'Create Patient Next of Kin';
$this->params['breadcrumbs'][] = ['label' => 'Patient Next of Kins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-next-of-kin-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
