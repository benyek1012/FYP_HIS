<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap4\ActiveForm;
use yii\widgets\Pjax;
use kartik\editable\Editable;

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

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'first_reg_date',
                'editableOptions' => [
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DATE,
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd' 
                        ]
                    ]
                ],
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nric',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'nationality',
                'editableOptions' => [
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => [
                        'malaysia'=>'Malaysia',
                        'indonesia'=>'Indonesia',
                        'singapore' => 'Singapore',
                        'thailand' => 'Thailand',
                        'china' => 'China'
                    ],
                ],
            ],

            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'name',
            ],

            [
                'class' => '\kartik\grid\EditableColumn',
                'attribute' => 'sex',
                'editableOptions' => [
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => [
                        'male' => 'Male', 
                        'female' => 'Female'
                    ],
                ],
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

<?php $form = ActiveForm::begin([]); ?>
    <div class="form-group">
        <?= Html::submitButton('Add', ['class' => 'btn btn-primary', 'name' => 'buttonAdd']) ?>
        <?= Html::submitButton('Cancel', ['class' => 'btn btn-primary', 'name' => 'buttonCancel']) ?>
    </div>
<?php ActiveForm::end(); ?>

<?php
    if(isset($_POST['buttonAdd'])){
        echo $this->render('_form', ['model' => $model]);
    }

    if(isset($_POST['buttonCancel'])){
        return Url::toRoute(['patient_information/index']);
    }
?>

</div>