<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_informationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Patient Informations';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="patient-information-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin();?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'patient_uid',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'first_reg_date',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'nric',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'nationality',
            // ],

            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'name',
            ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'sex',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'phone_number',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'email',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'address1',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'address2',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'address3',
            // ],

            // [
            //     'class' => '\kartik\grid\EditableColumn',
            //     'attribute' => 'job',
            // ],
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action,  $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'patient_uid' => $model->patient_uid]);
                 }
            ],
        ],
    ]); ?>

<?php Pjax::end();?>
<?php echo $this->render('_form', ['model' => $model]); ?>
</div>

