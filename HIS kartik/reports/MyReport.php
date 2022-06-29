<?php

namespace app\reports;

use \koolreport\KoolReport;
use \koolreport\processes\Filter;
use \koolreport\processes\TimeBucket;
use \koolreport\processes\Group;
use \koolreport\processes\Limit;
use \koolreport\Inputs\DateRangePicker;

class MyReport extends \koolreport\KoolReport
{
    public function settings()
    {
        return array(
            "dataSources"=>array(
                "monthly_bill"=>array(
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


         $this->src('monthly_bill')
        ->query("SELECT bill_generation_datetime,bill_generation_final_fee_rm,bill_generation_billable_sum_rm FROM bill")
        // ->pipe(new TimeBucket(array(
        //     "bill_generation_datetime"=>"dayofmonth"
        // )))
        ->pipe(new TimeBucket(array(
            "bill_generation_datetime"=>"month"
        )))
        ->pipe(new Filter(array(
            array("bill_generation_billable_sum_rm","<=",2000)
        )))
        ->pipe(new Group(array(
            "by"=>"bill_generation_datetime",
            "sum"=>"bill_generation_billable_sum_rm"
        )))
        ->pipe($this->dataStore('sale_by_month'));
    
        $this->src('monthly_bill')
        ->query("SELECT bill_generation_datetime,bill_generation_final_fee_rm,bill_generation_billable_sum_rm FROM bill")
        // ->pipe(new TimeBucket(array(
        //     "bill_generation_datetime"=>"dayofmonth"
        // )))
        ->pipe(new TimeBucket(array(
            "bill_generation_datetime"=>"month"
        )))
        ->pipe(new Filter(array(
            array("bill_generation_billable_sum_rm",">=",2000)
        )))
        ->pipe(new Group(array(
            "by"=>"bill_generation_datetime",
            "sum"=>"bill_generation_billable_sum_rm"
        )))
        ->pipe($this->dataStore('sale_by_day'));
    } 

}