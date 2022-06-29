<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\widgets\google\DonutChart;
    use \koolreport\processes\Timeline;
?>

<div class="report-content">
    <div class="text-center">
        <h1>MySQL Report</h1>
        <p class="lead">This report show how to build report from MySQL data</p>
    </div>

    <?php
    ColumnChart::create(array(
        "dataStore"=>$this->dataStore('sale_by_month'),  
        "columns"=>array(
            "bill_generation_datetime"=>array(
                "label"=>"Month",
                "type"=>"datetime",
                "format"=>"Y-n",
                "displayFormat"=>"F, Y",
            ),
            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Amount",
                "type"=>"number",
                "prefix"=>"RM",
            )
        ),
        "width"=>"100%",
    ));
    BarChart::create(array(
        "dataStore"=>$this->dataStore('sale_by_day'),  
        "columns"=>array(
            "bill_generation_datetime"=>array(
                "label"=>"Month",
                "type"=>"datetime",
                "format"=>"Y-n",
                "displayFormat"=>"F, Y",
            ),
            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Amount",
                "type"=>"number",
                "prefix"=>"RM",
            )
        ),
        "width"=>"100%"
    ));
    DonutChart::create(array(
        "dataStore"=>$this->dataStore('sale_by_day'),  
        "columns"=>array(
            "bill_generation_datetime"=>array(
                "label"=>"Month",
                "type"=>"datetime",
                "format"=>"Y-n",
                "displayFormat"=>"F, Y",
            ),
            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Amount",
                "type"=>"number",
                "prefix"=>"RM",
            )
        ),
        "width"=>"100%"
    ));
    
    ?>

    <?php
    Table::create(array(
        "dataStore"=>$this->dataStore('sale_by_month'),
        "columns"=>array(
            "bill_generation_datetime"=>array(
                "label"=>"Month",
                "type"=>"datetime",
                "format"=>"Y-n",
                "displayFormat"=>"F, Y",
            ),
            "bill_generation_billable_sum_rm"=>array(
                "label"=>"Amount",
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