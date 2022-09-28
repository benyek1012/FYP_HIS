<?php

use app\controllers\SiteController;
use app\models\Cancellation;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use yii\data\ActiveDataProvider;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Deleted');
$this->params['breadcrumbs'][] = $this->title;
?>

<body>
    <div id="card1" class="container-fluid">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Bill Cancellation');?></h3>
                <div class="d-flex justify-content-end">
                    <div class="card-tools">
                        <!-- Collapse Button -->
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
        <?php 
            if(!empty($model_bill))
            {
                $dataProvider1 = new ActiveDataProvider([
                    'query'=> Cancellation::find()->where(['table' => 'bill'])
                    // ->orderBy(['entry_datetime' => SORT_DESC])
                    // 'pagination'=>['pageSize'=>5],
                ]);

                echo $this->render('/cancellation/deleted_bill', ['dataProvider'=>$dataProvider1]);
        ?>
        <?php
            } 
            else echo Yii::t('app','Bill cancellation record is not found');
        ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <div class="card">
            <div class="card-header text-white bg-primary">
                <h3 class="card-title"><?php echo Yii::t('app','Receipt Cancellation');?></h3>
                <div class="card-tools">
                    <!-- Collapse Button -->
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                            class="fas fa-minus"></i></button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <!-- This is the form that shows patient information which can directly updating-->
        <?php
            if(!empty($model_receipt))
            {
                $dataProvider1 = new ActiveDataProvider([
                    'query'=> Cancellation::find()->where(['table' => 'receipt'])
                    // ->orderBy(['entry_datetime' => SORT_DESC])
                    // 'pagination'=>['pageSize'=>5],
                ]);

                echo $this->render('/cancellation/deleted_receipt', ['dataProvider'=>$dataProvider1]);
        ?>
        <?php
            } 
            else echo Yii::t('app','Receipt cancellation record is not found');
        ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</body>