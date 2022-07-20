<?php

namespace app\reports;

use Yii;
use \koolreport\KoolReport;
use \koolreport\processes\Filter;
use \koolreport\processes\TimeBucket;
use \koolreport\processes\Group;
use \koolreport\processes\Limit;
use \koolreport\processes\Timeline;
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use GpsLab\Component\Base64UID\Base64UID;
use app\models\Patient_admission;
use app\models\Bill;
use app\models\Ward;
use yii\helpers\Url;
use app\models\BillSearch;






class MyReport1 extends \koolreport\KoolReport
{

    public function settings()
    {
        return array(
            "dataSources"=>array(
                "yearly_bill"=>array(
                    "connectionString"=>"mysql:host=localhost;dbname=dbhis",
                    "username"=>"root",
                    "password"=>"",
                    "charset"=>"utf8"
               )              
            ),
            "assets"=>array(
                "path"=>"../web/assets",
                "url"=>"assets"
            )
        );
    }   
    protected function setup()
    {
        $this->src('yearly_bill')
        ->query("SELECT bill_generation_datetime,bill_generation_final_fee_rm,bill_generation_billable_sum_rm FROM bill")
        // ->pipe(new TimeBucket(array(
        //     "bill_generation_datetime"=>"dayofmonth"
        // )))
        ->pipe(new TimeBucket(array(
            "bill_generation_datetime"=>"year"
        )))
        ->pipe(new Filter(array(
            array("bill_generation_billable_sum_rm",">=",5500)
        )))
        ->pipe(new Group(array(
            "by"=>"bill_generation_datetime",
            "sum"=>"bill_generation_billable_sum_rm"
        )))
        ->pipe($this->dataStore('sale_by_year_over_five'));

        // $this->src('yearly_bill')
        // ->query("SELECT bill_generation_datetime,bill_generation_final_fee_rm,bill_generation_billable_sum_rm FROM bill")
        // // ->pipe(new TimeBucket(array(
        // //     "bill_generation_datetime"=>"dayofmonth"
        // // )))
        // ->pipe(new TimeBucket(array(
        //     "bill_generation_datetime"=>"year"
        // )))
        // ->pipe(new Group(array(
        //     "by"=>"bill_generation_datetime",
        //     "sum"=>"bill_generation_billable_sum_rm"
        // )))
        // ->pipe($this->dataStore('sale_by_all'));
    } 

}