<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Lookup_generalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lookup Generals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-general-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lookup General', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lookup_general_uid',
            'code',
            'category',
            'name',
            'long_description',
            //'recommend',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Lookup_general $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lookup_general_uid' => $model->lookup_general_uid]);
                 }
            ],
        ],
    ]); ?>


</div>
