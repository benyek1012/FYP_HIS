<?php

namespace app\controllers;

use app\models\Bill;
use app\models\Reminder;
use app\models\New_user;
use app\models\ReminderSearch;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Pekeliling_import;
use app\models\Pekeliling_importSearch;
use app\models\Receipt;
use app\models\Reminder_pdf;
use app\models\Report;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use Yii;
use yii\data\ArrayDataProvider;


class ReportController extends Controller
{
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
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionReport1()
    {
        $model = new Report();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $senarai_pada = $model->date_report;
                    if(!empty($senarai_pada))
                    {
                        Report::export_csv_report1($senarai_pada);
                    }
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        } 

        return $this->render('report1', [
            'model' => $model,
        ]);
    }

    public function actionReport5()
    {
        $model = new Report();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $year = $model->year;
                    $month = $model->month;
                    Report::export_csv_report5($year, $month);
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        } 

        return $this->render('report5', [
            'model' => $model,
        ]);
    }

    public function actionReport7()
    {
        $model = new Report();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $year = $model->year;
                    $month = $model->month;
                    Report::export_csv_report7($year, $month);
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        }

        return $this->render('report7', [
            'model' => $model,
        ]);
    }
    
    public function actionReport8()
    {
        $model = new Report();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($this->request->isPost && isset($_POST['csv'])){
                    $year = $model->year;
                    $month = $model->month;
                    Report::export_csv_report8($year, $month);
                }

                if($this->request->isPost && isset($_POST['pdf'])){
                    
                }
            }
        }

        return $this->render('report8', [
            'model' => $model,
        ]);
    }
}