<?php

namespace app\controllers;
require 'vendor/autoload.php';

use Yii;
use app\components\Utils;
use app\models\Bill;
use app\models\BillSearch;
use app\models\Cancellation;
use app\models\DateFormat;
use app\models\Fpp;
use app\models\Inpatient_treatment;
use app\models\Lookup_department;
use app\models\Lookup_fpp;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Ward;
use yii\base\Exception;
use app\models\Model;
use yii\helpers\Json;
use app\models\Treatment_details;
use app\models\Lookup_status;
use app\models\Lookup_ward;
use app\models\Lookup_treatment;
use app\models\Lookup_general;
use app\models\Receipt;
use app\models\Patient_information;
use app\models\Patient_admission;
use app\models\Patient_next_of_kin;
use app\models\SerialNumber;
use GpsLab\Component\Base64UID\Base64UID;
use yii\helpers\ArrayHelper;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use yii\helpers\VarDumper;
use app\models\PrintForm;
use DateTime;
use yii\db\Query;


/**
 * BillController implements the CRUD actions for Bill model.
 */
class BillController extends Controller
{
	
	public function actionIndex2($rn) //handles bill page and request to create/update a bill
	{
		//need to check whether RN exists, otherwise return 'no RN found message' 
		if(empty($rn)){
			echo 'RN does not exist';
			return;
		}
		
		//url may send in just RN, or RN + bill_uid
		//just RN will show 'default bill' whether it exists or not
		//RN + bill_uid will show the specific bill if the rn given is correct 

		
		if ($this->request->isPost) {//only handling main bill 'save' and 'update' functionality
			$requested_bill_uid = Yii::$app->request->get('bill_uid'); //by default should be blank 
			
			if(empty($requested_bill_uid)) //get default bill		
				$model = Bill::find()->where(['rn' => $rn])->andWhere(['<>','deleted', 1])->one();
			else //load forced bill 
				$model = Bill::find()->where(['rn' => $rn, 'bill_uid'=>$requested_bill_uid])->one();
			
			if(empty($model))
				$model = new Bill();
			//echo 'Attempting to load values';			
            if($model->load($this->request->post())) {
				//var_dump($_POST);
				
				$bill_model = Bill::find()->where(['bill_uid'=>$model->bill_uid])->one();		
				if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
					['success'=>false, 'message'=>'Bill can not be edited at this moment'];
				
				if(!empty($model->status_code))
					$model->status_description = Lookup_status::find()->where(['status_code'=>$model->status_code])->one()->status_description;
				if(!empty($model->department_code))
					$model->department_name = Lookup_department::find()->where(['department_code'=>$model->department_code])->one()->department_name;
				

				//echo 'status_code:'.$model->status_code;
				//echo 'ward_class:'.$model->class;
				if((!empty($model->status_code))&(!empty($model->class))){
					$model->daily_ward_cost = $this->calculateWardDailyCost($model->status_code, $model->class);
					//echo 'updated daily_ward_cost';
				}
				//echo 'Attempting to save';
				
				
				if($model->save()){
					//echo 'Saved';
					if(Ward::find()->where(['bill_uid'=>$model->bill_uid])->count() == 0){ //if bill exists and no ward exists, create a ward
						//echo 'attempting to create new ward';
						$admissionModel = Patient_admission::find()->where(['rn' => $model->rn])->one();
						if(Lookup_ward::find()->where(['ward_code'=>$admissionModel->initial_ward_code])->count() != 0){
							//echo 'attempting to create new ward part 2';
							$newModelWard = new Ward(); 
							
							$newModelWard->bill_uid = $model->bill_uid; //copied from ward controller
							$newModelWard->ward_uid = Base64UID::generate(32);
							$newModelWard->ward_start_datetime = substr($admissionModel->entry_datetime,0,16);
							$newModelWard->ward_end_datetime = date("Y-m-d H:i");
							$newModelWard->dirtyUpdateWardEndDateAndTime();
							$newModelWard->ward_number_of_days = $newModelWard->calculateDays();
							$newModelWard->ward_code = $admissionModel->initial_ward_code;
							$newModelWard->ward_name = Lookup_ward::find()->where(['ward_code'=>$admissionModel->initial_ward_code])->one()->ward_name;
							//echo 'attempting to save new ward';
							if(!$newModelWard->save()) {
								var_dump($newModelWard->errors);
								echo 'Failed to add ward to bill';
							}
							

						}
					}
					
					$newModelInpatientTreatment = Inpatient_treatment::find()->where(['bill_uid'=>$model->bill_uid])->one();
					if(empty($newModelInpatientTreatment)){
						$newModelInpatientTreatment = new Inpatient_treatment();
						$newModelInpatientTreatment->inpatient_treatment_uid = Base64UID::generate(32);
					}
					$newModelInpatientTreatment->bill_uid = $model->bill_uid;
					$newModelInpatientTreatment->recalculateCost();
					if(!$newModelInpatientTreatment->save()) {
						var_dump($newModelInpatientTreatment->errors);
						echo 'Failed to add inpatient treatment cost to bill';
					}
				}else{
					var_dump($model->errors);
					echo 'Failed to save bill details';
				}
			}
		}
		
		return $this->render('index_clem', [
			'rn' => $rn,
			'bill_uid' => Yii::$app->request->get('bill_uid'), //by default should be blank 
			'data' => Lookup_status::find()->select(['status_code','status_description'])
								->where(['like','status_code', 'ac%',false])
								->orWhere(['like','status_description', 'ac%',false])
								->all(),
        ]);
	}

	public function actionLoadStatusCode($q = null, $id = null) {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		if (!is_null($q)) {
			
			$data = (new \yii\db\Query())
								->select(['status_code as id','status_description as text'])
								->from('lookup_status')
								->where(['like','status_code', $q.'%',false])
								->orWhere(['like','status_description', $q.'%',false])
								->orderby(Lookup_status::getNaturalSortArgs())
								->all();
			
			$out['results'] = array_values($data);
			
		}
		elseif ($id > 0) { //if id exists, load original value instead
			//$out['results'] = ['id' => $id, 'text' => Lookup_status::find($id)->status_description];
		}
		return $out;
	}
	
	public function actionLoadDepartmentCode($q = null, $id = null) {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		if (!is_null($q)) {
			
			$data = (new \yii\db\Query())
								->select(['department_code as id','department_name as text'])
								->from('lookup_department')
								->where(['like','department_code', $q.'%',false])
								->orWhere(['like','department_name', $q.'%',false])
								->orderby(Lookup_department::getNaturalSortArgs())
								->all();
			
			$out['results'] = array_values($data);
			
		}
		elseif ($id > 0) { //if id exists, load original value instead
			//$out['results'] = ['id' => $id, 'text' => Lookup_status::find($id)->status_description];
		}
		return $out;
	}
	
	public function actionLoadCollectionCenterCode($q = null, $id = null) {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		if (!is_null($q)) {
			
			$data = (new \yii\db\Query())
								->select(['code as id','name as text'])
								->from('lookup_general')
								->where(['category'=>'Collection Center'])
								->andwhere(['or',['like','code', $q.'%',false],['like','name', $q.'%',false]])
								->orderby(Lookup_general::getNaturalSortArgs())
								->all();
			
			$out['results'] = array_values($data);
			
		}
		elseif ($id > 0) { //if id exists, load original value instead
			//$out['results'] = ['id' => $id, 'text' => Lookup_status::find($id)->status_description];
		}
		return $out;
	}
	
