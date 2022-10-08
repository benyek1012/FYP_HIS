<?php

use yii\bootstrap4\Html;
use GpsLab\Component\Base64UID\Base64UID;

/* @var $this yii\web\View */
/* @var $model app\models\Lookup_general */
/* @var $form yii\widgets\ActiveForm */

$rows_cat = (new \yii\db\Query())
->select('category')
->from('lookup_general')
->groupBy('category')
->all();

$category = array();
foreach($rows_cat as $rows_cat){
    $category[$rows_cat['category']] = $rows_cat['category'];  
} 

?>

<div class="lookup-general-form" id="LOK_div" style="display:none;">

<?php $form = kartik\form\ActiveForm::begin([
        'action' => ['lookup_general/index','lookup_general_uid'=>$model['lookup_general_uid']],
        'id' => 'lookup-general-form',
        'type' => 'vertical',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
        
    ]); 
    $lookup_general_uid = Base64UID::generate(32);
    ?>
    
    <div class ="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'code')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'category')->widget(kartik\select2\Select2::classname(), [
                    'data' => $category,
                    'options' => ['placeholder' => Yii::t('app','Please select category'), 'id' => 'race'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ]])?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'long_description')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
        </div> 
        <div class="col-sm-6">
            <?= $form->field($model, 'recommend')->textInput(['autocomplete' =>'off', 'value' => '1']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'lookup_general_uid')->hiddenInput(['readonly' => true, 'maxlength' => true, 'value' => $lookup_general_uid])->label(false)?>
        </div>
    </div>
    

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php kartik\form\ActiveForm::end(); ?>

</div>
