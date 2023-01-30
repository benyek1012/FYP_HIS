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
use app\models\Treatment_details;
use app\components\Utils;
//Initial Args
//	'bill_model' => $bill_model,
//	'admission_model' => $admission_model,
//	'treatments_query' => Treatment_details::find()->where(['bill_uid'=>$bill_model->bill_uid])->orderBy(Treatment_details::getNaturalSortArgs()),

	$page_mode = Utils::getBillPageMode();
	$editeable = Utils::getEditeable();
?>


<div class="card" id="treatment_details">
	<div class="card-header text-white bg-primary">
		<h3 class="card-title">
			<?= Yii::t('app','Treatment Details')?>
		</h3>
		<div class="d-flex justify-content-end">
			<div > Total Cost (RM):<span id="treatment_total_cost"><?=number_format((float)(-9999), 2, '.', '')?></span></div>
			<div class="card-tools">
				<!-- Collapse Button -->
				<button type="button" class="btn btn-tool" data-card-widget="collapse"><i
						class="fas fa-minus"></i></button>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div>
			<div class="row"><label>Inpatient Treatment Cost (RM):&nbsp </label> <span id="inpatient_treatment_cost"><?=number_format((float)(-9999), 2, '.', '')?></span></div>
			<div class="row">
				<label class="col-sm-1">Saved</label>
				<label class="col-sm-6">Treatment Code</label>
				<label class="col-sm-2">Cost Per Unit</label>
				<label class="col-sm-1"># of Units</label>
				<label class="col-sm-2">Total Cost</label>
			</div>
			<?php
				$treatment_models = $treatments_query->all();
				foreach($treatment_models as $treatment_model)
				{
					echo $this->render('_bill_treatment_section_row_clem', [
					'treatment_model' => $treatment_model,
					]);
				}
		
			?>
			
			<div hidden=true id="treatment_end_marker">Used to mark where new rows would be inserted into</div> 

			<br/>
			<?php
				$new_treatment_model = new Treatment_details();
				$new_treatment_model->bill_uid = $bill_model->bill_uid;
			?>

			<?php $form = kartik\form\ActiveForm::begin([
				'id' => 'new_treatment_form',
				'type' => 'vertical',
				'action' => \yii\helpers\Url::to(['treatment-create']),
				'fieldConfig' => [
					'template' => "{label}\n{input}\n{error}",
					'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
				],
				'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
			]); ?>

			<div class="row" style="background-color:aliceblue" <?=(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable)?'hidden':''?>>
				<?= $form->field($new_treatment_model, 'bill_uid')->hiddenInput(['id'=>'new_treatment_bill_uid'])->label(false)?> 
				<div class="col-sm-1"></div>
				<div class="col-sm-6">
						<?=	$form->field($new_treatment_model, 'treatment_code')->widget(Select2::classname(),[
						'data' =>[],
						'options' => [
							'id'=> 'new_treatment_treatment_code',
							'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
							'value' => null,
							'placeholder' => 'Please select treatment code'],
							'pluginOptions' => [
								'minimumInputLength' => 2,
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
				<div class="col-sm-2"></div>
				<div class="col-sm-1"><?= $form->field($new_treatment_model, 'item_count')->textInput(['autocomplete' =>'off', 'maxlength' => true,   
												//'placeholder'=>'yyyy-mm-dd',
												'id'=>'new_treatment_item_count',
												'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
				<div class="col-sm-2"><?php
					if(!(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable))
						echo Html::submitButton('Submit', ['class'=>'btn btn-success']); 
					?>
				</div>
			</div>
			<?php kartik\form\ActiveForm::end(); ?>
		</div>
	</div>
</div>

<?php 

$script = <<< TreatmentJS

$(document).on("submit","#new_treatment_form", function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var actionUrl = form.attr('action');
    
    $.ajax({
        type: "POST",
        url: actionUrl,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
			$("#treatment_end_marker").before(data);
			resetTreatmentNewRow();
			updateInpatientTreatmentDataAndTotalTreatmentCost();
			if (typeof getGenerateValues === "function") //_bill_generate_section_clem view
				getGenerateValues();
        },
		error: function (data) {
			console.log( JSON.stringify(data));
		}
    });
    
});



TreatmentJS;
$this->registerJS($script);
?>





<script>

updateInpatientTreatmentDataAndTotalTreatmentCost();

function updateInpatientTreatmentDataAndTotalTreatmentCost()
{
	var xmlhttp = new XMLHttpRequest(); //ajax can't be used for functions that will be executed on page load
		xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var data = JSON.parse(this.responseText);
			//debugger;
			if(data.success){
				//debugger;
				document.getElementById('treatment_total_cost').textContent = data.treatment_total_cost;
				document.getElementById('inpatient_treatment_cost').textContent = data.inpatient_treatment_cost;
			
			}
			else{
				alert ('failed to run updateInpatientTreatmentDataAndTotalTreatmentCost')
				alert (data.message)
			};
		}
	};
	xmlhttp.open("GET", "<?=\yii\helpers\Url::to(['update-inpatient-treatment-data-and-total-treatment-cost', 'bill_uid'=>$bill_model->bill_uid])?>", true);
	xmlhttp.send();
}

function resetTreatmentNewRow(){

	
	//var new_ward_ward_code = document.getElementById('new_ward_ward_code');
	//new_ward_ward_code.value = null;
	//new_ward_ward_code.classList.remove("is-invalid");
	//new_ward_ward_code.parentElement.classList.remove("has-error");
	
	var new_treatment_item_count = document.getElementById('new_treatment_item_count');
	new_treatment_item_count.value = null;
	//new_ward_ward_start_datetime.classList.remove("is-invalid");
	//new_ward_ward_start_datetime.parentElement.classList.remove("has-error");
	
	//$('new_ward_ward_code').select2('open');
	
	
}



function dirtyTreatmentRow(treatment_uid){
	var badge = document.getElementById(treatment_uid+'dirty_status');
	var row = document.getElementById(treatment_uid+'treatment_row');
	badge.classList.remove("badge-primary");
	badge.classList.remove("badge-success");
	badge.classList.add("badge-danger");
	badge.textContent = 'Not Saved';
	row.classList.add("bg-warning");
}

function clearDirtyTreatmentRow(treatment_uid){
	var badge = document.getElementById(treatment_uid+'dirty_status');
	var row = document.getElementById(treatment_uid+'treatment_row');
	badge.classList.remove("badge-primary");
	badge.classList.add("badge-success");
	badge.classList.remove("badge-danger");
	badge.textContent = 'Saved';
	row.classList.remove("bg-warning");
}

function updateTreatment(e, form, treatment_uid) {
	e.preventDefault(); // avoid to execute the actual submit of the form.

    var actionUrl = form.attr('action');
    //debugger;
    $.ajax({
        type: "POST",
        url: actionUrl,
        data: form.serialize(), // serializes the form's elements.
		dataType:'json',
        success: function(data)
        {
			//debugger;
			//alert(actionUrl);
			if(data.success){
				document.getElementById(treatment_uid+'item_per_unit_cost_rm').value = data.item_per_unit_cost_rm;
				document.getElementById(treatment_uid+'item_total_unit_cost_rm').value = data.item_total_unit_cost_rm;
				clearDirtyTreatmentRow(treatment_uid);
				updateInpatientTreatmentDataAndTotalTreatmentCost(); //_bill_generate_section_clem view
				if (typeof getGenerateValues === "function") 
					getGenerateValues();
			}
			else alert (data.message);
        },
		error: function (data) {
			//console.log( JSON.stringify(data));
			alert('Failed to save: Coding error');
			//debugger;
			//alert(data);
		}
    });
}


function deleteTreatment(treatment_uid) {
	//console.log(treatment_uid);
	if(confirm('Delete Treatment?')){
		$.ajax({
			type: "POST",
			url: "<?=\yii\helpers\Url::to(['treatment-delete'])?>",
			data: {"treatment_details_uid": treatment_uid },//form.serialize(), // serializes the form's elements.
			dataType:'json',
			success: function(data)
			{
				//alert(actionUrl);
				if(data.success){
					//document.getElementById('ward_total_days').textContent = data.ward_total_days;
					//document.getElementById('ward_total_cost').textContent = data.ward_total_cost;
					//document.getElementById(ward_uid+'ward_ward_number_of_days').textContent = data.ward_number_of_days;
					//clearDirtyWardRow(ward_uid);
					document.getElementById('treatment_form_'+treatment_uid).remove();
					updateInpatientTreatmentDataAndTotalTreatmentCost(); //_bill_generate_section_clem view
					if (typeof getGenerateValues === "function") 
						getGenerateValues();
				}
				else alert (data.message);
			},
			error: function (data) {
				//console.log( JSON.stringify(data));
				alert('Failed to delete: Coding error');
				//debugger;
				//alert(data);
			}
		});
	}
}
</script>