	public function calculateWardDailyCost($status_code = null, $ward_class = null)
	{
		if(empty($status_code) OR empty($ward_class) ){
			return "Unknown";
		}
		
		$result = Lookup_status::findOne(['status_code' =>$status_code]);
		
		if(empty($result))
			return "Unknown";
		else
			return $result['class_'.$ward_class.'_ward_cost'];
	}
	public function actionCalculateWardDailyCost($status_code = null, $ward_class = null){

		return $this->calculateWardDailyCost($status_code, $ward_class);
	}
	
	public function actionLoadWardCode($q = null, $id = null){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		if (!is_null($q)) {
			
			$data = (new \yii\db\Query())
								->select(['ward_code as id','ward_name as text'])
								->from('lookup_ward')
								->where(['like','ward_code', $q.'%',false])
								->orWhere(['like','ward_name', $q.'%',false])
								->orderby(Lookup_ward::getNaturalSortArgs())
								->all();
			
			$out['results'] = array_values($data);
			
		}
		elseif ($id > 0) { //if id exists, load original value instead
			//$out['results'] = ['id' => $id, 'text' => Lookup_status::find($id)->status_description];
		}
		return $out;
	}
	
	public function actionWardCreate(){
		$new_ward_model = new Ward();
		if ($this->request->isPost && $new_ward_model->load($this->request->post())) {
			
			
			$bill_model = Bill::find()->where(['bill_uid'=>$new_ward_model->bill_uid])->one();		
			if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
				['success'=>false, 'message'=>'Bill can not be edited at this moment'];
			
			
			$new_ward_model->ward_uid = Utils::generateUID();
			$new_ward_model->ward_name = Lookup_ward::findOne(['ward_code'=>$new_ward_model->ward_code])->ward_name;
			//echo 'lala' . $new_ward_model->ward_name . ' ';
			//var_dump($new_ward_model['ward_end_date']);
			//echo $_POST['_csrf'];
			//echo $new_ward_model->end_date . ' ' . $new_ward_model->end_time ;
			$new_ward_model->ward_end_datetime = $new_ward_model->getWardEndDatetime();
			$new_ward_model->ward_number_of_days = $new_ward_model->calculateDays();
            if(!$new_ward_model->validate()){
				var_dump($new_ward_model->errors);
				return;
			}
			if(!$new_ward_model->save()) //probably validation error
				return 'Error';
			
			$this->updateFinalWardDatetime($bill_model);
			
			return $this->renderAjax('_bill_ward_section_row_clem', [
												'ward_model' => $new_ward_model,
										]);
		}
		
		
		return 'Error';
	}
	public function actionWardUpdate(){
	
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$ward_model = null;
		
		//var_dump($_POST);
		//return;
			
		if(empty($ward_model = Ward::find()->where(['ward_uid'=>$_POST['Ward']['ward_uid']])->one()))
			return ['success'=>false, 'message'=>'ward_uid does not exist'];
		
		
		$bill_model = Bill::find()->where(['bill_uid'=>$ward_model->bill_uid])->one();		
		if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
			['success'=>false, 'message'=>'Bill can not be edited at this moment'];

		if ($this->request->isPost && $ward_model->load($this->request->post())) { //update model with new content
			
			//$ward_model->ward_uid = Utils::generateUID();
			
			$ward_model->ward_name = Lookup_ward::findOne(['ward_code'=>$ward_model->ward_code])->ward_name;
			$ward_model->ward_end_datetime = $ward_model->getWardEndDatetime();
			$ward_model->ward_number_of_days = $ward_model->calculateDays();
            if(!$ward_model->validate())
				return ['success'=>false, 'message'=>'Ward values are not correct'];
			if(!$ward_model->save()) //probably validation error
				return ['success'=>false, 'message'=>'Failed to save'];
		
			
			$this->updateFinalWardDatetime($bill_model);
			$bill_uid = $ward_model->bill_uid;
			$total_days = Ward::find()->where(['bill_uid'=>$bill_uid])->sum('ward_number_of_days');
			$daily_ward_cost = Bill::find()->where(['bill_uid'=>$bill_uid])->one()->daily_ward_cost;
			return ['success'=>true, 
				'ward_number_of_days' => $ward_model->ward_number_of_days, 
				'ward_total_days'=> empty($total_days)?0:$total_days,
				'ward_total_cost' => number_format((float)((empty($total_days)?0:$total_days)*$daily_ward_cost), 2, '.', ''), 
				];
		}
		//$wards_query->sum('ward_number_of_days')*$bill_model->daily_ward_cost)
		
		return ['success'=>false, 'message'=>'end of function: Code should not reach here'];
	}
	public function actionWardDelete(){
		

		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//var_dump($_POST);
		
	
		$ward_model = null;

		//if(empty($ward_model = Ward::find()->where(['ward_uid'=>$_POST['Ward']['ward_uid']])->one()))
		if(empty($ward_model = Ward::find()->where(['ward_uid'=>$_POST['ward_uid']])->one()))
			return ['success'=>false, 'message'=>'ward_uid does not exist'];
		
		$bill_uid = $ward_model->bill_uid;
		
		$bill_model = Bill::find()->where(['bill_uid'=>$bill_uid])->one();		
		if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
			['success'=>false, 'message'=>'Bill can not be edited at this moment'];
		
		if($ward_model->delete()){
			$total_days = Ward::find()->where(['bill_uid'=>$bill_uid])->sum('ward_number_of_days');
			$daily_ward_cost = Bill::find()->where(['bill_uid'=>$bill_uid])->one()->daily_ward_cost;
			$this->updateFinalWardDatetime($bill_model);
			return ['success'=>true, 
				'ward_total_days'=> empty($total_days)?0:$total_days,
				'ward_total_cost' => number_format((float)((empty($total_days)?0:$total_days)*$daily_ward_cost), 2, '.', ''), ];			
		}
		else 
			return ['success'=>false, 'message'=>'failed to delete'];

		return ['success'=>false, 'message'=>'end of function: Code should not reach here'];
	}
	public function updateFinalWardDatetime($bill_model){
		$bill_model->final_ward_datetime = Ward::find()->where(['bill_uid'=>$bill_model->bill_uid])->max('ward_end_datetime');
		$bill_model->save();
	}
	public function actionUpdateInpatientTreatmentDataAndTotalTreatmentCost($bill_uid){

		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$inpatient_treatment_model = Inpatient_treatment::find()->where(['bill_uid'=>$bill_uid])->one();
		if(empty($inpatient_treatment_model)){
			$inpatient_treatment_model = new Inpatient_treatment();
			$inpatient_treatment_model->bill_uid = $bill_uid;
			$inpatient_treatment_model->inpatient_treatment_uid = Utils::generateUID();
		}
		
		$inpatient_treatment_model->recalculateCost();
		$inpatient_treatment_model->save();
		$total_treatment_detail_cost = Treatment_details::find()->where(['bill_uid'=>$bill_uid])->sum('item_total_unit_cost_rm');
		
		if(empty($total_treatment_detail_cost))
			$total_treatment_detail_cost = 0;
		
		return ['success'=>true, 
			'inpatient_treatment_cost' => number_format((float)$inpatient_treatment_model->inpatient_treatment_cost_rm, 2, '.', ''), 
			'treatment_total_cost' => number_format((float)($inpatient_treatment_model->inpatient_treatment_cost_rm + $total_treatment_detail_cost), 2, '.', ''), 
			];
	
	}
		
	
	public function actionLoadTreatmentCode($q = null, $id = null){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		if (!is_null($q)) {
			
			$data = (new \yii\db\Query())
								->select(['treatment_code as id','treatment_name as text'])
								->from('lookup_treatment')
								->where(['like','treatment_code', $q.'%',false])
								->orWhere(['like','treatment_name', $q.'%',false])
								->orderby(Lookup_treatment::getNaturalSortArgs())
								->all();
			
			$out['results'] = array_values($data);
			
		}
		elseif ($id > 0) { //if id exists, load original value instead
			//$out['results'] = ['id' => $id, 'text' => Lookup_status::find($id)->status_description];
		}
		return $out;
	}
	
	
	public function actionTreatmentCreate(){
		$new_treatment_model = new Treatment_details();
		if ($this->request->isPost && $new_treatment_model->load($this->request->post())) {
			
			$bill_model = Bill::find()->where(['bill_uid'=>$new_treatment_model->bill_uid])->one();		
			if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
				['success'=>false, 'message'=>'Bill can not be edited at this moment'];
			
			$new_treatment_model->treatment_details_uid = Utils::generateUID();
			$new_treatment_model->refreshInfoAndRecalculate();
			
            if(!$new_treatment_model->validate()){
				var_dump($new_treatment_model->errors);
				return;
			}
			if(!$new_treatment_model->save()) //probably validation error
				return 'Error';
			
			
			return $this->renderAjax('_bill_treatment_section_row_clem', [
												'treatment_model' => $new_treatment_model,
										]);
		}
		
		
		return 'Error';
	}
	public function actionTreatmentUpdate(){
	
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$treatment_model = null;
		
		//var_dump($_POST);
		//return;
			
		if(empty($treatment_model = Treatment_details::find()->where(['treatment_details_uid'=>$_POST['Treatment_details']['treatment_details_uid']])->one()))
			return ['success'=>false, 'message'=>'treatment_details_uid does not exist'];
		

		$bill_model = Bill::find()->where(['bill_uid'=>$treatment_model->bill_uid])->one();		
		if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
			['success'=>false, 'message'=>'Bill can not be edited at this moment'];
		
		if ($this->request->isPost && $treatment_model->load($this->request->post())) { //update model with new content
			
			$treatment_model->refreshInfoAndRecalculate();
            if(!$treatment_model->validate())
				return ['success'=>false, 'message'=>'Treatment values are not correct'];
			if(!$treatment_model->save()) //probably validation error
				return ['success'=>false, 'message'=>'Failed to save'];
	
			return ['success'=>true, 
				'item_per_unit_cost_rm' => number_format((float)$treatment_model->item_per_unit_cost_rm, 2, '.', ''), 
				'item_total_unit_cost_rm' => number_format((float)$treatment_model->item_total_unit_cost_rm, 2, '.', ''), 
				];
		}
		
		return ['success'=>false, 'message'=>'end of function: Code should not reach here'];
	}
	public function actionTreatmentDelete(){
		

		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//var_dump($_POST);
		//return;
	
		$treatment_model = null;
	
		if(empty($treatment_model = Treatment_details::find()->where(['treatment_details_uid'=>$_POST['treatment_details_uid']])->one()))
			return ['success'=>false, 'message'=>'treatment_details_uid does not exist'];
		
		$bill_uid = $treatment_model->bill_uid;
		
		$bill_model = Bill::find()->where(['bill_uid'=>$bill_uid])->one();		
		if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
			['success'=>false, 'message'=>'Bill can not be edited at this moment'];
		
		if($treatment_model->delete()){
			return ['success'=>true,];			
		}
		else 
			return ['success'=>false, 'message'=>'failed to delete'];

		return ['success'=>false, 'message'=>'end of function: Code should not reach here'];
	}
	public function getGenerateValues($bill_uid){
		$bill_model = Bill::find()->where(['bill_uid'=>$bill_uid])->one();
		if(empty($bill_model))
			return ['success'=>false,'message'=>'Bill does no exist'];
		
		$billable_text = $bill_model->calculateBillable($bill_uid);
		$final_fee_text = $bill_model->calculateFinalFee($bill_uid);
		$sum_payments_text = number_format((float)$final_fee_text - (float)$billable_text , 2, '.', '');//negative if they have paid
		return (['success'=>true, 'gen_date'=> date("Y-m-d H:i"),'billable_text'=>$billable_text, 'sum_payments_text'=>$sum_payments_text, 'final_fee_text'=>$final_fee_text]);
	}
	
