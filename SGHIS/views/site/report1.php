<h1>Hello World</h1>
<h2>this is for admission sum </h2>

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
    use yii\widgets\ActiveForm;
    use kartik\checkbox\CheckboxX;
    use app\models\BillSearch;

$addon = <<< HTML
<span class="input-group-text">
    <i class="fas fa-calendar-alt"></i>
</span>
HTML;
$form =   ActiveForm::begin([
    'action' => ['/site/report1'],
    'method' => 'get',
]); 
echo '<div class="input-group drp-container">';
?>
<br/>
<?= $form->field($searchModel, 'date_from')->widget(DateRangePicker::className(), [
      'name'=>'kvdate3',
      'startAttribute' => 'date_from',
      'endAttribute' => 'date_to',
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
  ])->label('Please select the date range').$addon;
echo '</div>';
   echo $form->field($searchModel, 'rn');
echo '<label class="control-label">Please select date type</br></label>';
echo Select2::widget([
    'name' => 'kv-type-01',
    'data' => [1 => "houroftheday", 2 => "date", 3 => "week", 4 => "month", 5 => "quarter"],
    'options' => [
        'placeholder' => 'Select a type ...',
        'options' => [
            3 => ['disabled' => false],
            4 => ['disabled' => false],
        ]
    ],
]);
echo '<label class="control-label">Please select things to be filtered</label>';
echo CheckboxX::widget(['name'=>'s_1', 'options'=>['id'=>'s_5'], ]); 
echo '<label class="cbx-label" for="s_5">Male</label>';
echo CheckboxX::widget(['name'=>'s_2', 'options'=>['id'=>'s_6'], ]); 
echo '<label class="cbx-label" for="s_6">Female</label>';
echo CheckboxX::widget(['name'=>'s_3', 'options'=>['id'=>'s_7'], ]); 
echo '<label class="cbx-label" for="s_7">Race</label>';
echo CheckboxX::widget(['name'=>'s_4', 'options'=>['id'=>'s_8'], ]); 
echo '<label class="cbx-label" for="s_8">Nationality</label>';



?>

<div class="form-group text-center">
                    <button class="btn btn-success"><i class="glyphicon glyphicon-refresh"></i> Load</button>
</div>

<?php ActiveForm::end(); ?>

<?php 
$report1->render();?>