<?php

use app\models\Bill;
use app\models\Lookup_general;
use app\models\Patient_admission;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Patient_information;
use app\models\Receipt;

if (Yii::$app->user->isGuest){  ?>

<style type="text/css">
#sidebarx {
    display: none;
}
</style>

<?php
}

// Load the patient info and particular RN info
function getInfo()
{
    $info = null;
    if(!empty(Yii::$app->request->get('id'))){
        $info = Patient_information::findOne(['patient_uid' => Yii::$app->request->get('id')]);
    }
    else if(!empty(Yii::$app->request->get('rn')))
    {
        $info = Patient_admission::findOne(['rn' => Yii::$app->request->get('rn')]);
        $info = Patient_information::findOne(['patient_uid' => $info->patient_uid]);
    }
    else if(!empty(Yii::$app->request->get('receipt_uid')))
    {
        $info = Receipt::findOne(['receipt_uid'=> Yii::$app->request->get('receipt_uid')]);
        $info = Patient_admission::findOne(['rn'=> $info->rn]);
        $info = Patient_information::findOne(['patient_uid'=> $info->patient_uid]);
    }
    else if(!empty(Yii::$app->request->get('bill_uid')))
    {
        $info = Bill::findOne(['bill_uid'=> Yii::$app->request->get('bill_uid')]);
        $info = Patient_admission::findOne(['rn'=> $info->rn]);
        $info = Patient_information::findOne(['patient_uid'=> $info->patient_uid]);
    }
    else{
        $info = null;
    }
    return $info;
}

// return all RN from particular patient 
function items()
{
    $info = getInfo();

    $rows = (new \yii\db\Query())
    ->select(['rn'])
    ->from('patient_admission')
    ->where(['patient_uid' => $info->patient_uid])
    ->orderBy(['entry_datetime' => SORT_DESC, 'rn' => SORT_DESC])
    ->all();

    $items = [];

    foreach ($rows as $row) {
        array_push($items, ['label' => '' .  $row['rn'] .'','iconClass' => '', 'url' => ['patient_admission/update', 'rn' =>  $row['rn']]]);
    }
    // array_push($items,['label' => Yii::t('app','Print Transaction Records'), 'iconClass' => '']);
    return $items;
}

// return new R/N and labor R/N from particular patient 
function items_new_rn()
{
    $info = getInfo();
    $items = [];

    array_push($items,
        ['label' => Yii::t('app','Add New R/N'), 'iconClass' => '', 'url' => ['site/admission', 'id' => $info->patient_uid,'type' => 'Normal']],
        ['label' =>  Yii::t('app','Add New Labor R/N'), 'iconClass' => '', 'url' => ['site/admission', 'id' => $info->patient_uid, 'type' => 'Labor']]
    );
    return $items;
}


function items_receipt()
{
    if(!empty(Yii::$app->request->get('rn'))) 
        $rows = (new \yii\db\Query())
        ->select(['*'])
        ->from('receipt')
        ->where(['rn' => Yii::$app->request->get('rn')])
        ->all();
    else
        $rows = (new \yii\db\Query())
        ->select(['*'])
        ->from('receipt')
        ->where(['receipt_uid' => Yii::$app->request->get('receipt_uid')])
        ->all();

    $items = [];

    foreach ($rows as $row) {
        array_push($items, ['label' => '' .  $row['rn'] .' receipt','iconClass' => '', 'url' => ['receipt/update', 'receipt_uid' =>  $row['receipt_uid']]]);
    }
    
    return $items;
}

if(!empty(Yii::$app->request->queryParams))
    $info = getInfo();

