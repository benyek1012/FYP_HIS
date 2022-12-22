<?php

use app\controllers\BillController;
use app\controllers\SiteController;
use app\models\Patient_admission;
use app\models\Patient_information;
use app\models\Patient_next_of_kin;
use yii\data\ActiveDataProvider;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use app\models\Bill;
use app\controllers\Patient_informationController;
use app\controllers\ReceiptController;

$this->title = Yii::t('app','Biil Forgive');
//get rn array where bill_forgive_date = NULL
$rn_array = array();
$rn = array();
$time = new DateTime('now');
$check_time = $time->modify('-7 year')->format('Y-m-d');
$rows = (new \yii\db\Query())
->select('rn')->where(['bill_forgive_date'=>NULL])->from('Bill')
->andWhere(['<','DATE(bill_generation_datetime)',$check_time])
->all();
foreach($rows as $row){
    $rn_array = $row['rn'];
    //var_dump($rn_array);
    $amountDue = (new Bill()) -> getAmtDued($rn_array);
    //var_dump($amountDue);
    $unclaimedBalanced = (new Bill()) -> getUnclaimed($rn_array);
    //var_dump($unclaimedBalanced);
    if($amountDue == 0 && $unclaimedBalanced == 0){
        array_push($rn, $row['rn']);
    }
}  
$model2 = Bill::find()->where(['in', 'rn', $rn]);
?>


<body>
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                aria-controls="nav-home" aria-selected="true">Tab 1</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
                aria-controls="nav-profile" aria-selected="false">Tab 2</a>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <br /><br />
            <div id="card1" class="container-fluid">
                <div class="card">
                    <div class="card-header text-white bg-primary">
                        <h3 class="card-title"><?php echo Yii::t('app','Bill Forgived');?></h3>
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
            if(!empty($model))
            {
                $dataProvider = new ActiveDataProvider([
                    'query'=> Bill::find()->where(['not',['bill_forgive_date'=>NULL]]),
                    // ->joinWith('bill',true)
                    'pagination'=>['pageSize'=>5],
                ]);
                $form = kartik\form\ActiveForm::begin([
                    'id' => 'patient-admission-form',
                    'type' => 'vertical',
                    'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                    ],
                    ]); 
                ?>
                        <div class="bill-view">

                            <?= kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [ 'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'attribute' =>  'rn',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                        ], 
                        [
                            'attribute' =>  'bill_generation_datetime',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                        ],
                        [
                            'attribute' =>  'bill_forgive_date',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                        ]
                    ]
                ]);
                ?>
                        </div>
                        <?php kartik\form\ActiveForm::end(); ?>
                        <?php
            } 
            else echo Yii::t('app','Bill record is not found');
        ?>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>

        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <br /><br />
            <div id="card1" class="container-fluid">
                <div class="card">
                    <div class="card-header text-white bg-primary">
                        <h3 class="card-title"><?php echo Yii::t('app','Bill');?></h3>
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
            if(!empty($model2))
            {       
                $dataProvider = new ActiveDataProvider([
                    'query'=> Patient_admission::find()->select('patient_admission.*, patient_information.*, bill.*')->from('patient_admission')->joinWith('bill',true)->joinWith('receipt',true)->joinWith('patient_information',true)->where(['in', 'bill.rn', $rn]),
                    // ->joinWith('bill',true)
                  //  'pagination'=>['pageSize'=> 20],
                ]);
                $form = kartik\form\ActiveForm::begin([
                    'id' => 'patient-admission-form',
                    'type' => 'vertical',
                    'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                    ],
                    ]); 
                ?>
                        <div class="bill-view">

                            <?= kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [ 'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' =>
                            function($model) {
                                return ['value' => $model->rn, 'class' => 'checkbox-row', 'id' => 'checkbox'];
                        }
                        ],
                        [
                            'attribute' =>  'name',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                            'value' => function($data){
                                return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->name;
                            },
                        ], 
                        [
                            'attribute' =>  'nric',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                            'value' => function($data){
                                return  ((new Patient_informationController(null,null)) -> findModel($data->patient_uid))->nric;
                            },
                        ], 
                        [
                            'attribute' =>  'rn',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                        ], 
                        [
                            'attribute' =>  'bill_generation_final_fee_rm',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                            'value' => function($data){
                                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_generation_final_fee_rm;
                            },
                        ],
                        [
                            'attribute' =>  'class',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                            'value' => function($data){
                                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->class;
                            },
                        ],
                        [
                            'attribute' =>  'initial_ward_code',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                           
                        ],
                        [
                            'attribute' =>  'receipt_content_description',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                            'value' => function($data){
                                return  ((new ReceiptController(null,null)) -> findModelByRn($data->rn))->receipt_content_description;
                            },
                        ],
                        [
                            'attribute' =>  'bill_print_id',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                            'value' => function($data){
                                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_print_id;
                            },
                        ],
                        [
                            'attribute' =>  'bill_print_datetime',
                            'headerOptions'=>['style'=>'max-width: 100px;'],
                            'contentOptions'=>['style'=>'max-width: 100px;vertical-align:middle'],
                            'value' => function($data){
                                return  ((new BillController(null,null)) -> findModelByRn($data->rn))->bill_print_datetime;
                            },
                        ],
                    ]
                ]);
                ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= $form->field($model_forgive, 'comment')->textInput(['autocomplete' =>'off', 'maxlength' => true]) ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= Html::submitButton(Yii::t('app','Forgive'), ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                        <?php kartik\form\ActiveForm::end(); ?>
                        <?php
            } 
            else echo Yii::t('app','Bill record is not found');
        ?>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
</body>