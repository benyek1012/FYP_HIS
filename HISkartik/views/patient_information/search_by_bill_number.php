<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_admissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Patient Information By Bill Number');
$this->params['breadcrumbs'][] = $this->title;
?>
<div>

    <?= Html::beginForm(); ?>

		<?= Html::input('text', 'bill_print_id', empty($bill_print_id)?'':$bill_print_id) ?>
				
		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
		</div>

    <?= Html::endForm(); ?>


	<?php
		if(!empty($result))
			echo 'RN: '.$result;
	
	
	?>
</div>