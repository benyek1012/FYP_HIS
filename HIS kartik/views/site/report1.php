
<h2>Patient Admission Report </h2>

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
use kartik\checkbox\CheckboxX;
use kartik\popover\PopoverX;
use app\models\Patient_information;
use app\models\Lookup_general;
use yii\helpers\ArrayHelper;

// $datalist = Patient_information::findAll();
//  $datalist = Lookup_general::find()->andWhere(['code' => 'Sex'])->all();
// $data = ArrayHelper::map($datalist, 'code', 'Sex');

// $data1 = implode($data);

// echo $data1;



$form = ActiveForm::begin();

echo '<div class="row">';
  echo  '<div class="col-sm-6">';
echo $form->field($searchModel, 'first_reg_date', [
    'addon'=>['prepend'=>['content'=>'<i class="fas fa-calendar-alt"></i>']],
    'options'=>['class'=>'drp-container mb-2']
])->widget(DateRangePicker::classname(), [
    'useWithAddon'=>true
]);

$addon = <<< HTML
<span class="input-group-text">
    <i class="fas fa-calendar-alt"></i>
</span>
HTML;



echo '</div>';



  echo  '<div class="col-sm-6">';
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
echo '</div>';
echo '</div>';
// echo '<div class="input-group drp-container">';
// echo '<label class="control-label">Please select things to be filtered</label>';
// echo CheckboxX::widget(['name'=>'s_1', 'options'=>['id'=>'s_5'], ]); 
// echo '<label class="cbx-label" for="s_5">Male</label>';
// echo CheckboxX::widget(['name'=>'s_2', 'options'=>['id'=>'s_6'], ]); 
// echo '<label class="cbx-label" for="s_6">Female</label>';
// echo CheckboxX::widget(['name'=>'s_3', 'options'=>['id'=>'s_7'], ]); 
// echo '<label class="cbx-label" for="s_7">Race</label>';
// echo CheckboxX::widget(['name'=>'s_4', 'options'=>['id'=>'s_8'], ]); 
// echo '<label class="cbx-label" for="s_8">Nationality</label>';
// echo '</div>';

$rows_sex = (new \yii\db\Query())
->select('*')
->from('lookup_general')
->where(['category' => 'Sex'])
->all();

$sex = array();
foreach($rows_sex as $rows_sex){
    $sex[$rows_sex['code']] = $rows_sex['name'];
}
echo '<div class="row">';
  echo  '<div class="col-sm-4">';
// echo Select2::widget([
    echo $form->field($searchModel, 'sex')->widget(Select2::classname(),[
    'name' => 'state_10',
    'data' => $sex,
    'options' => [
        'placeholder' => 'Select Gender ...',
        'multiple' => true
        
        
    
    ],
]);

echo '</div>';
$rows_race = (new \yii\db\Query())
->select('*')
->from('lookup_general')
->where(['category' => 'Race'])
->all();

$race = array();
foreach($rows_race as $rows_race){
    $race[$rows_race['code']] =  $rows_race['name'];
}

echo  '<div class="col-sm-4">';

echo $form->field($searchModel, 'race')->widget(Select2::classname(),[
    'name' => 'state_10',
    'data' => $race,
    'options' => [
        'placeholder' => 'Select Race ...',
        'multiple' => true
    ],
]);
echo '</div>';

$rows_nationality = (new \yii\db\Query())
->select('*')
->from('lookup_general')
->where(['category' => 'Nationality'])
->all();

$country = array();
foreach($rows_nationality as $rows_nationality){
    $country[$rows_nationality['code']] =  $rows_nationality['name'];
}



echo  '<div class="col-sm-4">';

echo $form->field($searchModel, 'nationality')->widget(Select2::classname(),[
    'name' => 'state_10',
    'data' => $country,
    'options' => [
        'placeholder' => 'Select Nationality ...',
        'multiple' => true,
        'allowClear' => true
    ],
]);
echo '</div>';
// echo  '<div class="col-sm-3">';
// echo '<label class="control-label">Religion</label>';
// echo Select2::widget([
//     'name' => 'state_10',
//     'data' => [1 => "Buddha", 2 => "Christian", 3 => "Islam", 4 => "Hindu", 5 => "lain-lain"],
//     'options' => [
//         'placeholder' => 'Select Religion ...',
//         'multiple' => true,
//         1 => ['disabled' => true],
//         2 => ['disabled' => true],
//         3 => ['disabled' => true],
//         4 => ['disabled' => true],
//         5 => ['disabled' => true],

//     ],
// ]);
// echo '</div>';
echo '</div>';


ActiveForm::end();

?>

<div class="form-group">
        <?= Html::submitButton('submit', ['class' => 'btn btn-primary']) ?>
    </div>




<?php $report1->render();?>
