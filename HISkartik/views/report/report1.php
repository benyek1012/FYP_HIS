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

$this->title = 'Report Senarai Baki Pendeposit';
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

        <div class="row">
            <div class="col-sm-12 required">
                <?= $form->field($model, 'date_report')->widget(DatePicker::classname(),[
                'pluginOptions' => ['autoclose' => true,'format' => 'yyyy-mm-dd'],
                'pluginEvents' => [],]);?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Export CSV'), ['class' => 'btn btn-success', 'name' => 'csv', 'value' => 'csv']) ?>
            <?= Html::submitButton(Yii::t('app','Export PDF'), ['class' => 'btn btn-info', 'name' => 'pdf', 'value' => 'pdf']) ?>
        </div>

        <?php kartik\form\ActiveForm::end(); ?>
    </div>
</div>