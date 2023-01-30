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
use app\models\Treatment_details;
use app\components\Utils;

//Initial Args
//	'treatment_model' => $treatment_model,

	$page_mode = Utils::getBillPageMode();
	$editeable = Utils::getEditeable();
?>

<?php $form = kartik\form\ActiveForm::begin([
	'id' => 'treatment_form_'.$treatment_model->treatment_details_uid,
	'type' => 'vertical',
	'action' => \yii\helpers\Url::to(['treatment-update']),
	'fieldConfig' => [
		'template' => "{label}\n{input}\n{error}",
		'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
	],
	'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
]); ?>
<div class="row" id="<?=$treatment_model->treatment_details_uid.'treatment_row'?>">
	<div class="col-sm-1">
		<div class="row" <?=(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable)?'hidden':''?>><span id="<?=$treatment_model->treatment_details_uid.'dirty_status'?>" class="badge badge-primary">Saved</span></div>
		<div class="row" <?=(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable)?'hidden':''?>><a style="cursor:pointer;" id="<?=$treatment_model->treatment_details_uid.'treatment_delete'?>" class="badge badge-danger" tabindex="-1" onclick="deleteTreatment('<?=$treatment_model->treatment_details_uid?>')">Delete</a></div>
		<?= $form->field($treatment_model, 'treatment_details_uid')->hiddenInput(['id'=>$treatment_model->treatment_details_uid.'treatment_details_uid', 'value'=>$treatment_model->treatment_details_uid])->label(false)?>
	</div>
	<div class="col-sm-6">
			<?=	$form->field($treatment_model, 'treatment_code')->widget(Select2::classname(),[
				'attribute'=> 'treatment_code',
				'value'=>$treatment_model->treatment_code,
				'data' => empty($treatment_model->treatment_code)?
					[]:
					[$treatment_model->treatment_code=> $treatment_model->treatment_name],//ArrayHelper::map($data, 'status_code', 'status_description'),
				'options' => [
					'id'=> $treatment_model->treatment_details_uid.'treatment_treatment_code',
					//'class'=> 'form-control',
					'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
					'value' => empty($treatment_model->treatment_code)?null: $treatment_model->treatment_code,
					'placeholder' => 'Please select treatment code',
					'onchange' => 'dirtyTreatmentRow(\''.$treatment_model->treatment_details_uid.'\')',
				],
				'pluginOptions' => [
					'minimumInputLength' => 1,
					'language' => [
						'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
					],
					'ajax' => [
						'url' => \yii\helpers\Url::to(['load-treatment-code']),
						'dataType' => 'json',
						'data' => new JsExpression('function(params) { return {q:params.term}; }'),
					],
				'escapeMarkup' => new JsExpression('function (model_row) { return model_row; }'),
				'templateResult' => new JsExpression(empty($templateResult)? 'function(model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateResult),
				'templateSelection' => new JsExpression(empty($templateSelection)?'function (model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateSelection),
				],
			])->label(false);?>  
		
	</div>
	<div class="col-sm-2"><?= $form->field($treatment_model, 'item_per_unit_cost_rm')->textInput(['autocomplete' =>'off', 'maxlength' => true,  
									'id'=> $treatment_model->treatment_details_uid.'item_per_unit_cost_rm',
									'disabled' => true])->label(false)?></div>
	<div class="col-sm-1"><?= $form->field($treatment_model, 'item_count')->textInput(['autocomplete' =>'off', 'maxlength' => true,   
									//'placeholder'=>'yyyy-mm-dd',
									'onchange' => 'dirtyTreatmentRow(\''.$treatment_model->treatment_details_uid.'\')',
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
	<div class="col-sm-2" >
		<?= $form->field($treatment_model, 'item_total_unit_cost_rm')->textInput(['autocomplete' =>'off', 'maxlength' => true,  
									'id'=> $treatment_model->treatment_details_uid.'item_total_unit_cost_rm',
									'disabled' => true])->label(false)?>
		<div hidden><?= Html::submitButton('Submit'); ?></div>
	</div>

</div>
<?php kartik\form\ActiveForm::end(); ?>
<?php 
$script = <<< TreatmentJS

$(document).on("submit","#treatment_form_{$treatment_model->treatment_details_uid}", function(e) {
	updateTreatment(e, $(this),'{$treatment_model->treatment_details_uid}');
});

TreatmentJS;
$this->registerJS($script);
?>