	public function actionGetGenerateValues($bill_uid){

		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$ret_Array = $this->getGenerateValues($bill_uid);
		
		if($ret_Array['success'])
			return ['success'=>$ret_Array['success'], 'generate_nonForm_generate_calculate_datetime'=> $ret_Array['gen_date'],'generate_nonForm_billable_total'=>$ret_Array['billable_text'], 'generate_nonForm_sum_payments'=>$ret_Array['sum_payments_text'], 'generate_nonForm_final_fee'=>$ret_Array['final_fee_text']];
		else	
			return ['success'=>$ret_Array['success'],'message'=>$ret_Array['message']];
	}
	public function actionBillGenerateByClem(){

		
		
		$connection = \Yii::$app->db;
		$transaction = $connection->beginTransaction();;
		try {
			
			$bill_uid = $_POST['Bill']['bill_uid'];
		
			$bill_model = Bill::find()->where(['bill_uid'=>$bill_uid])->one();
			if(empty ($bill_model))
				throw new Exception('Bill not found');
			if(!empty ($bill_model->bill_print_datetime))
				throw new Exception('Bill has been printed previously');
			if(!empty ($bill_model->bill_generation_datetime))
				throw new Exception('Bill has been generated previously');
			
			if(!Utils::checkAndSetEditeable((!empty($bill_model->deleted)) && ($bill_model->deleted == 1)))
				throw new Exception('Bill can not be edited at this moment');
			
			//update simpler bill details (found at index2)
			if(!empty($bill_model->status_code))
				$bill_model->status_description = Lookup_status::find()->where(['status_code'=>$bill_model->status_code])->one()->status_description;
			if(!empty($bill_model->department_code))
				$bill_model->department_name = Lookup_department::find()->where(['department_code'=>$bill_model->department_code])->one()->department_name;
			if((!empty($bill_model->status_code))&(!empty($model->class))){
				$bill_model->daily_ward_cost = $this->calculateWardDailyCost($bill_model->status_code, $bill_model->class);
			}
			$bill_model->discharge_date = $_POST['Bill']['discharge_date'];
			if(!$bill_model->validate())
				throw new Exception("Bill failed to validate");
			if(!$bill_model->save()) //probably validation error
				throw new Exception("Bill failed to save: ".$bill_model->errors);
			
			//recalculate wards (found at ward_update)
			$ward_models = Ward::find()->where(['bill_uid' => $bill_uid])->all();
			foreach($ward_models as $ward_model){
				$ward_model->ward_name = Lookup_ward::findOne(['ward_code'=>$ward_model->ward_code])->ward_name;
				$ward_model->ward_start_datetime = substr($ward_model->ward_start_datetime, 0, 16); //shorten to pass validation
				$ward_model->ward_end_datetime = substr($ward_model->ward_end_datetime, 0, 16); //shorten to pass validation
				$ward_model->ward_number_of_days = $ward_model->calculateDays();
				$ward_model->dirtyUpdateWardEndDateAndTime();
				if(!$ward_model->validate()){
					//var_dump( $ward_model);
					//var_dump( $ward_model->ward_uid);
					var_dump( $ward_model->errors);
					throw new Exception("Ward failed to validate ");
				}
				if(!$ward_model->save()){ //probably validation error
					var_dump( $ward_model->errors);
					throw new Exception("Ward failed to save: ".$ward_model->errors);
				}
			}
			
			$this->updateFinalWardDatetime($bill_model);
			
			//recalculate inpatient()
			$inpatient_models = Inpatient_treatment::find()->where(['bill_uid' => $bill_uid])->all();
			foreach($inpatient_models as $inpatient_model){
				$inpatient_model->recalculateCost();
				if(!$inpatient_model->validate()){
					var_dump( $inpatient_model->errors);
					throw new Exception("Inpatient_treatment failed to validate");
				}
				if(!$inpatient_model->save()){ //probably validation error
					var_dump( $inpatient_model->errors);
					throw new Exception("Inpatient_treatment failed to save: ".$inpatient_model->errors);
				}
			}
		
			//recalculate treatment()
			$treatment_models = treatment_details::find()->where(['bill_uid' => $bill_uid])->all();
			foreach($treatment_models as $treatment_model){
				$treatment_model->refreshInfoAndRecalculate();
				if(!$treatment_model->validate()){
					var_dump( $treatment_model->errors);
					throw new Exception("Treatment_details failed to validate");
				}
				if(!$treatment_model->save()){ //probably validation error
					var_dump( $treatment_model->errors);
					throw new Exception("Treatment_details failed to save: ".$treatment_model->errors);
				}
			}
			
			//recalculate fpp()
			$fpp_models = fpp::find()->where(['bill_uid' => $bill_uid])->all();
			foreach($fpp_models as $fpp_model){
				$fpp_model->refreshInfoAndRecalculate();
				if(!$fpp_model->validate()){
					var_dump( $fpp_model->errors);
					throw new Exception("Fpp failed to validate");
				}
				if(!$fpp_model->save()){ //probably validation error
					var_dump( $fpp_model->errors);
					throw new Exception("Fpp failed to save: ".$fpp_model->errors);
				}
			}
			
			//refresh bill model
			$bill_model= Bill::find()->where(['bill_uid'=>$bill_uid])->one();
			$genArray = $this->getGenerateValues($bill_uid);
			if(!$genArray['success'])
				throw new Exception('Bill failed to calculate final costs');
			$bill_model->generation_responsible_uid = Yii::$app->user->identity->getId();
			$bill_model->bill_generation_datetime = date("Y-m-d H:i:s");
			$bill_model->bill_generation_billable_sum_rm = (float)$genArray['billable_text'];
			$bill_model->bill_generation_final_fee_rm = (float)$genArray['final_fee_text'];
			
			if(!$bill_model->validate()){
				var_dump( $bill_model->errors);
				throw new Exception("Bill (2) failed to validate");
			}
			if(!$bill_model->save()){ //probably validation error
				var_dump( $bill_model->errors);
				throw new Exception("Bill (2) failed to save: ".$bill_model->errors);
			}
			
			

			
			
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
			return $e->getMessage();
			//var_dump( $e);
			return;
		} catch (\Throwable $e) {
			$transaction->rollBack();
			return $e->getMessage();
			//var_dump( $e);
			return;
		}
		
		//refreshInfoAndRecalculate
		
		
		//noting after save, models are probably dirty
		
		//update cost recalculation including
		//all kods and kod names 
		//ward_days, cost_per_day, inpatient_cost, treatment cost, 
		//gather discharge_date from _post
		//save 
		//then refresh 'getGenerateValues' (requires new values)
		//set generation datetime, responsible 
		return $this->redirect(['bill/index2','rn'=>$bill_model->rn, 'bill_uid'=>$bill_model->bill_uid]);
		
	}
	
