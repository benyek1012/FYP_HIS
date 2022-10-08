<?php 
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Patient_information;
use app\controllers\Patient_informationController;
use app\models\Patient_next_of_kin;
use Exception;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Report;

class ReportController extends Controller
{

    public function actionReport()
    {
        $report = new \app\reports\MyReport;
        $report->run();
        return $this->render('report',array(
            "report"=>$report
        ));
        
    }

    public function actionReport1()
    {
        $reports = new \app\reports\MyReport1;
        $reports->run();
        return $this->render('reports',array(
            "reports"=>$reports
        ));
    }
    // public function actionReport()
    // {
    //     $report = new Report;
      
    //     return $this->render('report',array(
    //         "report"=>$report
    //     ));
        
    // } 

}



    ?>