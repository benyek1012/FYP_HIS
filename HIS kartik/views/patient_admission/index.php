<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_admissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Patient Admissions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-admission-index">

    <p>
        <?= Html::a('Create Patient Admission', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'rn',
            [
                'attribute' => 'entry_datetime',
                'value' => 'entry_datetime',
                'format' => 'raw',
                'filter' => DateTimePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'entry_datetime',
                    'pluginOptions' => ['autoclose' => true,  'format' => 'yyyy-mm-dd hh:ii' ]
                ])
            ],
            'patient_uid',
            'initial_ward_code',
            'initial_ward_class',
            //'reference',
            //'medigal_legal_code',
            //'reminder_given',
            //'guarantor_name',
            //'guarantor_nric',
            //'guarantor_phone_number',
            //'guarantor_email:email',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'rn' => $model->rn]);
                 }
            ],
        ],
    ]); ?>


</div>
