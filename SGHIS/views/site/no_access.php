<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = Yii::t('app','Sorry, you do not have permission to access this page');
?>
<div>
    <p><?php echo Yii::t('app', 'Please contact your administrator for assistance'); ?></p>
</div>