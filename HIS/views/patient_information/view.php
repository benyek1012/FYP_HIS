<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Patient_next_of_kin;

/* @var $this yii\web\View */
/* @var $model app\models\Patient_information */

if(empty($model))
{
    $this->title = "empty";
}
else $this->title = $model->name;
//$this->params['breadcrumbs'][] = ['label' => 'Patient Informations', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
