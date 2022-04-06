<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Patient_next_of_kin;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Patient Informations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="patient-information-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'patient_uid' => $model->patient_uid], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'patient_uid',
            'first_reg_date',
            'nric',
            'nationality',
            'name',
            'sex',
            'phone_number',
            'email:email',
            'address1',
            'address2',
            'address3',
            'job',
        ],
    ]) ?>
    <?php   
      
    $NOK = Patient_next_of_kin::findOne(['patient_uid' => $model->patient_uid]);
    if (!empty($NOK)) 
      echo $this->render('/patient_next_of_kin/view', ['model'=>$NOK]);
    
    ?>

</div>