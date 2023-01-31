<?php
namespace app\components;
use Yii;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Variable;


class Utils{
	const bill_page_mode_new = 0;
	const bill_page_mode_created = 1;
	const bill_page_mode_generated = 2;
	const bill_page_mode_printed = 3;
	
	const permission_clerk = 0;
	const permission_cashier = 1;
	const permission_admin = 2;
	
	static function getEditeable(){
		return Yii::$app->session['editeable'];
	}
	static function checkAndSetEditeable($forceUnediteable = false){
		
		$model_read_only = Variable::find()->one();

		if($model_read_only->read_only == 1){ //system in read only model_read_only{
			echo "<h2>This page is currently read-only because system is performing automatic database backup</h2>";
			return Yii::$app->session['editeable']=false;
		}
		//echo $bill_uid . ' ' . empty($bill_model->deleted) . '    ' . $bill_model->deleted;
		if($forceUnediteable){
			echo "<h2>Not allowed to edit</h2>";
			return Yii::$app->session['editeable'] = false;
		}
		
		return Yii::$app->session['editeable'] = true;
		
	}
	
	static function getBillPageMode(){
		
		return Yii::$app->session['bill_page_mode'];
	}
	
	static function setBillPageMode($value){
		Yii::$app->session['bill_page_mode'] = $value;
	}
	
	static function generateUID(){return Base64UID::generate(32);}

	static function permissionCheck($access){
		if(Yii::$app->user->identity->role_cashier && in_array(Utils::permission_cashier,$access))
			return true;
		
		if(Yii::$app->user->identity->role_clerk && in_array(Utils::permission_clerk,$access))
			return true;
		
		if(Yii::$app->user->identity->role_admin && in_array(Utils::permission_admin,$access))
			return true;
	
		echo 'No Permission';
		exit;
	
	}


}



?>