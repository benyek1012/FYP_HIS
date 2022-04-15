<?php

use app\models\Patient_admission;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Patient_information;

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
    
    return $info;
}

function items()
{
    $info = getInfo();

    $rows = (new \yii\db\Query())
    ->select(['rn'])
    ->from('patient_admission')
    ->where(['patient_uid' => $info->patient_uid])
    ->all();

    $items = [];

    foreach ($rows as $row) {
        array_push($items, ['label' => '' .  $row['rn'] .'','iconClass' => '', 'url' => ['patient_admission/update', 'rn' =>  $row['rn']]]);
    }
    array_push($items,
        ['label' => 'New R/N', 'iconClass' => '', 'url' => ['patient_admission/create', 'id' => $info->patient_uid,'type' => 'Normal']],
        ['label' => 'New Labor R/N', 'iconClass' => '', 'url' => ['patient_admission/create', 'id' => $info->patient_uid, 'type' => 'Labor']]
    );
    array_push($items,['label' => 'Print Transaction Records', 'iconClass' => '']);
    
    return $items;
}

if(!empty(Yii::$app->request->get('rn') || Yii::$app->request->get('id')))
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
    <div class="sidebar">
        <!-- Search Bar -->
        <div class="user-panel">
            <!-- SidebarSearch Form -->
            <!-- href be escaped -->
            <?php /*  <div class="form-inline mt-3 pb-3 mb-4 d-flex">
                <div class="input-group" data-widget="sidebar-search">
                    <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'post',
                     ]); ?>
            <?= $form->field($model1, 'globalSearch')->textInput(['class' => 'form-control form-control-sidebar',
                    'style' => 'text-color: white !important;','placeholder'=>"Search IC"])->label(false)?>
            <div class="input-group-append">
                <?php /*Html::submitButton('<i class="fas fa-search fa-fw"></i>', ['class' => 'btn btn-sidebar'], [ 'onclick' => '
                                $.ajax({
                                    type: "POST",
                                    url: "/sidebar/actionGlobalSearch" ']); */?>
                <?php /*          <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    </div>
    ?>
    <?php */ ?>
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
                    'style' => 'text-color: white !important;','placeholder'=>"Search IC/RN"])->label(false)?>

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
                <p class="text-white"><?php echo "NRIC : ".$info->nric;?></p>
                <p class="text-light">Balance Unclaimed | Owed</p>
            </div>
        </div>
        <?php
    }else
    {
?>
        <div class="mt-1 ml-3 d-flex">
            <div class="info">
                <p class="text-white">Patient Name</p>
                <p class="text-white">Patient IC</p>
                <p class="text-light">Balance Unclaimed | Owed</p>
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
         if(!empty($info) && !empty(Yii::$app->request->get('rn'))){
        echo \hail812\adminlte\widgets\Menu::widget([
            'items' => [
                ['label' => Yii::$app->request->get('rn'), 'header' => true],
                ['label' => 'Bill', 'iconClass' => '', 'url' => ['bill/create', 'rn' =>  Yii::$app->request->get('rn')]],
                ['label' => 'Payments', 'iconClass' => ''],
                      ]
            ]);
?>
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel"> </div>
<?php        } ?>
    </nav>
    <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
</aside>