	public function actionBillPrintByClem(){
		//(ignore this, currently set to prioritize printing over saving 
		//prioritize saving over printing. If print fails, save still occurs. 

		$bill_uid = $_POST['Bill']['bill_uid'];
		$bill_model = Bill::findOne(["bill_uid"=>$bill_uid]);
		if(empty($bill_model)){
			echo 'Bill does not exist';
			return;
		}
		$print_number = $_POST['Bill']['bill_print_id'];
		if(empty($print_number)){
			echo 'No bill ID was given';
			return;
		}
		$total_count = Bill::find()->where(['bill_print_id'=>$print_number])->count() + Receipt::find()->where(['receipt_serial_number'=>$print_number])->count();
		if($total_count>0){
			echo 'Bill number has been taken. Please try again.';
			return;
		}	
		$choice = $_POST['Bill']['printer_choices'];
		Yii::$app->session->set('bill_printer_session', $choice);

		$error = PrintForm::printBill($bill_uid);
		
		if(!empty($error))
		{
			echo 'Printing error';
			echo $error;
			return;
		}
		
		$bill_model->bill_print_responsible_uid = Yii::$app->user->identity->getId();
		$bill_model->bill_print_id = $print_number;
		$bill_model->bill_print_datetime = date("Y-m-d H:i:s");
		
		if(!$bill_model->save()){
			echo 'Failed to save printing details';
			var_dump($bill_model->errors);
			return;
			
		}
		

		//update serial
		if($choice == 'Printer 1')
			$model_serial = SerialNumber::findOne(['serial_name' => "bill"]);
		else if($choice == 'Printer 2')
			$model_serial = SerialNumber::findOne(['serial_name' => "bill2"]);
		$str = $bill_model->bill_print_id;
		$only_integer = preg_replace('/[^0-9]/', '', $str);
		$model_serial->prepend = preg_replace('/[^a-zA-Z]/', '', $str);
		$model_serial->digit_length = strlen($only_integer);
		$model_serial->running_value = $only_integer;

		$model_serial->save();  
		

		
		return $this->redirect(['bill/index2','rn'=>$bill_model->rn, 'bill_uid'=>$bill_model->bill_uid]);
	}
	public function actionCancellationClem($bill_uid)
    {
		$connection = \Yii::$app->db;
		$transaction = $connection->beginTransaction();;
		try {
			$bill_model = Bill::find()->where(['bill_uid'=>$bill_uid])->one();


			$model_cancellation = new Cancellation();
			if($this->request->isPost && $model_cancellation->load($this->request->post())){
				if(empty($model_cancellation->deleted_datetime))
				{
					$date = new \DateTime();
					$date->setTimezone(new \DateTimeZone('+0800')); //GMT
					$model_cancellation->deleted_datetime =  $date->format('Y-m-d H:i:s');
				}

				$model_cancellation->cancellation_uid = $bill_model->bill_uid;
				$model_cancellation->table = 'bill';
				$model_cancellation->responsible_uid = Yii::$app->user->identity->getId();
				if($model_cancellation->validate() && $model_cancellation->save()){
					$bill_model->deleted = 1;
					if($bill_model->save()){
					
					}else
						throw new Exception('Failed to update bill');
				}else
					throw new Exception('Failed to update cancellation list');
			}
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
			return $e->getMessage();
			//var_dump( $e);
			return;
		} catch (\Throwable $e) {
			$transaction->rollBack();
			return $e->getMessage();
			//var_dump( $e);
			return;
		}
			
				
		return Yii::$app->getResponse()->redirect(array('/bill/index2', 
				'rn' => $bill_model->rn)); 
    }
	
	
	
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['GET'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Bill models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRefresh()
    {
        return SerialNumber::getSerialNumber("bill");
    }

    public function actionGet_printer_1()
    {
        return SerialNumber::getSerialNumber("bill");
    }

    public function actionGet_printer_2()
    {
        return SerialNumber::getSerialNumber("bill2");
    }

    public function actionStatus($status) {
        $model = Lookup_status::findOne( ['status_code' => $status]);
        echo Json::encode($model);
    }

    public function actionDepartment($department) {
        $model = Lookup_department::findOne( ['department_code' => $department]);
        echo Json::encode($model);
    }

    public function actionTreatment($treatment) {
        $model = Lookup_treatment::findOne( ['treatment_code' => $treatment]);
        echo Json::encode($model);
    }

    public function actionFpp($fpp) {
        $model = Lookup_fpp::findOne(['kod' => $fpp]);
        echo Json::encode($model);
    }

    public function actionWard($ward) {
        $model = Lookup_ward::findOne( ['ward_code' => $ward]);
        echo Json::encode($model);
    }

    public function actionWardRow($ward){
        for($i = 0; $i < $ward; $i++) {
            $modelWard[] = new Ward();
        }
        $modelWard[] = new Ward();

        echo Json::encode($modelWard);
    }

    public function actionTreatmentRow($treatment){
        for($i = 0; $i < $treatment; $i++) {
            $modelTreatment[] = new Treatment_details();
        }
        $modelTreatment[] = new Treatment_details();

        echo Json::encode($modelTreatment);
    }

    // Check Date Clashing
    public function actionDate($bill_uid){
        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(['ward_start_datetime' => SORT_ASC])->all(); 
         
        // if($modelWard != null){
        //     $modelDate = Ward::find()->where(['between', 'ward_start_datetime', $modelWard[0]->ward_start_datetime, $modelWard[0]->ward_end_datetime])->all();
        // }
        echo Json::encode($modelWard);
    }   
    
    // Check FPP Cost Range
    public function actionCost($bill_uid){
        $modelFPP = Fpp::find()->where(['bill_uid' => $bill_uid])->all(); 
         
        // if($modelWard != null){
        //     $modelDate = Ward::find()->where(['between', 'ward_start_datetime', $modelWard[0]->ward_start_datetime, $modelWard[0]->ward_end_datetime])->all();
        // }
        echo Json::encode($modelFPP);
    }  

    public function actionBillable_final_fee($bill_uid) {
        $cost = array();
        $cost['billAble'] = (new Bill()) -> calculateBillable($bill_uid);
        $cost['finalFee'] = (new Bill()) -> calculateFinalFee($bill_uid);
        echo Json::encode($cost);
    }

    /**
     * Displays a single Bill model.
     * @param string $bill_uid Bill Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($bill_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($bill_uid),
        ]);
    }
	

    /**
     * Creates a new Bill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */	 
    public function actionCreate()
    {
        $model = new Bill();



        $rowsWard = (new \yii\db\Query())
            ->select('ward_uid')
            ->from('ward')
            ->where(['bill_uid' => null])
            ->all();

        $rowsTreatment = (new \yii\db\Query())
            ->select('treatment_details_uid')
            ->from('treatment_details')
            ->where(['bill_uid' => null])
            ->all();

        $rowsFPP = (new \yii\db\Query())
            ->select('kod')
            ->from('fpp')
            ->where(['bill_uid' => null])
            ->all();

        if ($this->request->isPost) {
            // Insert Bill
            if($model->load($this->request->post()) && $model->save()) {
                foreach($rowsWard as $rowWard){
                    $modelWard = $this->findModel_Ward($rowWard['ward_uid']);
                    $modelWard->bill_uid = $model->bill_uid;
                    $modelWard->save();
                }
				

				//if bill doesn't have ward, add a ward based on admission details
				if(Ward::find()->where(['bill_uid' => $model->bill_uid])->count() == 0 ){
					$admissionModel = Patient_admission::find()->where(['rn' => $model->rn])->one();
					if(Lookup_ward::find()->where(['ward_code'=>$admissionModel->initial_ward_code])->count() != 0){
						$newModelWard = new Ward(); 
						
						$newModelWard->bill_uid = $model->bill_uid; //copied from ward controller
						$newModelWard->ward_uid = Base64UID::generate(32);
						$newModelWard->ward_start_datetime = substr($admissionModel->entry_datetime,0,16);
						$newModelWard->ward_end_datetime = date("Y-m-d H:i");
						$newModelWard->ward_number_of_days = $newModelWard->calculateDays();
						$newModelWard->ward_code = $admissionModel->initial_ward_code;
						$newModelWard->ward_name = $admissionModel->initial_ward_class;
						$newModelWard->dirtyUpdateWardEndDateAndTime();
						$newModelWard->save();
						
						//var_dump(Bill::find()->where(['bill_uid' => $model->bill_uid])->one()->bill_uid);
						//var_dump(substr($admissionModel->entry_datetime,0,16));
						//var_dump($model->bill_uid);
						//var_dump($newModelWard->validate());
						//var_dump($newModelWard->errors);
						//return;
					}

				}
				

                foreach($rowsTreatment as $rowTreatment){
                    $modelTreatment = $this->findModel_Treatment($rowTreatment['treatment_details_uid']);
                    $modelTreatment->bill_uid = $model->bill_uid;
                    $modelTreatment->save();
                }

                foreach($rowsFPP as $rowFPP){
                    $modelFPP = $this->findModel_FPP($rowFPP['kod']);
                    $modelFPP->bill_uid = $model->bill_uid;
                    $modelFPP->save();
                }

                $model_cancellation = Cancellation::findAll(['replacement_uid' => null]);
              
                if(!empty($model_cancellation)){
                    foreach($model_cancellation as $model_cancellation){
                        $model_bill_cancel = Bill::findOne(['bill_uid' => $model_cancellation->cancellation_uid]);
                        if(!empty($model_bill_cancel))
                            if($model_bill_cancel->rn == $model->rn){
                                $model_cancellation->replacement_uid = $model->bill_uid;
                                $model_cancellation->save();
                            }
                    }
                }
                
                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                    'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'ward'));
            }
        }
        return $this->render('create', [
            'model' => $model,
            'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
            'modelTreatment' =>(empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
            'modelFPP' =>(empty($modelFPP)) ? [new Fpp] : $modelFPP,
            'model_cancellation' => new Cancellation(),
            'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
        ]);
    }


    /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($bill_uid)
    {
        $model = $this->findModel($bill_uid);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->bill_uid = $bill_uid;
            $model->save();           
        }

        $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);
        $wardClass = $model->class;

        foreach($modelTreatment as $modelTreatment){
            $modelLoopUpTreatment = Lookup_treatment::findOne( ['treatment_code' => $modelTreatment->treatment_code]);
            if($wardClass == '1a' || $wardClass == '1b' || $wardClass == '1c') {
                $modelTreatment->item_per_unit_cost_rm = $modelLoopUpTreatment->class_1_cost_per_unit;
            }
            if($wardClass == '2'){
                $modelTreatment->item_per_unit_cost_rm = $modelLoopUpTreatment->class_2_cost_per_unit;
            }
            if($wardClass == '3'){
                $modelTreatment->item_per_unit_cost_rm = $modelLoopUpTreatment->class_3_cost_per_unit;
            }

            $modelTreatment->item_total_unit_cost_rm = $modelTreatment->item_per_unit_cost_rm * $modelTreatment->item_count;

            $modelTreatment->save();
        }      
        
        // return Yii::$app->getResponse()->redirect(array('/bill/generate', 
        //     'bill_uid' => $model->bill_uid, 'rn' => $model->rn, '#' => 'bill'));

        return Yii::$app->getResponse()->redirect(array('/bill/generate', 
            'bill_uid' => $model->bill_uid, 'rn' => $model->rn));
    }

