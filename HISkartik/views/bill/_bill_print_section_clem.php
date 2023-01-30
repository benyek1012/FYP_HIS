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
	
	
	$printer_choices = array("Printer 1" => "Printer 1", "Printer 2" => "Printer 2");

?>


<div class="card" id="print">
	<div class="card-header text-white bg-primary">
		<h3 class="card-title">
			<?= Yii::t('app','Printing Details')?>
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
		<?php $form = kartik\form\ActiveForm::begin([
				'id' => 'bill_print_form',
				'action'=> \yii\helpers\Url::to(['bill-print-by-clem']),
				'type' => 'vertical',
				'fieldConfig' => [
					'template' => "{label}\n{input}\n{error}",
					'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
				],
			]); 
		?>
		<div class="row">
			<?= $form->field($bill_model, 'bill_uid')->hiddenInput(['id'=>'print_bill_uid', 'value'=>$bill_model->bill_uid])->label(false)?>
			<div class="col-sm-12" id="choose_printer_div" <?=(($page_mode >= Utils::bill_page_mode_printed)?true:!$editeable)?'hidden':''?> >
				<?= $form->field($bill_model, 'printer_choices')->radioList($printer_choices, 
				['maxlength' => true, 'id' => 'printer_radio', 'custom' => true, 'inline' => true, 
				'value' => Yii::$app->session->get('bill_printer_session'), 'onChange'=>'choosePrinter();']) ?>
			</div>
			<div class="col-sm-12">
				<?= $form->field($bill_model, 'bill_print_id')->textInput(['autocomplete' =>'off', 'maxlength' => true, 'disabled' => ($page_mode >= Utils::bill_page_mode_printed)?true:!$editeable, 'id' => 'serial_number']) ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
			<?php 
				if( $page_mode >= Utils::bill_page_mode_printed )
					echo "<span class='badge badge-primary'>".Yii::t('app','Bill has been printed')."</span> <br/><br/>"; 
				else{
					echo Html::submitButton(Yii::t('app', 'Print'), ['class' => 'btn btn-primary', 'disabled' => ($page_mode >= Utils::bill_page_mode_printed)?true:!$editeable,]);
					echo Html::button(Yii::t('app', 'Refresh Number'), 
								['class' => 'btn btn-secondary', 'id' => 'refresh', 'onclick' => "choosePrinter()", 'disabled' => ($page_mode >= Utils::bill_page_mode_printed)?true:!$editeable,]);
				}
			?>
			</div>
		</div>
        <!-- /.card-body -->
    </div>

    <?php kartik\form\ActiveForm::end(); ?>
</div>


<script>

// auto refresh the bill print ID based on printer chosen
function choosePrinter() {
	if(<?=(($page_mode >= Utils::bill_page_mode_printed)?1:!$editeable)?1:0?>)
		return;
	//var choice = $('input[name="Bill[printer_choices]"]:checked').val();
    
	//var choice = document.getElementById("printer_radio").value
    if (document.getElementById('printer_radio-0').checked) {//value = 'Printer 1'
		const xhttp = new XMLHttpRequest();
        var url = '<?php echo Url::toRoute(['bill/get_printer_1']); ?>'
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                document.getElementById("serial_number").value = this.responseText;
                //document.getElementById("serial_number").readOnly = true;
            }
        }
        xhttp.open("GET", url, true);
        xhttp.send();
    } else if (document.getElementById('printer_radio-1').checked) {//value = 'Printer 2'
		const xhttp = new XMLHttpRequest();
        var url = '<?php echo Url::toRoute(['bill/get_printer_2']); ?>'
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                document.getElementById("serial_number").value = this.responseText;
                //document.getElementById("serial_number").readOnly = true;
            }
        }
        xhttp.open("GET", url, true);
        xhttp.send();
    }
		
}

var firstPrintChecked = 0;
choosePrinter();
firstPrintChecked = 1;
</script>