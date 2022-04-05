<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use kartik\date\DatePicker;

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

    <?php 
     $countries = array(
        'malaysia'=>'Malaysia',
        'indonesia'=>'Indonesia',
        'singapore' => 'Singapore',
        'thailand' => 'Thailand',
        'china' => 'China'
    );
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'patient_uid',
            [
                'attribute' => 'first_reg_date',
                'value' => 'first_reg_date',
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'first_reg_date',
                    'pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd' ]
                ])
            ],
            'nric',
            [
                'attribute' => 'nationality',
                'value' => 'nationality',
                'filter'=> Html::activeDropDownList($searchModel, 'nationality', $countries,['class'=>'form-control','prompt' => 'Select nationality','maxlength' => true]),
            ],
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

