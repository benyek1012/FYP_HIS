<?php

use yii\bootstrap4\Dropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_next_of_kinSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Patient Next Of Kins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-next-of-kin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Patient Next Of Kin', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php     
        $relationship = array(
        'father'=>'Father',
        'monther'=>'Monther',
        'couple' => 'Couple',
        'brother' => 'Brother',
        'sister' => 'Sister',
        'other' => 'Other'
    );// echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'nok_uid',
            'patient_uid',
            'nok_name',
            [
                'attribute' => 'nok_relationship',
                'value' => 'nok_relationship',
                'filter'=> Html::activeDropDownList($searchModel, 'nok_relationship', $relationship,['class'=>'form-control','prompt' => 'Select relationship','maxlength' => true]),
            ],
            'nok_phone_number',
            //'nok_email:email',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'nok_uid' => $model->nok_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
