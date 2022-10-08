<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_fppSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lookup-fpp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'kod') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'min_cost_per_unit') ?>

    <?= $form->field($model, 'max_cost_per_unit') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
