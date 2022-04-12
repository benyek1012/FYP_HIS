<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_admission */

$this->title = $model->rn;
$this->params['breadcrumbs'][] = ['label' => 'Patient Admissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="patient-admission-view">
    <p>
        <?= Html::a('Update', ['update', 'rn' => $model->rn], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'rn',
            'entry_datetime',
            'patient_uid',
            'initial_ward_code',
            'initial_ward_class',
            'reference',
            'medigal_legal_code',
            'reminder_given',
            'guarantor_name',
            'guarantor_nric',
            'guarantor_phone_number',
            'guarantor_email:email',
        ],
    ]) ?>

</div>
