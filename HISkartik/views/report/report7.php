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

$this->title = 'Report Serahan Wang Kutipan(Bulanan)';
$this->params['breadcrumbs'][] = $this->title;
$year = array();
for($i=1960; $i<=2022; $i++)
{
    $year[$i] = $i;
}
$month = array();
for($i=1; $i<=12; $i++)
{
    $month[$i] = $i;
}
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
            <div class="col-sm-6">
                <?= $form->field($model, 'year')->widget(kartik\select2\Select2::classname(), [
                    'data' => $year,
                    'options' => ['placeholder' => Yii::t('app','Please select year'), 'id' => 'year'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ]]);
            ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'month')->widget(kartik\select2\Select2::classname(), [
                    'data' => $month,
                    'options' => ['placeholder' => Yii::t('app','Please select month'), 'id' => 'month'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ]]);
            ?>

            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Export CSV'), ['class' => 'btn btn-success', 'name' => 'csv', 'value' => 'csv']) ?>
            <?= Html::submitButton(Yii::t('app','Export PDF'), ['class' => 'btn btn-info', 'name' => 'pdf', 'value' => 'pdf']) ?>
        </div>

        <!-- If the flash message existed, show it  -->
        <?php if(Yii::$app->session->hasFlash('msg')):?>
        <div id="flashError">
            <?= Yii::$app->session->getFlash('msg') ?>
        </div>
        <?php endif; ?>

        <?php kartik\form\ActiveForm::end(); ?>
    </div>
</div>