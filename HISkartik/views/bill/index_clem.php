<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2; 
use yii\web\JsExpression;
use app\models\Bill;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Lookup_status;
use app\models\Lookup_general;
use app\models\Fpp;
use app\controllers\BillController;
use yii\helpers\ArrayHelper;
use app\models\Variable;
use app\models\Ward;
use app\models\Treatment_details;
use app\components\Utils;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Cancellation;

?>


<?php
/*	Expected Arguments
	$rn
	$bill_uid (null if uncreated or expecting the current bill for rn, specific bill uid to view specific bill (including deleted) 
*/
	if(empty($bill_uid))
		$bill_model = Bill::find()->where(['rn'=>$rn])->andWhere(['<>', 'deleted', '1'])->one();
	else 
		$bill_model = Bill::find()->where(['rn'=>$rn, 'bill_uid'=>$bill_uid])->one(); //allow review of deleted bill 
		
	Utils::setBillPageMode(Utils::bill_page_mode_new);
	
	if(empty($bill_model))
	{
		Utils::setBillPageMode(Utils::bill_page_mode_new);
		$bill_model = new Bill();
		$bill_model->rn = $rn;
		$bill_model->bill_uid = Base64UID::generate(32);
		$bill_model->collection_center_code = '018';
		$not_bill_lock = false;
	}
	elseif(!empty($bill_model->bill_print_datetime))
		Utils::setBillPageMode(Utils::bill_page_mode_printed);
	elseif(!empty($bill_model->bill_generation_datetime))
		Utils::setBillPageMode(Utils::bill_page_mode_generated);
	else
		Utils::setBillPageMode(Utils::bill_page_mode_created);


	//echo 'PAGEMODE: '.Utils::getBillPageMode();

	
	$admission_model = Patient_admission::findOne(['rn'=>$rn]);
	$patient_model = Patient_information::findOne(['patient_uid'=>$admission_model->patient_uid]);
	$editeable = Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1));
	$page_mode = Utils::getBillPageMode();
	
	//echo 'editeable = '.$editeable;
	//echo 'page_mode = '.$page_mode;
?>
<?php

	$this->title = ' Billing : ' . $rn;
	$this->params['breadcrumbs'][] = ['label' => empty($patient_model->name)?'Unknown':$patient_model->name, 'url' => ['site/admission', 'id' => $patient_model->patient_uid]];
	$this->params['breadcrumbs'][] = Yii::t('app','Billing');

?>
<style>
.textColor {
    color: red;
}


.disabled-link {
    pointer-events: none;
}

textarea {
    height: 1em;
    width: 50%;
    padding: 3px;
    transition: all 0.5s ease;
}

.textarea-expand:focus {
    height: 5em;
}

.btn:focus {
    outline-color: transparent;
    outline-style: solid;
    box-shadow: 0 0 0 4px #5a01a7;
    transition: 0.2s;
}

.form-control.is-valid {
    border-color: #28a745 !important;
	padding-right:0 !important;
	background-image:none !important;
}
.form-control.is-valid, .was-validated .form-control:valid {
    border-color: #28a745 !important;
    padding-right:0 !important;
	background-image:none !important;
}

.form-control.is-invalid, .was-validated .form-control:invalid {
    border-color: #dc3545 !important;
    padding-right:0 !important;
	background-image:none !important;
}

.was-validated .form-control:invalid, .form-control.is-invalid {
    border-color: #dc3545; !important
    padding-right:0 !important;
	background-image:none !important;
}
</style>


