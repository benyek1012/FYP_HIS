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
use app\models\Inpatient_treatment;
use app\components\Utils;
//Initial Args
//	'bill_model' => $bill_model,
//	'admission_model' => $admission_model,
//	'wards_query' => Ward::find()->where(['bill_uid'=>$bill_model->bill_uid])->orderBy(['ward_end_datetime'=>SORT_ASC, 'ward_code'=>SORT_ASC]),

	$page_mode = Utils::getBillPageMode();
	$editeable = Utils::getEditeable();
?>


<div class="card" id="ward_details">
	<div class="card-header text-white bg-primary">
		<h3 class="card-title">
			<div><?= Yii::t('app','Ward Details')?></div>
			<!--<div id="ward_section_admission_entrydatetime">Entry Datetime: &nbsp<?=$admission_model->entry_datetime?>&nbsp&nbsp&nbsp&nbsp&nbsp</div>-->
		</h3>
		<div class="d-flex justify-content-end">
			<div > Total Days: <span id="ward_total_days"><?=$wards_query->sum('ward_number_of_days')?> </span>day(s)&nbsp&nbsp&nbsp&nbsp</div>
			<div > Total Cost: RM<span id="ward_total_cost"><?=number_format((float)($wards_query->sum('ward_number_of_days')*$bill_model->daily_ward_cost), 2, '.', '')?></span></div>
			<div class="card-tools">
				<!-- Collapse Button -->
				<button type="button" class="btn btn-tool" data-card-widget="collapse"><i
						class="fas fa-minus"></i></button>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div>
			<div class="row">
				<label class="col-sm-1">Saved</label>
				<label class="col-sm-4">Ward</label>
				<label class="col-sm-3">Start Datetime</label>
				<label class="col-sm-2">End Date</label>
				<label class="col-sm-1">End Time</label>
				<label class="col-sm-1">Number of Days</label>
			</div>
			<?php
				$ward_models = $wards_query->all();
				foreach($ward_models as $ward_model)
				{
					echo $this->render('_bill_ward_section_row_clem', [
					'ward_model' => $ward_model,
					]);
				}
		
			?>
			
			<div hidden=true id="ward_end_marker">Used to mark where new rows would be inserted into</div> 

			<br/>
			<?php
				$new_ward_model = new Ward();
				$new_ward_model->bill_uid = $bill_model->bill_uid;
			?>

			<?php $form = kartik\form\ActiveForm::begin([
				'id' => 'new_ward_form',
				'type' => 'vertical',
				'action' => \yii\helpers\Url::to(['ward-create']),
				'fieldConfig' => [
					'template' => "{label}\n{input}\n{error}",
					'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
				],
				'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
			]); ?>
			<div class="row" style="background-color:aliceblue" <?=(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable)?'hidden':''?>>
				<?= $form->field($new_ward_model, 'bill_uid')->hiddenInput(['id'=>'new_ward_bill_uid'])->label(false)?> 
				<div class="col-sm-1"></div>
				<div class="col-sm-4">
						<?=	$form->field($new_ward_model, 'ward_code')->widget(Select2::classname(),[
						'data' =>[],
						'options' => [
							'id'=> 'new_ward_ward_code',
							'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
							'value' => null,
							'placeholder' => 'Please select ward code'],
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
				<div class="col-sm-3"><?= $form->field($new_ward_model, 'ward_start_datetime')->textInput(['autocomplete' =>'off', 'maxlength' => true,  
												'placeholder'=>'yyyy-mm-dd hh:mm',
												'id'=>'new_ward_ward_start_datetime',
												'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
				<div class="col-sm-2"><?= $form->field($new_ward_model, 'ward_end_date')->textInput(['autocomplete' =>'off', 'maxlength' => true,   
												'placeholder'=>'yyyy-mm-dd',
												'id'=>'new_ward_ward_end_date',
												'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
				<div class="col-sm-1"><?= $form->field($new_ward_model, 'ward_end_time')->textInput(['autocomplete' =>'off', 'maxlength' => true,   
												'placeholder'=>'hh:mm',
												'id'=>'new_ward_ward_end_time',
												'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false)?></div>
				<div class="col-sm-1">
					<?php 
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
$script = <<< WardJS

$(document).on("submit","#new_ward_form", function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var actionUrl = form.attr('action');
    
    $.ajax({
        type: "POST",
        url: actionUrl,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
			$("#ward_end_marker").before(data);
			updateInpatientTreatmentDataAndTotalTreatmentCost();//_bill_treatment_section_clem view
			resetWardNewRow();
			if (typeof getGenerateValues === "function") //_bill_generate_section_clem view
				getGenerateValues();
        },
		error: function (data) {
			console.log( JSON.stringify(data));
		}
    });
    
});

WardJS;
$this->registerJS($script);
?>





<script>

function dirtyWardRow(ward_uid){
	var badge = document.getElementById(ward_uid+'dirty_status');
	var row = document.getElementById(ward_uid+'ward_row');
	badge.classList.remove("badge-primary");
	badge.classList.remove("badge-success");
	badge.classList.add("badge-danger");
	badge.textContent = 'Not Saved';
	row.classList.add("bg-warning");
}

function clearDirtyWardRow(ward_uid){
	var badge = document.getElementById(ward_uid+'dirty_status');
	var row = document.getElementById(ward_uid+'ward_row');
	badge.classList.remove("badge-primary");
	badge.classList.add("badge-success");
	badge.classList.remove("badge-danger");
	badge.textContent = 'Saved';
	row.classList.remove("bg-warning");
}

function resetWardNewRow(){

	
	//var new_ward_ward_code = document.getElementById('new_ward_ward_code');
	//new_ward_ward_code.value = null;
	//new_ward_ward_code.classList.remove("is-invalid");
	//new_ward_ward_code.parentElement.classList.remove("has-error");
	
	var new_ward_ward_start_datetime = document.getElementById('new_ward_ward_start_datetime');
	new_ward_ward_start_datetime.value = null;
	//new_ward_ward_start_datetime.classList.remove("is-invalid");
	//new_ward_ward_start_datetime.parentElement.classList.remove("has-error");
		
	var new_ward_ward_end_date = document.getElementById('new_ward_ward_end_date');
	new_ward_ward_end_date.value = null;
	//new_ward_ward_end_date.classList.remove("is-invalid");
	//new_ward_ward_end_date.parentElement.classList.remove("has-error");
		
	var new_ward_ward_end_time = document.getElementById('new_ward_ward_end_time');
	new_ward_ward_end_time.value = null;
	//new_ward_ward_end_time.classList.remove("is-invalid");
	//new_ward_ward_end_time.parentElement.classList.remove("has-error");
	
	//$('new_ward_ward_code').select2('open');
	
	
}

function updateWard(e, form, ward_uid) {
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
			//alert(actionUrl);
			if(data.success){
				document.getElementById('ward_total_days').textContent = data.ward_total_days;
				document.getElementById('ward_total_cost').textContent = data.ward_total_cost;
				document.getElementById(ward_uid+'ward_ward_number_of_days').value = data.ward_number_of_days;
				clearDirtyWardRow(ward_uid);
				updateInpatientTreatmentDataAndTotalTreatmentCost();//_bill_treatment_section_clem view
				if (typeof getGenerateValues === "function") //_bill_generate_section_clem view
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

function deleteWard(ward_uid) {
	if(confirm('Delete Ward?')){
		$.ajax({
			type: "POST",
			url: "<?=\yii\helpers\Url::to(['ward-delete'])?>",
			data: {"ward_uid": ward_uid },//form.serialize(), // serializes the form's elements.
			dataType:'json',
			success: function(data)
			{
				//alert(actionUrl);
				if(data.success){
					document.getElementById('ward_total_days').textContent = data.ward_total_days;
					document.getElementById('ward_total_cost').textContent = data.ward_total_cost;
					//document.getElementById(ward_uid+'ward_ward_number_of_days').textContent = data.ward_number_of_days;
					//clearDirtyWardRow(ward_uid);
					document.getElementById('ward_form_'+ward_uid).remove();
					updateInpatientTreatmentDataAndTotalTreatmentCost();//_bill_treatment_section_clem view
					if (typeof getGenerateValues === "function") //_bill_generate_section_clem view
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