<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\widgets\google\DonutChart;
    use \koolreport\processes\Timeline;
    use yii\helpers\Html;
    use kartik\datetime\DateTimePicker;
    use kartik\daterange\DateRangePicker;
    use GpsLab\Component\Base64UID\Base64UID;
    use app\models\Patient_admission;
    use app\models\Bill;
    use app\models\BillSearch;
    use app\models\Ward;
    use yii\helpers\Url;
    use kartik\select2\Select2;
    use kartik\form\ActiveForm;
    use app\controllers\SiteController;
    use koolreport\widgets\google\LineChart;
    use koolreport\widgets\google\AreaChart;



?>


<div class="report-content">
    <div class="text-center">
        <h1>Billable Sum Report</h1>
        <p class="lead">Billable Sum Report in Charts</p>
    </div>
  
   
    <?php

 
    ColumnChart::create(array(
        "dataSource"=> (BillSearch::getReportData((new SiteController(null, null))->request->queryParams)),
        "columns"=>array(
            "bill_generation_datetime"=>array(
                
            ),
            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Total Billable Sum ",
                "type"=>"number",
                "prefix"=>"RM",
            )
        ),
        "width"=>"95%",
    ));
    Table::create(array(
       "dataSource"=> (BillSearch::getReportData((new SiteController(null, null))->request->queryParams)),
       "showFooter"=>"bottom",
        "columns"=>array(
            "bill_generation_datetime"=>array(
                "label"=>"Time",
                "type"=>"datetime",
                "format"=>"Y-n",
                "displayFormat"=>"F, Y",
                "footer"=>"count",
                "footerText"=>"Total count : @value",
            ),

            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Total Billable Sum ",
                "type"=>"number",
                "prefix"=>"RM",
                "footer"=>"avg",
                "footerText"=>"Average Sale: RM @value",
            ),
        ),
        "cssClass"=>array(
            "table"=>"table table-hover table-bordered"
        )
    ));


    // Table::create(array(
    //     "dataSource"=> (BillSearch::getReportData((new SiteController(null, null))->request->queryParams)),
    //     "showFooter"=>"bottom",
    //     "columns" => array(
    //         "bill_generation_datetime"=>array(
    //             "label"=>"Time",
    //             "type"=>"datetime",
    //             "format"=>"Y-n",
    //             "displayFormat"=>"F, Y",
    //         ),
    //         "bill_generation_billable_sum_rm"=>array(
    //             "label"=>"Total Billable Sum ",
    //             "prefix" => "RM",
    //             "footer" => "sum",
    //             "aggregates" => array(
    //                 "totalCount" => array("count", "bill_generation_billable_sum_rm"),                    
    //                 "avgSale" => array("avg", "bill_generation_billable_sum_rm"),
    //             ),
    //             "footerText" => "Sum: @value | Avg: @avgSale | Count: @totalCount",
    //         )
    //     ),
    //         "cssClass"=>array(
    //             "table"=>"table table-hover table-bordered"
    //         )
    // ));


    BarChart::create(array(
        "dataSource"=> (BillSearch::getReportData((new SiteController(null, null))->request->queryParams)),
        "columns"=>array(
            "bill_generation_datetime"=>array(
                "label"=>"Time",
                "type"=>"datetime",
                "format"=>"Y-n",
                "displayFormat"=>"F, Y",
            ),
            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Total Billable Sum ",
                "type"=>"number",
                "prefix"=>"RM",
            )
        ),
        "width"=>"95%"
    ));
    
    
    ?>

    <?php
    // Table::create(array(
    //     "dataStore"=>$this->dataStore('sale_by_year_over_five'),
    //     "columns"=>array(
    //         "bill_generation_datetime"=>array(
    //             "label"=>"Month",
    //             "type"=>"datetime",
    //             "format"=>"Y-n",
    //             "displayFormat"=>"F, Y",
    //         ),
    //         "bill_generation_billable_sum_rm"=>array(
    //             "label"=>"Total Billable Sum",
    //             "type"=>"number",
    //             "prefix"=>"RM",
    //                     )
    //     ),
    //     "cssClass"=>array(
    //         "table"=>"table table-hover table-bordered"
    //     )
    // ));

    DonutChart::create(array(
        "dataSource"=> (BillSearch::getReportData((new SiteController(null, null))->request->queryParams)),
        "columns"=>array(
            "bill_generation_datetime"=>array(
                "label"=>"Time",
                "type"=>"datetime",
                "format"=>"Y-n",
                "displayFormat"=>"F, Y",
            ),
            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Total Billable Sum ",
                "type"=>"number",
                "prefix"=>"RM",
            )
        ),
        "width"=>"100%"
    ));
    LineChart::create(array(
        // "dataStore"=>$this->dataStore('sale_by_year'),
        "dataSource"=> (BillSearch::getReportData((new SiteController(null, null))->request->queryParams)),
 
         "columns"=>array(
             "bill_generation_datetime"=>array(
                "label"=>"Time",
                 "type"=>"datetime",
                 "format"=>"Y-n",
                 "displayFormat"=>"F, Y",
             ),
             "bill_generation_billable_sum_rm"=>array(
                "label"=>"Total Billable Sum ",
                 "type"=>"number",
                 "prefix"=>"RM",
                         )
         ),
         "cssClass"=>array(
             "table"=>"table table-hover table-bordered"
         )
     ));
    ?>
</div>