<div class="bill-form">

	<?php $form = kartik\form\ActiveForm::begin([
		'id' => 'bill-form',
		'type' => 'vertical',
		'fieldConfig' => [
			'template' => "{label}\n{input}\n{error}",
			'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
		],
		'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
	]); 
	?>
		<a name="bill">
			<div class="card" id="bill_details">
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
				
                    <?= $form->field($bill_model, 'bill_uid')->hiddenInput(['readonly' => true, 'maxlength' => true])->label(false) ?>

                    <?= $form->field($bill_model, 'rn')->hiddenInput(['readonly' => true, 'maxlength' => true])->label(false) ?>
					<div class="row" style="background-color:aliceblue">
						<div class="col-sm-6">

							<?=	$form->field($bill_model, 'status_code')->widget(Select2::classname(), 
							[
								'data' => empty($bill_model->status_code)?$patient_model->hasValidIC()?
													['PD'=> Lookup_status::findOne(['status_code'=>'PD'])->status_description]
													:['PDOA'=>Lookup_status::findOne(['status_code'=>'PDOA'])->status_description]
													:[$bill_model->status_code=>$bill_model->status_description],//ArrayHelper::map($data, 'status_code', 'status_description'),
								'options' => [
									'class' => 'formHighlight',
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
									'value' => empty($bill_model->status_code)?$patient_model->hasValidIC()? 'PD' : 'PDOA': $bill_model->status_code,
									'placeholder' => 'Please select status code'],
									'pluginOptions' => [
										'minimumInputLength' => 2,
										'language' => [
											'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
										],

										'ajax' => [
											'url' => \yii\helpers\Url::to(['load-status-code']),
											'dataType' => 'json',
											'data' => new JsExpression('function(params) { return {q:params.term}; }'),	
										],											
										'escapeMarkup' => new JsExpression('function (model_row) { return model_row; }'),
										'templateResult' => new JsExpression(empty($templateResult)? 'function(model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateResult),
										'templateSelection' => new JsExpression(empty($templateSelection)?'function (model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateSelection),
									
									],
									
									'pluginEvents' => ["change" => 'function(){updateWardDailyCost()}',],
							]);?>
						</div>

						<div class="col-sm-3">
							<?= $form->field($bill_model, 'class')->dropDownList(array(
																			"1a" =>'1a', 
																			"1b" =>'1b', 
																			"1c" =>'1c', 
																			"2" =>'2', 
																			"3" =>'3', 
																		), 
								[
									'id'=>'wardClass',
									'prompt'=> Yii::t('app','Please select ward class'), 
									'value' => $admission_model->initial_ward_class == 'UNKNOWN' ? 3 : $admission_model->initial_ward_class,
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
									"onchange" => 'updateWardDailyCost()',
								]
								
								) ?>
						</div>
						<div class="col-sm-3">
							<?= $form->field($bill_model, 'daily_ward_cost')->textInput(['autocomplete' =>'off', 'tabindex' => '-1', 'maxlength' => true, 'id'=>'daily_ward_cost',  
									'readonly' => true, 'disabled' => true]) ?>
						</div>
					</div>
					<div class="row" style="background-color:aliceblue">
						<div class="col-sm-6">
							<?=	$form->field($bill_model, 'department_code')->widget(Select2::classname(), [
								'data' => empty($bill_model->department_code)?
										[]
										:[$bill_model->department_code=> $bill_model->department_name],//ArrayHelper::map($data, 'status_code', 'status_description'),
								'options' => [
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
									'value' => empty($bill_model->department_code)?null: $bill_model->department_code,
									'placeholder' => 'Please select department code'],
									'pluginOptions' => [
										'minimumInputLength' => 2,
										'language' => [
											'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
										],
										'ajax' => [
											'url' => \yii\helpers\Url::to(['load-department-code']),
											'dataType' => 'json',
											'data' => new JsExpression('function(params) { return {q:params.term}; }'),
										],
									'escapeMarkup' => new JsExpression('function (model_row) { return model_row; }'),
									'templateResult' => new JsExpression(empty($templateResult)? 'function(model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateResult),
									'templateSelection' => new JsExpression(empty($templateSelection)?'function (model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateSelection),
								],
							]);?>
						</div>
						<div class="col-sm-6">
							<?=	$form->field($bill_model, 'collection_center_code')->widget(Select2::classname(), [
								'data' => empty($bill_model->collection_center_code)?
									[]:
									[$bill_model->collection_center_code=> Lookup_general::find()->where(['code'=>$bill_model->collection_center_code,'category'=>'Collection Center'])->one()->name],//ArrayHelper::map($data, 'status_code', 'status_description'),
								'options' => [
									'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,
									'value' => empty($bill_model->collection_center_code)?null: $bill_model->collection_center_code,
									'placeholder' => 'Please select collection center code'],
									'pluginOptions' => [
										'minimumInputLength' => 2,
										'language' => [
											'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
										],
										'ajax' => [
											'url' => \yii\helpers\Url::to(['load-collection-center-code']),
											'dataType' => 'json',
											'data' => new JsExpression('function(params) { return {q:params.term}; }'),
										],
									'escapeMarkup' => new JsExpression('function (model_row) { return model_row; }'),
									'templateResult' => new JsExpression(empty($templateResult)? 'function(model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateResult),
									'templateSelection' => new JsExpression(empty($templateSelection)?'function (model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateSelection),
								],
							]);?>                        
						</div>
					</div>
					<br/>
                    <div class="row">
						<div class="col-sm-6">
							<?= $form->field($bill_model, 'is_free')->radioList([0=>'No', 1=>'Yes'], ['value' => $page_mode > Utils::bill_page_mode_new? $bill_model->is_free:0, 'custom' => true, 'inline' => true, 'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]); ?>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<?= $form->field($bill_model, 'description')->textArea(['autocomplete' =>'off', 'maxlength' => true,'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]) ?>

						</div>
					</div>
					<br/>
					<hr/>
					
					<div class="row">
						<div class="col-sm-6">
							<?= $form->field($bill_model, 'guarantor_name')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]) ?>
						</div>
						<div class="col-sm-6">
							<?= $form->field($bill_model, 'guarantor_nric')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'disabled' => ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]) ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<?= $form->field($bill_model, 'guarantor_phone_number')->textInput(['autocomplete' =>'off', 'maxlength' => true,
							  'disabled' =>  ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]) ?>
						</div>
						<div class="col-sm-6">
							<?= $form->field($bill_model, 'guarantor_email')->textInput(['autocomplete' =>'off', 'maxlength' => true, 
							 'disabled' =>  ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]) ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<?= $form->field($bill_model, 'guarantor_comment')->textArea(['autocomplete' =>'off', 'maxlength' => true, 
								'disabled' =>  ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]) ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-9">
							<?= $form->field($bill_model, 'guarantor_address1')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'disabled' =>  ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,]) ?>
							<?= $form->field($bill_model, 'guarantor_address2')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'disabled' =>  ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false) ?>
							<?= $form->field($bill_model, 'guarantor_address3')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'disabled' =>  ($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable,])->label(false) ?>
						</div>
					</div>
					<?php
						if(!(($page_mode >= Utils::bill_page_mode_generated)?true:!$editeable))
							echo Html::submitButton(Yii::t('app',$page_mode == Utils::bill_page_mode_new?'Save':'Update'), ['name' => 'saveBill', 'value' => 'true', 'class' => 'btn btn-success']);
					?>
				</div>
			</div>
			</div>


