<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_informationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Search Patient Informations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-information-index">

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    
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
                'template' => '{view} {update}',
                'urlCreator' => function ($action,  $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'patient_uid' => $model->patient_uid]);
                 }
            ],
        ],
    ]); ?>


</div>