      /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGenerate($bill_uid)
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
            
        $model = $this->findModel($bill_uid);
        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderBy(Ward::getNaturalSortArgs())->all();
		
        //$modelTreatment = Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(['treatment_code'=>SORT_ASC])->all();
        $modelTreatment = Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(Treatment_details::getNaturalSortArgs())->all();
		$modelFPP = Fpp::find()->where(['bill_uid' => $bill_uid])->orderBy(Fpp::getNaturalSortArgs())->all();
        $modelInpatient = Inpatient_treatment::findOne(['bill_uid' => $bill_uid]);
        
        // Update Bill
        if(Yii::$app->request->post('updateBill') == 'true') {
            $this->actionUpdate($bill_uid);
        }

        // Insert and Update Ward
        if(Yii::$app->request->post('saveWard') == 'true') {
            (new WardController(null, null))->actionUpdate();
        }

        // Add Ward Row
        if (Yii::$app->request->post('addWardRow') == 'true') {
            (new WardController(null, null))->actionWardrow();
        }

        // Remove Ward Row
        // if (Yii::$app->request->post('removeWardRow') == 'true') {
        //     // $modelWard = array_pop($modelWard);

        //     return $this->render('generate', [
        //         'model' => $model,
        //         'modelWard' => $modelWard,
        //         'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details] : $modelTreatment,
        //     ]);
        // }

