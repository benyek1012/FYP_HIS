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
//	'rn'=>$rn,

	$page_mode = Utils::getBillPageMode();
	$editeable = Utils::getEditeable();
?>


<div class="card" id="generate">
	<div class="card-header text-white bg-primary">
		<h3 class="card-title">
			<?= Yii::t('app','Bill Generation Details')?>
			&nbsp[ Calculated On:&nbsp <span id="generate_nonForm_generate_calculate_datetime"><?=($page_mode >= Utils::bill_page_mode_generated)?$bill_model->bill_generation_datetime:'N/A'?></span> ]
		</h3>
		<div class="d-flex justify-content-end">
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
				<div class="col-sm-4">
					<label> Billable Total (RM)</label>
					<?=Html::input('text','',($page_mode >= Utils::bill_page_mode_generated)?number_format($bill_model->bill_generation_billable_sum_rm, 2, '.', ''):'N/A', $options=['class'=>'form-control','maxlength'=>10, 'disabled'=>true, 'id'=>'generate_nonForm_billable_total'])?>
				</div>
				<div class="col-sm-4">
					<label> Sum (Payments)(RM)</label>
					<?=Html::input('text','',($page_mode >= Utils::bill_page_mode_generated)?number_format($bill_model->bill_generation_billable_sum_rm - $bill_model->bill_generation_final_fee_rm, 2, '.', ''):'N/A', $options=['class'=>'form-control','maxlength'=>10, 'disabled'=>true, 'id'=>'generate_nonForm_sum_payments'])?>
				</div>
				<div class="col-sm-4">
					<label> Final Fee (RM)</label>
					<?=Html::input('text','',($page_mode >= Utils::bill_page_mode_generated)?number_format($bill_model->bill_generation_final_fee_rm, 2, '.', ''):'N/A', $options=['class'=>'form-control','maxlength'=>10, 'disabled'=>true, 'id'=>'generate_nonForm_final_fee'])?>
				</div>
			</div>
			<?php $form = kartik\form\ActiveForm::begin([
				'id' => 'generate_form',
				'type' => 'vertical',
				'action' => \yii\helpers\Url::to(['bill-generate-by-clem']),
				'fieldConfig' => [
					'template' => "{label}\n{input}\n{error}",
					'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
				],
				'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
			]); ?>
				<div class="row">
					<?= $form->field($bill_model, 'bill_uid')->hiddenInput(['id'=>'generate_bill_uid', 'value'=>$bill_model->bill_uid])->label(false)?>
					<?= $form->field($bill_model, 'rn')->hiddenInput(['id'=>'generate_rn', 'value'=>$bill_model->rn])->label(false)?>
	
					<div class="col-sm-6">
						<?= $form->field($bill_model, 'discharge_date')->textInput(['autocomplete' =>'off', 'maxlength' => true,   
														'placeholder'=>'yyyy-mm-dd hh:ii',
														'id'=>'generate_discharge_date',
														'value'=>empty($bill_model->discharge_date)?$bill_model->getLastWardEndDateTime($bill_model->bill_uid):$bill_model->discharge_date,
														'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])?>
					</div>
				</div>	
				<div class="row">
					<div class="col-sm-2">
					<?php
						if(!(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable))
							echo Html::submitButton('Submit', ['class'=>'btn btn-success','data'=>['confirm'=>'Generate Bill?'],'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]);
					?>
					<?php
						if((($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable))
							echo Html::button('Cancel Bill', ['class'=>'btn btn-danger','onclick'=>'toggleCancelCard()','disabled' => ($page_mode < Utils::bill_page_mode_generated)?true:!$editeable,]);
					?>
					</div>
				</div>
			<?php kartik\form\ActiveForm::end(); ?>
		</div>
	</div>
</div>


<script>

getGenerateValues();

function getGenerateValues()
{
	if(<?=($page_mode < Utils::bill_page_mode_generated)?1:0?>){
		var xmlhttp = new XMLHttpRequest(); //ajax can't be used for functions that will be executed on page load
			xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var data = JSON.parse(this.responseText);
				//debugger;
				if(data.success){
					//debugger;
					document.getElementById('generate_nonForm_billable_total').value = data.generate_nonForm_billable_total;
					document.getElementById('generate_nonForm_sum_payments').value = data.generate_nonForm_sum_payments;
					document.getElementById('generate_nonForm_final_fee').value = data.generate_nonForm_final_fee;
					document.getElementById('generate_nonForm_generate_calculate_datetime').textContent = data.generate_nonForm_generate_calculate_datetime;
				
				}
				else{
					alert ('failed to run getGenerateValues')
					alert (data.message)
				};
			}
		};
		xmlhttp.open("GET", "<?=\yii\helpers\Url::to(['get-generate-values', 'bill_uid'=>($page_mode < Utils::bill_page_mode_generated)?$bill_model->bill_uid:null])?>", true);
		xmlhttp.send();
	}
}

</script>