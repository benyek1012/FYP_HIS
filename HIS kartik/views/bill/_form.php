<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\Patient_admission;
use app\models\Treatment_details;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */

$admission_model = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);

$billuid = Base64UID::generate(32);
$generationresponsibleuid = Base64UID::generate(32);
$billprintresponsibleuid = Base64UID::generate(32);

if(!empty( Yii::$app->request->get('rn')))
    $initial_ward_class = $admission_model->initial_ward_class;


$free = array(
    0 =>'No', //false
    1 =>'Yes',    //true
);

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
?>

    <?php /*  $this->render('/ward/create', ['model' => $model, 'modelWard' => $modelWard]) */?>

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Billing Details');?></h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <?= $form->field($model, 'bill_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $billuid])->label(false) ?>

                <?= $form->field($model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => Yii::$app->request->get('rn')])->label(false) ?>

                <div class="col-sm-6">
                    <?= $form->field($model, 'status_code')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'status_description')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?php if(!empty( Yii::$app->request->get('rn'))){ ?>
                    <?= $form->field($model, 'class')->textInput(['maxlength' => true,'value' => $initial_ward_class]) ?>
                    <?php }else{ ?>
                    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>
                    <?php } ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'daily_ward_cost')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'department_code')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'department_name')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'is_free')->dropDownList($free) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'collection_center_code')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'nurse_responsilbe')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Ward Details');?></h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">

            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="glyphicon glyphicon-envelope"></i>Ward</h4>
                    </div>
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

                        <div class="container-items">
                            <!-- widgetContainer -->
                            <?php foreach ($modelWard as $i => $modelWard): ?>
                            <div class="item panel panel-default">
                                <!-- widgetBody -->
                                <div class="panel-heading">
                                    <!-- <h3 class="panel-title pull-left">Ward</h3> -->
                                    <div class="pull-right">
                                        <button type="button" class="add-item btn btn-success btn-xs"><i
                                                class="glyphicon glyphicon-plus">+</i></button>
                                        <button type="button" class="remove-item btn btn-danger btn-xs"><i
                                                class="glyphicon glyphicon-minus">-</i></button>
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
                                            <?= $form->field($modelWard, "[{$i}]ward_uid")->textInput(['maxlength' => true, 'value' => Base64UID::generate(32)]);  ?>
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
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title"><?php echo Yii::t('app','Treatment Details');?></h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php
                $dataProvider2 = new ActiveDataProvider([
                'query'=> Treatment_details::find()->where(['bill_uid'=>$model->bill_uid]),
                'pagination'=>['pageSize'=>3],
                ]);
                echo $this->render('/treatment_details/index', ['dataProvider'=>$dataProvider2]);
            ?>
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<div class="card" id="bill_div" <?php if(empty($generate)){ echo 'style="display:none;"'; }
            else echo 'style="display:block;"';
    ?>>
    <div class="card-header text-white bg-primary">
        <h3 class="card-title"><?php echo Yii::t('app','Bill Generation Details');?></h3>
        <div class="card-tools">
            <!-- Collapse Button -->
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
        <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <?= $form->field($model, 'generation_responsible_uid')->hiddenInput([
                'readonly' => true, 'maxlength' => true,'value' => $generationresponsibleuid])->label(false) ?>

            <div class="col-sm-6">
                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'bill_generation_billable_sum_rm')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'bill_generation_final_fee_rm')->textInput(['maxlength' => true]) ?>
            </div>

        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<div class="card" id="print_div" style="display:none;">
    <div class="card-header text-white bg-primary">
        <h3 class="card-title"><?php echo Yii::t('app','Printing Details');?></h3>
        <div class="card-tools">
            <!-- Collapse Button -->
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
        <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <?= $form->field($model, 'bill_print_responsible_uid')->hiddenInput(['readonly' => true, 'maxlength' => true,'value' => $billprintresponsibleuid])->label(false) ?>
            <div class="col-sm-12">
                <?= $form->field($model, 'bill_print_id')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->


<div class="form-group">

    <?php if(!empty( Yii::$app->request->get('bill_print_responsible_uid') && Yii::$app->request->get('bill_uid'))){ ?>
    <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
    <?php }else if(!empty( Yii::$app->request->get('bill_uid'))){ ?>
    <?= Html::submitButton(Yii::t('app','Generate'), ['class' => 'btn btn-success']) ?>
    <?php }else{ ?>
    <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
    <?php } ?>
</div>


<?php kartik\form\ActiveForm::end(); ?>

</div>


<script>
    
<?php if(!empty( Yii::$app->request->get('bill_uid'))){?>
document.getElementById("bill_div").style.display = "block";
document.getElementById('print_div').style.display = "none";
<?php } if(!empty( Yii::$app->request->get('bill_print_responsible_uid') && Yii::$app->request->get('bill_uid'))){ ?>
document.getElementById("print_div").style.display = "block";
document.getElementById('card_div').style.display = "block";
<?php } ?>

</script>