        // Insert and Update Treatment
        if(Yii::$app->request->post('saveTreatment') == 'true') {
            (new Treatment_detailsController(null, null))->actionUpdate();
        }

        // Add Treatment Row
        if (Yii::$app->request->post('addTreatmentRow') == 'true') {
            $dbTreatment = Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(Treatment_details::getNaturalSortArgs())->all();

            if(empty($dbTreatment)) {
                $count = count(Yii::$app->request->post('Treatment_details', []));
                for($i = 0; $i < $count; $i++) {
                    $modelTreatment[] = new Treatment_details();
                }
                $modelTreatment[] = new Treatment_details();
            }
            else {
                $modelTreatment = $dbTreatment;
                $count = count(Yii::$app->request->post('Treatment_details', [])) - count($dbTreatment);
                for($i = 0; $i < $count; $i++) {
                    $modelTreatment[] = new Treatment_details();
                }
                $modelTreatment[] = new Treatment_details();
            }


            return $this->render('generate', [
                'model' => $model,
                'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
                'modelTreatment' => Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(Treatment_details::getNaturalSortArgs())->all(),
                'model_cancellation' => (empty($model_cancellation)) ? [new Cancellation] : $model_cancellation,
                'modelFPP' => (empty($modelFPP)) ? [new Fpp] : $modelFPP,
                'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
            ]);
            // (new Treatment_detailsController(null, null))->actionTreatmentrow();
        }

        // Remove Treatment Row
        // if (Yii::$app->request->post('removeTreatmentRow') == 'true') {
        //     // $modelTreatment = array_pop($modelTreatment);

        //     return $this->render('generate', [
        //         'model' => $model,
        //         'modelWard' => $modelWard,
        //         'modelTreatment' => $modelTreatment,
        //     ]);
        // }

        // Insert and Update Fpp
        if(Yii::$app->request->post('saveFpp') == 'true') {
            (new FppController(null, null))->actionUpdate();
        }

        // Add Fpp Row
        if (Yii::$app->request->post('addFppRow') == 'true') {
            $dbFpp = Fpp::findAll(['bill_uid' => $bill_uid]);

            if(empty($dbFpp)) {
                $count = count(Yii::$app->request->post('Fpp', []));
                for($i = 0; $i < $count; $i++) {
                    $modelFPP[] = new Fpp();
                }
                $modelFPP[] = new Fpp();
            }
            else {
                $modelFPP = $dbFpp;
                $count = count(Yii::$app->request->post('Fpp', [])) - count($dbFpp);
                for($i = 0; $i < $count; $i++) {
                    $modelFPP[] = new Fpp();
                }
                $modelFPP[] = new Fpp();
            }

            return $this->render('generate', [
                'model' => $model,
                'modelWard' => (empty($modelWard)) ? [new Ward] : $modelWard,
                'modelTreatment' => (empty($modelTreatment)) ? [new Treatment_details] : Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(Treatment_details::getNaturalSortArgs())->all(),
                'model_cancellation' => (empty($model_cancellation)) ? [new Cancellation] : $model_cancellation,
                'modelFPP' => $modelFPP,
                'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
            ]);
            // (new FppController(null, null))->actionFpprow();
        }


        $modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(Ward::getNaturalSortArgs())->all(); 
        //$modelTreatment = Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(['treatment_code'=>SORT_ASC])->all();
        $modelTreatment = Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(Treatment_details::getNaturalSortArgs())->all();
        $model_cancellation = new Cancellation();
        $modelFPP = Fpp::find()->where(['bill_uid' => $bill_uid])->orderBy(Fpp::getNaturalSortArgs())->all();
        $modelFPP[] = new Fpp();
        $modelTreatment[] = new Treatment_details();
        $modelWard[] = new Ward();

