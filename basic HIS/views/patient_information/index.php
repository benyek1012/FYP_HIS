<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_informationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Patient Informations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-information-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Patient Information', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'patient_uid',
            'first_reg_date',
            'nric',
            'nationality',
            'name',
            //'sex',
            //'phone_number',
            //'email:email',
            //'address1',
            //'address2',
            //'address3',
            //'job',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action,  $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'patient_uid' => $model->patient_uid]);
                 }
            ],
        ],
    ]); ?>


</div>