?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= Yii::$app->homeUrl; ?>" class="brand-link">
        <img src="<?=$assetDir?>/img/AdminLTELogo.png" alt="HIS Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">SGH HIS</span>
    </a>

    <!-- Sidebar -->
    <div id="sidebarx" class="sidebar">
        <!-- Search Bar -->
        <div class="user-panel">
            <!-- SidebarSearch Form -->
            <div class="form-inline mt-3 d-flex">
                <?php 
                    $model = new Patient_information();
                    $form = ActiveForm::begin([
                    'action' => ['site/admission'],
                    'enableClientValidation'=> false,
                    'options' => [
                        'class' => 'input-group'
                    ]]); 
                ?>
                <?= $form->field($model , 'nric')->textInput(['autocomplete' =>'off', 'class' => 'form-control form-control-sidebar',
                    'style' => 'text-color: white !important;','placeholder'=>Yii::t('app','Search IC/RN')])->label(false)?>

                <div class="input-group-append">
                    <?= Html::submitButton('<i class="fas fa-search fa-fw"></i>', ['class' => 'btn btn-sidebar']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

            <!-- Sidebar Menu Line Break -->
            <div class="user-panel mt-2"></div>

            <!-- Sidebar Menu which the patient is loaded -->
            <?php
                if(!empty($info)){
                    if($info->name == "") $temp_name = "Unknown";
                    else $temp_name = $info->name;
                    echo \hail812\adminlte\widgets\Menu::widget([
                        'items' => [['label' => $temp_name,'icon' => 'user',  'url' => ['site/admission', 'id' => $info->patient_uid]]]]);
            ?>
            <div class="mt-1 ml-1 d-flex">
                <div class="info">
                    <p class="text-white"><?php echo Yii::t('app','Patient IC')." : ".$info->nric;?></p>
                    <p class="text-light">
                        <?php echo (new Patient_information()) -> getBalance($info->patient_uid).
                                "<br/>".(new Patient_information()) ->getUnclaimedBalance($info->patient_uid);?>
                    </p>
                </div>
            </div>
            <?php  }else{   ?>
            <!-- Sidebar Menu which no patient is loaded -->
            <div class="mt-1 d-flex">
                <div class="info">
                    <p class="text-white"><?php echo Yii::t('app','Patient Name')." : "?></p>
                    <p class="text-white"><?php echo Yii::t('app','Patient IC')." : "?></p>
                </div>
            </div>
            <?php  } ?>

            <!-- Sidebar Menu Line Break -->
            <div class="user-panel "></div>

            <!-- Return all RN from particular patient -->
            <?php 
                if(!empty($info)){
            ?>
            <div class="mt-2"></div>
            <?php
                echo \hail812\adminlte\widgets\Menu::widget(['items' => items()]);
                    if (Yii::$app->session->has('close_rn'))
                    {
                        Yii::$app->session->remove('close_rn');
                    }
                    else  echo \hail812\adminlte\widgets\Menu::widget(['items' => items_new_rn()]);
                    echo \hail812\adminlte\widgets\Menu::widget(['items' => 
                        [   
                            ['label' => Yii::t('app','Print Transaction Records'), 'iconClass' => '',
                            'url' => ['receipt/record', 'rn' =>  Yii::$app->request->get('rn'), 'id' => Yii::$app->request->get('id')]]
                        ]
                    ]);
                }
            ?>

            <!-- Sidebar Menu Line Break -->
            <div class="user-panel "></div>

            <!-- Load bill and payment tab when RN is selected -->
            <?php
                if(!empty($info) && !empty(Yii::$app->request->get('rn')
                    ||Yii::$app->request->get('receipt_uid') ||Yii::$app->request->get('bill_uid')))
                {
                    $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn')]);

                    // Here is loaded when bill is generated
                    if(!empty($model_bill))
                    {
                        if(empty($model_bill))
                            $url_bill = 'bill/create';
                        else if(empty($model_bill->bill_generation_datetime))
                            $url_bill = 'bill/generate';
                        else $url_bill = 'bill/print';

                        echo \hail812\adminlte\widgets\Menu::widget([
                            'items' => [
                                    ['label' => $model_bill->rn, 'header' => true],
                                    ['label' => Yii::t('app','Bill'), 'iconClass' => '', 'url' => [$url_bill, 
                                        'bill_uid' =>  $model_bill->bill_uid,  'rn' => $model_bill->rn]],
                                    ['label' => Yii::t('app','Payment'), 'iconClass' => '',
                                        'url' => ['receipt/index', 'rn' =>  Yii::$app->request->get('rn')]],
                                    ]
                            ]);
                    }
                    else 
                        // Here is loaded when bill is created
                        echo \hail812\adminlte\widgets\Menu::widget([
                            'items' => [
                                ['label' => Yii::$app->request->get('rn'), 'header' => true],
                                ['label' => Yii::t('app','Bill'), 'iconClass' => '', 
                                    'url' => ['bill/create', 'rn' =>  Yii::$app->request->get('rn')]],
                                ['label' => Yii::t('app','Payment'), 'iconClass' => '', 
                                    'url' => ['receipt/index', 'rn' =>  Yii::$app->request->get('rn')]],
                                ]
                        ]);
                }
            ?>
        </div>
        <!-- /.sidebar -->
</aside>