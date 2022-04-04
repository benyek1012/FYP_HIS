<?php

use app\models\Patient;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
// use yii\grid\GridView;
use kartik\grid\GridView;
// use kartik\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Patients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Patient', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'patient_uid',
                'attribute' => 'patient_uid',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'first_reg_date',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nric',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nationality',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'name',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'sex',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'phone_number',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'email',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address1',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address2',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'address3',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'job',
            ],

            // 'patient_uid',
            // 'first_reg_date',
            // 'nric',
            // 'nationality',
            // 'name',
            // 'sex',
            // 'phone_number',
            // 'email:email',
            // 'address1',
            // 'address2',
            // 'address3',
            // 'job',
            
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Patient $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'patient_uid' => $model->patient_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