<?php kartik\form\ActiveForm::end(); ?>
</a>

<?php //ward section
	if($page_mode >= Utils::bill_page_mode_created){
		echo $this->render('_bill_ward_section_clem', [
			'bill_model' => $bill_model,
			'admission_model' => $admission_model,
			'wards_query' => Ward::find()->where(['bill_uid'=>$bill_model->bill_uid])->orderBy(Ward::getNaturalSortArgs()),
		]);
	}
?>



<?php //treatment section
	if($page_mode >= Utils::bill_page_mode_created){
		echo $this->render('_bill_treatment_section_clem', [
			'bill_model' => $bill_model,
			'admission_model' => $admission_model,
			'treatments_query' => Treatment_details::find()->where(['bill_uid'=>$bill_model->bill_uid])->orderBy(Treatment_details::getNaturalSortArgs()),
		]);
	}

?>

<?php //fpp section //leaving this till later
	if($page_mode >= Utils::bill_page_mode_created){
	
	
	}

?>

<?php //generate section
	if($page_mode >= Utils::bill_page_mode_created){
		echo $this->render('_bill_generate_section_clem', [
			'bill_model' => $bill_model,
			'rn'=>$rn,
		]);
	
	}

?>

<!--cancellation section-->

<div class="card" id="cancellation_div" style="display:none;" <?=($page_mode >= Utils::bill_page_mode_generated)?'':'hidden'?>>
	<div class="card-header text-white bg-primary">
		<h3 class="card-title"><?php echo Yii::t('app','Cancellation');?></h3>
		<div class="d-flex justify-content-end">
			<div class="card-tools">
				<!-- Collapse Button -->
				<button type="button" class="btn btn-tool" data-card-widget="collapse"><i
						class="fas fa-minus"></i></button>
			</div>
		</div>
		<!-- /.card-tools -->
	</div>
	<!-- /.card-header -->
	<div class="card-body" id="cancellation-div">
	<?php
		$cancellation_model = Cancellation::findOne(['cancellation_uid'=>$bill_model->bill_uid]);
		if(empty($cancellation_model))
			$cancellation_model = new Cancellation();
		if($page_mode >= Utils::bill_page_mode_generated){
			echo $this->render('/cancellation/create', [
				'model_admission' => null,
				'model_cancellation' => $cancellation_model,
				'type' => 'bill_clem',
				'bill_uid'=>$bill_model->bill_uid
			]);
		}
	?>
	</div>
	<!-- /.card-body -->
</div>

<?php //print section
	if($page_mode >= Utils::bill_page_mode_generated & !$bill_model->is_free){
		echo $this->render('_bill_print_section_clem', [
			'bill_model' => $bill_model,
			'rn'=>$rn,
		]);
	}

?>


<?php //registering event scripts
	if($page_mode >= Utils::bill_page_mode_created){
		$this->registerJs("$('#bill_details').CardWidget('collapse');");
	}

	if($page_mode >= Utils::bill_page_mode_generated)
	{
		$this->registerJs(
			"
				$('#ward_details').CardWidget('collapse');
				$('#treatment_details').CardWidget('collapse');
				$('#fpp_details').CardWidget('collapse');
			"
		);
	}

	$checkFPP = Fpp::findAll(['bill_uid' => Yii::$app->request->get('bill_uid')]);
	if(empty($checkFPP)){
		$this->registerJs(
			"$('#fpp_details').CardWidget('collapse');"
		);
	}
?>


<script>

//For when page is ready
	updateWardDailyCost();

//
function updateWardDailyCost() {
	if(<?=$page_mode < Utils::bill_page_mode_generated?1:0?>) //if bill has been generated, don't do anything
	{
		var xmlhttp = new XMLHttpRequest(); //ajax can't be used for functions that will be executed on page load
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("daily_ward_cost").value = this.responseText;
			}
		};
		xmlhttp.open("GET", "<?=\yii\helpers\Url::to(['calculate-ward-daily-cost'])?>" + "&status_code=" + document.getElementById("bill-status_code").value + "&ward_class="+document.getElementById("wardClass").value, true);
		xmlhttp.send();
	}
}

function toggleCancelCard(){
    if (document.getElementById("cancellation_div").style.display == "none") {
        document.getElementById("cancellation_div").style.display = "block";
    } else {
        document.getElementById("cancellation_div").style.display = "none";
    }
}
</script>




