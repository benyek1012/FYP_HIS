<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\Pekeliling_import;
use yii\helpers\StringHelper;
use app\models\New_user;
use app\models\Report;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Pekeliling_importSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Adjust Printing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pekeliling-import-index">

    <div id="lookup_form">
        <?php $form = kartik\form\ActiveForm::begin([
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ]]); ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Adjust Printer'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php kartik\form\ActiveForm::end(); ?>
    </div>
</div>