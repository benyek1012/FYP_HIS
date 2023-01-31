<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\reports\Report;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_admissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Patient Information By Bill Number');
$this->params['breadcrumbs'][] = $this->title;
?>
<div>

    <?= Html::beginForm(); ?>
		<?= Html::dropDownList('list', $list_value, Report::reportList)?>
		<?= Html::input('text', 'start_date', empty($start_date)?'':$start_date) ?>
		<?= Html::input('text', 'end_date', empty($end_date)?'':$end_date) ?>
				
		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'pdf'), ['class' => 'btn btn-primary', 'name'=>'submit', 'value'=>'pdf']) ?>
			<?= Html::submitButton(Yii::t('app', 'csv'), ['class' => 'btn btn-primary', 'name'=>'submit','value'=>'csv']) ?>
		</div>

    <?= Html::endForm(); ?>


	<?php
		if(!empty($result))
			echo 'Error: '.$result;
	
	
	?>
</div>