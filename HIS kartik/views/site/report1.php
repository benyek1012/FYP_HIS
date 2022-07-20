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
use app\models\Lookup_general;

$addon = <<< HTML
<span class="input-group-text">
    <i class="fas fa-calendar-alt"></i>
</span>
HTML;
// $rows_race = (new \yii\db\Query())
// ->select('*')
// ->from('lookup_general')
// ->where(['category'=> 'Race'])
// ->all();

// $race = array();
// foreach($rows_race as $row_race){
//     $race[$row_race['name']] = $row_race['name'];
// } 

// $rows_nationality = (new \yii\db\Query())
// ->select('*')
// ->from('lookup_general')
// ->where(['category'=> 'Nationality'])
// ->all();

// $countries = array();
// foreach($rows_nationality as $row_nationality){
//     $countries[$row_nationality['name']] = $row_nationality['name'];
// } 

$rows_race = Lookup_general::findAll(['category'=> 'Race']);
$race = array();
foreach($rows_race as $row_race){
    $race[$row_race['name']] = $row_race['name'];
} 

$rows_nationality = Lookup_general::findAll(['category'=> 'Nationality']);
$countries = array();
foreach($rows_nationality as $row_nationality){
    $countries[$row_nationality['name']] = $row_nationality['name'];
} 

$type = array('dately' => 'dately','weekly'=>'weekly','monthly'=>'monthly','quarterly'=>'quarterly','yearly'=>'yearly','race' => 'race','nationality'=>'nationality');

$form =   ActiveForm::begin([
    'action' => ['/site/report1'],
    'method' => 'get',
]); 
// echo '<div class="input-group drp-container">';
// 
// <br/>
// <?= $form->field($searchModel, 'date_from')->widget(DateRangePicker::className(), [
//       'name'=>'date_range_1',
//       'value'=>'01-Jan-14 to 20-Feb-14',
//       'startAttribute' => 'date_from',
//       'endAttribute' => 'date_to',
//         'convertFormat'=>true,
//         'useWithAddon'=>true,
//         'pluginOptions'=>[
//             'locale'=>[
//                 'format'=>'d-M-y',
//                 'separator'=>' to ',
//             ],
//             'opens'=>'left'
//         ]
//   ])->label('Please select the date range').$addon;
// echo '</div>';


echo '<div class="input-group drp-container">';?>
 <?= $form->field($searchModel, 'race')->dropDownList($race, 
    ['prompt'=> Yii::t('app','Please select race'),'maxlength' => true]); 
echo '</div>';

echo '<div class="input-group drp-container">';?>
 <?= $form->field($searchModel, 'nationality')->dropDownList($countries, 
    ['prompt'=> Yii::t('app','Please select nationality'),'maxlength' => true]); 
echo '</div>';

echo '<div class="input-group drp-container">';?>
 <?= $form->field($searchModel, 'report_type')->dropDownList($type, 
    ['prompt'=> Yii::t('app','Please select type'),'maxlength' => true]); 
echo '</div>';


// echo '<label class="control-label">Please select things to be filtered</label>';
// echo CheckboxX::widget(['name'=>'s_1', 'options'=>['id'=>'s_5'], ]); 
// echo '<label class="cbx-label" for="s_5">Male</label>';
// echo CheckboxX::widget(['name'=>'s_2', 'options'=>['id'=>'s_6'], ]); 
// echo '<label class="cbx-label" for="s_6">Female</label>';
// echo CheckboxX::widget(['name'=>'s_3', 'options'=>['id'=>'s_7'], ]); 
// echo '<label class="cbx-label" for="s_7">Race</label>';
// echo CheckboxX::widget(['name'=>'s_4', 'options'=>['id'=>'s_8'], ]); 
// echo '<label class="cbx-label" for="s_8">Nationality</label>';



?>

<div class="form-group text-center">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>


<?php ActiveForm::end(); ?>

<?php 
$report1->render();?>
