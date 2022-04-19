<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bill-form">


<?php $form = kartik\form\ActiveForm::begin([
        'id' => 'ward-dynamic-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); 
    $billuid = Base64UID::generate(32);
    $generationresponsibleuid = Base64UID::generate(32);
    $billprintresponsibleuid = Base64UID::generate(32);
    $rn = date('Y')."/".substr(number_format(time() * rand(),0,'',''),0,6);

    ?>

    <?php /*  $this->render('/ward/create', ['model' => $model, 'modelWard' => $modelWard]) */?> 

    <?= $form->field($model, 'bill_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $billuid])->label(false) ?>

    <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false) ?>

    <?= $form->field($model, 'status_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_name')->textInput(['maxlength' => true]) ?>
 
    <div class="row">
    <div class="panel panel-default">
        <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Ward</h4></div>
        <div class="panel-body">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                // 'limit' => 4, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelWard[0],
                'formId' => 'ward-dynamic-form',
                'formFields' => [
                    'ward_uid',
                    'bill_uid',
                    'ward_code',
                    'ward_name',
                    'ward_start_datetime',
                    'ward_end_datetime',
                    'ward_numbers_of_days',
                ],
            ]); ?>

            <div class="container-items"><!-- widgetContainer -->
            <?php foreach ($modelWard as $i => $modelWard): ?>
                <div class="item panel panel-default"><!-- widgetBody -->
                    <div class="panel-heading">
                        <!-- <h3 class="panel-title pull-left">Ward</h3> -->
                        <div class="pull-right">
                            <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus">+</i></button>
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus">-</i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <?php
                            // necessary for update action.
                            // if (! $modelWard->modelWard) {
                            //     echo Html::activeHiddenInput($modelWard, "[{$i}]ward_id");
                            // }
                        ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($modelWard, "[{$i}]ward_uid")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelWard, "[{$i}]bill_uid")->textInput(['maxlength' => true, 'value' => $billuid]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($modelWard, "[{$i}]ward_code")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelWard, "[{$i}]ward_name")->textInput(['maxlength' => true]) ?>
                            </div>
                        </div><!-- .row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($modelWard, "[{$i}]ward_start_datetime")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelWard, "[{$i}]ward_end_datetime")->textInput(['maxlength' => true]) ?>
                            </div>
                        </div><!-- .row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($modelWard, "[{$i}]ward_number_of_days")->textInput(['maxlength' => true]) ?>
                            </div>
                        </div><!-- .row -->
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
        </div>
    </div>
     

    <?= $form->field($model, 'is_free')->textInput() ?>

    <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nurse_responsilbe')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'bill_generation_datetime')->widget(DateTimePicker::classname(), 
        ['pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii' ]
    ])?>

    <?= $form->field($model, 'generation_responsible_uid')->textInput(['readonly' => true, 'maxlength' => true,'value' => $generationresponsibleuid]) ?>

    <?= $form->field($model, 'bill_generation_billable_sum_rm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bill_print_responsible_uid')->textInput(['readonly' => true, 'maxlength' => true,'value' => $billprintresponsibleuid]) ?>
    
    <?= $form->field($model, 'bill_print_datetime')->widget(DateTimePicker::classname(), 
        ['pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd hh:ii' ]
    ])?>

    <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
