<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2; 
use yii\web\JsExpression;
use app\models\Bill;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Lookup_status;
use app\controllers\BillController;
use yii\helpers\ArrayHelper;
use app\models\Variable;
use app\models\Ward;
use app\components\Utils;

//Initial Args
//	'ward_model' => $ward_model,

	$page_mode = Utils::getBillPageMode();
	$editeable = Utils::getEditeable();
	$ward_model->dirtyUpdateWardEndDateAndTime();
	$ward_model->ward_start_datetime = substr($ward_model->ward_start_datetime, 0, 16)
?>

<?php $form = kartik\form\ActiveForm::begin([
	'id' => 'ward_form_'.$ward_model->ward_uid,
	'type' => 'vertical',
	'action' => \yii\helpers\Url::to(['ward-update']),
	'fieldConfig' => [
		'template' => "{label}\n{input}\n{error}",
		'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
	],
	'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
]); ?>

<div class="row" id="<?=$ward_model->ward_uid.'ward_row'?>">
	<div class="col-sm-1">
		<div class="row" <?=(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable)?'hidden':''?>><span id="<?=$ward_model->ward_uid.'dirty_status'?>" class="badge badge-primary">Saved</span></div>
		<div class="row" <?=(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable)?'hidden':''?>><a style="cursor:pointer;" id="<?=$ward_model->ward_uid.'ward_delete'?>" class="badge badge-danger" tabindex="-1" onclick="deleteWard('<?=$ward_model->ward_uid?>')">Delete</a></div>
		<?= $form->field($ward_model, 'ward_uid')->hiddenInput(['id'=>$ward_model->ward_uid.'ward_uid', 'value'=>$ward_model->ward_uid])->label(false)?>
	</div>
	<div class="col-sm-4">
			<?=	$form->field($ward_model, 'ward_code')->widget(Select2::classname(),[
				'attribute'=> 'ward_code',
				'value'=>$ward_model->ward_code,
				'data' => empty($ward_model->ward_code)?
					[]:
					[$ward_model->ward_code=> $ward_model->ward_name],//ArrayHelper::map($data, 'status_code', 'status_description'),
				'options' => [
					'id'=> $ward_model->ward_uid.'ward_ward_code',
					//'class'=> 'form-control',
					'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
					'value' => empty($ward_model->ward_code)?null: $ward_model->ward_code,
					'placeholder' => 'Please select ward code',
					'onchange' => 'dirtyWardRow(\''.$ward_model->ward_uid.'\')',
				],
				'pluginOptions' => [
					'minimumInputLength' => 1,
					'language' => [
						'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
					],
					'ajax' => [
						'url' => \yii\helpers\Url::to(['load-ward-code']),
						'dataType' => 'json',
						'data' => new JsExpression('function(params) { return {q:params.term}; }'),
					],
				'escapeMarkup' => new JsExpression('function (model_row) { return model_row; }'),
				'templateResult' => new JsExpression(empty($templateResult)? 'function(model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateResult),
				'templateSelection' => new JsExpression(empty($templateSelection)?'function (model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateSelection),
				],
			])->label(false);?>  
		
	</div>
	<div class="col-sm-3"><?= $form->field($ward_model, 'ward_start_datetime')->textInput(['autocomplete' =>'off', 'maxlength' => true,  
									'placeholder'=>'yyyy-mm-dd hh:mm',
									'onchange' => 'dirtyWardRow(\''.$ward_model->ward_uid.'\')',
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
	<div class="col-sm-2"><?= $form->field($ward_model, 'ward_end_date')->textInput(['autocomplete' =>'off', 'maxlength' => true,   
									'placeholder'=>'yyyy-mm-dd',
									'onchange' => 'dirtyWardRow(\''.$ward_model->ward_uid.'\')',
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
	<div class="col-sm-1"><?= $form->field($ward_model, 'ward_end_time')->textInput(['autocomplete' =>'off', 'maxlength' => true,   
									'placeholder'=>'hh:mm',
									'onchange' => 'dirtyWardRow(\''.$ward_model->ward_uid.'\')',
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
	<div class="col-sm-1" >
		
		<?= $form->field($ward_model, 'ward_number_of_days')->textInput(['autocomplete' =>'off', 'maxlength' => true,  
									'id'=> $ward_model->ward_uid.'ward_ward_number_of_days',
									'disabled' => true])->label(false)?>
	
		<div hidden><?= Html::submitButton('Submit'); ?></div>
	</div>

</div>
<?php kartik\form\ActiveForm::end(); ?>
<?php 
$script = <<< WardJS

$(document).on("submit","#ward_form_{$ward_model->ward_uid}", function(e) {
	updateWard(e, $(this),'{$ward_model->ward_uid}');
});

WardJS;
$this->registerJS($script);
?>