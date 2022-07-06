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
use app\models\Ward;
use yii\helpers\Url;
use kartik\select2\Select2;
use kartik\form\ActiveForm;







$addon = <<< HTML
<span class="input-group-text">
    <i class="fas fa-calendar-alt"></i>
</span>
HTML;


echo '<label class="control-label">Date Range</label>';
echo '<div class="input-group drp-container">';
echo DateRangePicker::widget([
    'name'=>'date_range_1',
    'value'=>'01-Jan-14 to 20-Feb-14',
    'convertFormat'=>true,
    'useWithAddon'=>true,
    'pluginOptions'=>[
        'locale'=>[
            'format'=>'d-M-y',
            'separator'=>' to ',
        ],
        'opens'=>'left'
    ]
]) . $addon;
echo '</div>';

echo Select2::widget([
    'name' => 'kv-type-01',
    'data' => [1 => "RN", 2 => "Bill", 3 => "Third", 4 => "Fourth", 5 => "Fifth"],
    'options' => [
        'placeholder' => 'Select a type ...',
        'options' => [
            3 => ['disabled' => false],
            4 => ['disabled' => false],
        ]
    ],
]);




?>

<h1>Hello World</h1>
<h2>this is for billable sum </h2>



<?php $report->render();?>

