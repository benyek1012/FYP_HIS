<?php

use app\models\Bill;
use app\models\Patient_admission;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Patient_information;
use app\models\Receipt;

if (Yii::$app->user->isGuest){ 
    ?>
  
<style type="text/css">#sidebarx{
display:none;
}</style>

<?php
}

function getInfo()
{
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
    
    return $info;
}

function items()
{
    $info = getInfo();

    $rows = (new \yii\db\Query())
    ->select(['rn'])
    ->from('patient_admission')
    ->where(['patient_uid' => $info->patient_uid])
    ->orderBy(['entry_datetime' => SORT_DESC])
    ->all();

    $items = [];

    foreach ($rows as $row) {
        array_push($items, ['label' => '' .  $row['rn'] .'','iconClass' => '', 'url' => ['patient_admission/update', 'rn' =>  $row['rn']]]);
    }
    array_push($items,
        ['label' => Yii::t('app','New R/N'), 'iconClass' => '', 'url' => ['site/index', 'id' => $info->patient_uid,'type' => 'Normal']],
        ['label' =>  Yii::t('app','New Labor R/N'), 'iconClass' => '', 'url' => ['site/index', 'id' => $info->patient_uid, 'type' => 'Labor']]
    );
    array_push($items,['label' => Yii::t('app','Print Transaction Records'), 'iconClass' => '']);
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
        <span class="brand-text font-weight-light">HIS</span>
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
        'action' => ['site/index'],
        'enableClientValidation'=> false,
        'options' => [
            'class' => 'input-group'
         ]
    ]); ?>

        <?= $form->field($model , 'nric')->textInput(['autocomplete' =>'off', 'class' => 'form-control form-control-sidebar',
                    'style' => 'text-color: white !important;','placeholder'=>Yii::t('app','Search IC/RN')])->label(false)?>

        <div class="input-group-append">
            <?= Html::submitButton('<i class="fas fa-search fa-fw"></i>', ['class' => 'btn btn-sidebar']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="user-panel"><br /> </div>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <?php
    if(!empty($info)){
        if($info->name == "") $temp_name = "User";
        else $temp_name = $info->name;
         echo \hail812\adminlte\widgets\Menu::widget([
            'items' => [['label' => $temp_name, 'iconClass' => '', 'url' => ['site/index', 'id' => $info->patient_uid]]]
])
?>
        <div class="mt-1 ml-3 d-flex">
            <div class="info">
                <p class="text-white"><?php echo Yii::t('app','NRIC')." : ".$info->nric;?></p>
                <p class="text-light"><?php echo Yii::t('app','Balance Unclaimed | Owed')?></p>
            </div>
        </div>
        <?php
    }else
    {
?>
        <div class="mt-1 ml-3 d-flex">
            <div class="info">
                <p class="text-white"><?php echo Yii::t('app','Patient Name')?></p>
                <p class="text-white"><?php echo Yii::t('app','Patient IC')?></p>
                <p class="text-light"><?php echo Yii::t('app','Balance Unclaimed | Owed')?></p>
            </div>
        </div>
        <?php
    }
    if(!empty($info))
        echo \hail812\adminlte\widgets\Menu::widget(['items' => items()]);
?>

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel"> </div>

<?php
    if(!empty($info) && !empty(Yii::$app->request->get('rn')||Yii::$app->request->get('receipt_uid') ||Yii::$app->request->get('bill_uid'))){
        echo \hail812\adminlte\widgets\Menu::widget([
            'items' => [
                ['label' => Yii::$app->request->get('rn'), 'header' => true],
                ['label' => Yii::t('app','Bill'), 'iconClass' => '', 'url' => ['bill/create', 'rn' =>  Yii::$app->request->get('rn')]],
                ['label' => Yii::t('app','Payment'), 'iconClass' => '', 'url' => ['receipt/index', 'rn' =>  Yii::$app->request->get('rn')]],
                      ]
        ]);
    }
?>
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel"> </div>
<?php       
    // if(!empty($info) && !empty(Yii::$app->request->get('rn')||Yii::$app->request->get('receipt_uid')))
    //     echo \hail812\adminlte\widgets\Menu::widget(['items' => items_receipt()]);

         ?>
         <!-- Sidebar user panel (optional) -->
         <div class="user-panel"></div>

    </nav>
    <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
</aside>