        return $this->render('generate', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
            'modelFPP' => $modelFPP,
            'model_cancellation' => $model_cancellation,
            'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
        ]); 
    }



    public function actionGeneratebill($bill_uid)
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model = Bill::findOne(['bill_uid' => $bill_uid]);
        $modelWards = Ward::find()->where(['bill_uid'=>$bill_uid]);
        $error_generate = '';


        // // Popup Generation
        if(Yii::$app->request->get('confirm') != 'true'){
            Yii::$app->session->set('billable_sum', (new Bill()) -> calculateBillable(Yii::$app->request->get('bill_uid')));
            Yii::$app->session->set('final_fee', (new Bill()) -> calculateFinalFee(Yii::$app->request->get('bill_uid')));
			if($modelWards->count() == 0)
				return 'No Wards';
			//return $modelWards->count();
            return false; //ready to generate, request page to show a confirm dialogue
        }

        if (Yii::$app->request->get('confirm') == 'true'){
            $checkTreatment = (new Bill()) -> checkTreatmentPekeliling($bill_uid);
            $checkWard = (new Bill()) -> checkWardPekeliling($bill_uid);
            $checkFPP = (new Bill()) -> checkFppPekeliling($bill_uid);
            $checkDepartment = (new Bill()) -> checkDepartmentPekeliling($bill_uid);
            $checkStatus = (new Bill()) -> checkStatusPekeliling($bill_uid);

            if($checkTreatment && $checkWard && $checkFPP && $checkDepartment && $checkStatus){
                if(empty($model->bill_generation_datetime))
                {
                    $model->bill_generation_datetime =  $date->format('Y-m-d H:i:s');

                    if($modelWards->count() > 0)
                    {
                        $final_ward_datetime = Ward::find()->select('ward_end_datetime')->where(['bill_uid' => $bill_uid])
                        ->orderBy('ward_end_datetime DESC')->limit(1)->one();
                        // $model->final_ward_datetime =  date($final_ward_datetime["ward_end_datetime"]);
                        if(!empty($final_ward_datetime["ward_end_datetime"])){
                            $model->final_ward_datetime =  date($final_ward_datetime["ward_end_datetime"]);
                        }
                        else{
                            $model->final_ward_datetime =  $date->format('Y-m-d H:i:s');
                        }
                    }
                    else
                        $model->final_ward_datetime =  $date->format('Y-m-d H:i:s');
                }

                $model->bill_generation_billable_sum_rm = Yii::$app->session->get('billable_sum');
                $model->bill_generation_final_fee_rm = Yii::$app->session->get('final_fee');

                if(!empty(Yii::$app->request->get('discharge'))){
                    if(Yii::$app->request->get('discharge')){
                        $checkFormat = DateTime::createFromFormat('Y-m-d H:i', Yii::$app->request->get('discharge'));

                        if($checkFormat){
                            $validDate = DateFormat::convert(Yii::$app->request->get('discharge'), 'datetime');
                            if($validDate){
                                $model->discharge_date = Yii::$app->request->get('discharge') . ':00';
                            }
                            else{
                                Yii::$app->session->setFlash('error_generate', '
                                    <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                    <strong>'.Yii::t('app', 'Invalid Datetime Format!').' <br/></strong> 
                                    '.Yii::t('app', 'Invalid Datetime Format of Discharge Date').'</div>'
                                );

                                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));
                            }
                        }
                        else{
                            Yii::$app->session->setFlash('error_generate', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Invalid Datetime Format!').' <br/></strong> 
                                '.Yii::t('app', 'Invalid Datetime Format of Discharge Date').'</div>'
                            );

                            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));   
                        }
                    }
                }

                if (Yii::$app->session->has('billable_sum')) Yii::$app->session->remove('billable_sum');
                if (Yii::$app->session->has('final_fee')) Yii::$app->session->remove('final_fee');

                $model->bill_uid = Yii::$app->request->get('bill_uid');
                $model->generation_responsible_uid = Yii::$app->user->identity->getId();
                $model->save();

                return Yii::$app->getResponse()->redirect(array('/bill/print', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));    
            }
            else{
                if(!$checkTreatment){
                    $error_generate .= 'Treatment Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkWard){
                    $error_generate .= 'Ward Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkFPP){
                    $error_generate .= 'FPP Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkDepartment){
                    $error_generate .= 'Department Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkStatus){
                    $error_generate .= 'Status Pekeliling Kod no longer exist'."<br/>";
                }

                    Yii::$app->session->setFlash('error_generate', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>'.Yii::t('app', 'Generate error! ').' <br/></strong> 
                    '.Yii::t('app', $error_generate).'</div>'
                );

                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));   
            }
        }
    }
	public function actionGeneratebill_clemtest($bill_uid) //safe to delete
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model = Bill::findOne(['bill_uid' => $bill_uid]);
        $modelWards = Ward::find()->where(['bill_uid'=>$bill_uid]);
        $error_generate = '';


        // // Popup Generation
        if(Yii::$app->request->get('confirm') != 'true'){
            Yii::$app->session->set('billable_sum', (new Bill()) -> calculateBillable(Yii::$app->request->get('bill_uid')));
            Yii::$app->session->set('final_fee', (new Bill()) -> calculateFinalFee(Yii::$app->request->get('bill_uid')));
			//if($modelWards->count() == 0)
			//	return 'No Wards';
			//return $bill_uid;
			return $bill_uid.'   '.($modelWards->all()[0]->bill_uid);
            return false; //ready to generate, request page to show a confirm dialogue
        }

        if (Yii::$app->request->get('confirm') == 'true'){
            $checkTreatment = (new Bill()) -> checkTreatmentPekeliling($bill_uid);
            $checkWard = (new Bill()) -> checkWardPekeliling($bill_uid);
            $checkFPP = (new Bill()) -> checkFppPekeliling($bill_uid);
            $checkDepartment = (new Bill()) -> checkDepartmentPekeliling($bill_uid);
            $checkStatus = (new Bill()) -> checkStatusPekeliling($bill_uid);

            if($checkTreatment && $checkWard && $checkFPP && $checkDepartment && $checkStatus){
                if(empty($model->bill_generation_datetime))
                {
                    $model->bill_generation_datetime =  $date->format('Y-m-d H:i:s');

                    if($modelWards->all()->count() > 0)
                    {
                        $final_ward_datetime = Ward::find()->select('ward_end_datetime')->where(['bill_uid' => $bill_uid])
                        ->orderBy('ward_end_datetime DESC')->limit(1)->one();
                        // $model->final_ward_datetime =  date($final_ward_datetime["ward_end_datetime"]);
                        if(!empty($final_ward_datetime["ward_end_datetime"])){
                            $model->final_ward_datetime =  date($final_ward_datetime["ward_end_datetime"]);
                        }
                        else{
                            $model->final_ward_datetime =  $date->format('Y-m-d H:i:s');
                        }
                    }
                    else
                        $model->final_ward_datetime =  $date->format('Y-m-d H:i:s');
                }

                $model->bill_generation_billable_sum_rm = Yii::$app->session->get('billable_sum');
                $model->bill_generation_final_fee_rm = Yii::$app->session->get('final_fee');

                if(!empty(Yii::$app->request->get('discharge'))){
                    if(Yii::$app->request->get('discharge')){
                        $checkFormat = DateTime::createFromFormat('Y-m-d H:i', Yii::$app->request->get('discharge'));

                        if($checkFormat){
                            $validDate = DateFormat::convert(Yii::$app->request->get('discharge'), 'datetime');
                            if($validDate){
                                $model->discharge_date = Yii::$app->request->get('discharge') . ':00';
                            }
                            else{
                                Yii::$app->session->setFlash('error_generate', '
                                    <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                    <strong>'.Yii::t('app', 'Invalid Datetime Format!').' <br/></strong> 
                                    '.Yii::t('app', 'Invalid Datetime Format of Discharge Date').'</div>'
                                );

                                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));
                            }
                        }
                        else{
                            Yii::$app->session->setFlash('error_generate', '
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <strong>'.Yii::t('app', 'Invalid Datetime Format!').' <br/></strong> 
                                '.Yii::t('app', 'Invalid Datetime Format of Discharge Date').'</div>'
                            );

                            return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                                'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));   
                        }
                    }
                }

                if (Yii::$app->session->has('billable_sum')) Yii::$app->session->remove('billable_sum');
                if (Yii::$app->session->has('final_fee')) Yii::$app->session->remove('final_fee');

                $model->bill_uid = Yii::$app->request->get('bill_uid');
                $model->generation_responsible_uid = Yii::$app->user->identity->getId();
                $model->save();

                return Yii::$app->getResponse()->redirect(array('/bill/print', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));    
            }
            else{
                if(!$checkTreatment){
                    $error_generate .= 'Treatment Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkWard){
                    $error_generate .= 'Ward Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkFPP){
                    $error_generate .= 'FPP Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkDepartment){
                    $error_generate .= 'Department Pekeliling Kod no longer exist'."<br/>";
                }

                if(!$checkStatus){
                    $error_generate .= 'Status Pekeliling Kod no longer exist'."<br/>";
                }

                    Yii::$app->session->setFlash('error_generate', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>'.Yii::t('app', 'Generate error! ').' <br/></strong> 
                    '.Yii::t('app', $error_generate).'</div>'
                );

                return Yii::$app->getResponse()->redirect(array('/bill/generate', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'billGeneration'));   
            }
        }
    }






    // print test 
    // public function actionPrint_only($bill_uid)
    // {
    //     $model = $this->findModel($bill_uid);

    //     $error = PrintForm::printBill($bill_uid);
    //     #would have thrown exception by this point if there was any issue
    //     if(!empty($error))
    //     {
    //         Yii::$app->session->setFlash('msg', '
    //         <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
    //     }
    //     return Yii::$app->getResponse()->redirect(array('/bill/print', 
    //     'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));         
    // }
    
      /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $bill_uid Bill Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
	 
    public function actionPrint($bill_uid)
    {        
        $session = Yii::$app->session;
        
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model = $this->findModel($bill_uid);
		// $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
  
		$modelWard = Ward::find()->where(['bill_uid' => $bill_uid])->orderby(['ward_start_datetime' => SORT_ASC])->all();   
        $modelreceipt = Receipt::find()->where(['rn'=> $model->rn, 'receipt_type' => 'deposit'])->all();
        $modelrefund = Receipt::find()->where(['rn'=> $model->rn, 'receipt_type' => 'refund'])->all();
        $modelTreatment = Treatment_details::find()->where(['bill_uid' => $bill_uid])->orderBy(Treatment_details::getNaturalSortArgs())->all();
        $modelFPP = Fpp::find()->where(['bill_uid' => $bill_uid])->all();
        $modelInpatient = Inpatient_treatment::findOne(['bill_uid' => $bill_uid]);
        
        if ($this->request->isPost && $model->load($this->request->post())) {
            if($model->validate() && $model->bill_print_id != "")
            {    
                // set bill printer session
                $data = Yii::$app->request->post();
                $choice = $data['Bill']['printer_choices'];
                $session->set('bill_printer_session', $choice);

				$error = PrintForm::printBill($bill_uid);
				#would have thrown exception by this point if there was any issue
                if(!empty($error))
                {
                    Yii::$app->session->setFlash('msg', '
                    <span class="badge badge-warning"><h6>'.$error.' !</h6></span> <br/><br/>');
                }
                          
                if($choice == ""){
                    Yii::$app->session->setFlash('error_print', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>'.Yii::t('app', 'Printer Choice Error!').' <br/></strong> 
                        '.Yii::t('app', "At least choice one printer to print").'</div>'
                    );
                }
                else{
                    if($choice == 'Printer 1')
                        $serial_no =  $this->actionGet_printer_1();
                    else if($choice == 'Printer 2')
						$serial_no =  $this->actionGet_printer_2();

                    if($model->bill_print_id != $serial_no)
                    {
                        if($choice == 'Printer 1')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill"]);
                        else if($choice == 'Printer 2')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill2"]);

                        $str = $model->bill_print_id;
                        $only_integer = preg_replace('/[^0-9]/', '', $str);
                        $model_serial->prepend = preg_replace('/[^a-zA-Z]/', '', $str);
                        $model_serial->digit_length = strlen($only_integer);
                        $model_serial->running_value = $only_integer;

                        $model_serial->save();    
                    }
                    else{
                        if($choice == 'Printer 1')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill"]);
                        else if($choice == 'Printer 2')
                            $model_serial = SerialNumber::findOne(['serial_name' => "bill2"]);
                        $model_serial->running_value =  $model_serial->running_value + 1;
                        $model_serial->save();    
                    }
                    
                    $model->bill_print_datetime =  $date->format('Y-m-d H:i:s');
                    $model->bill_uid = Yii::$app->request->get('bill_uid');
                    $model->bill_print_responsible_uid = Yii::$app->user->identity->getId();
                    $model->save();
                    return Yii::$app->getResponse()->redirect(array('/bill/print', 
                    'bill_uid' => $bill_uid, 'rn' => $model->rn, '#' => 'printing'));       
                }  
            }
            else
            {
                $message =  Yii::t('app','Bill Print ID should not be empty!');
                $model->addError('bill_print_id', $message);
                return $this->render('print', [
                    'model' => $model,
                    'modelWard' => $modelWard,
                    'modelTreatment' => $modelTreatment,
                    'print_empty' => true,
                    'model_cancellation' => new Cancellation(),
                    'modelFPP' => $modelFPP,
                    'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
                ]);
            }
        }
        else{
            if(!(new Bill()) -> isPrinted(Yii::$app->request->get('rn')))
            {
                if ($session->has('bill_printer_session')) {
                    $choice = $session->get('bill_printer_session');

                    if($choice == 'Printer 1')
                        $model->bill_print_id = $this->actionGet_printer_1();
                    else if($choice == 'Printer 2')
                        $model->bill_print_id = $this->actionGet_printer_2();
                }
            }
        }

        return $this->render('print', [
            'model' => $model,
            'modelWard' => $modelWard,
            'modelTreatment' => $modelTreatment,
            'model_cancellation' => new Cancellation(),
            'modelFPP' => $modelFPP,
            'modelInpatient' => (empty($modelInpatient))? new Inpatient_treatment() : $modelInpatient,
        ]);
    }
	
    /**
     * Deletes an existing Bill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $bill_uid Bill Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCancellation($bill_uid)
    {
        // $modelWard = Ward::findAll(['bill_uid' => $bill_uid]);
        // $modelTreatment = Treatment_details::findAll(['bill_uid' => $bill_uid]);

        // foreach ($modelWard as $modelWard) {
        //     $modelWard->delete();
        // }

        // foreach ($modelTreatment as $modelTreatment) {
        //     $modelTreatment->delete();
        // }

        // $this->findModel($bill_uid)->delete();

        $model = $this->findModel($bill_uid);
        $model->deleted = 1;
        // echo '<pre>';
        // var_dump($model);
        // echo '</pre>';
        // exit;
        // $model->validate();
        // var_dump($model->errors);
        // exit;
        $model->save();

        $model_cancellation = new Cancellation();
        if($this->request->isPost && $model_cancellation->load($this->request->post())){
            if(empty($model_cancellation->deleted_datetime))
            {
                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('+0800')); //GMT
                $model_cancellation->deleted_datetime =  $date->format('Y-m-d H:i:s');
            }

            $model_cancellation->cancellation_uid = $model->bill_uid;
            $model_cancellation->table = 'bill';
            $model_cancellation->responsible_uid = Yii::$app->user->identity->getId();

            if($model_cancellation->validate() && $model_cancellation->save()){
                return Yii::$app->getResponse()->redirect(array('/bill/create', 
                    'rn' => Yii::$app->request->get('rn'))); 
            }
        }
    }

    /**
     * Finds the Bill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $bill_uid Bill Uid
     * @return Bill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($bill_uid)
    {
        if (($model = Bill::findOne(['bill_uid' => $bill_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function findModel_Rn($rn)
    {
        if (($model = Bill::findOne(['rn' => $rn])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel_Ward($ward_uid)
    {
        if (($model = Ward::findOne(['ward_uid' => $ward_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel_Treatment($treatment_details_uid)
    {
        if (($model = Treatment_details::findOne(['treatment_details_uid' => $treatment_details_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel_FPP($kod)
    {
        if (($model = Fpp::findOne(['kod' => $kod])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function findModelByRn($rn)
    {
        if (($model = Bill::findOne(['rn' => $rn])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}