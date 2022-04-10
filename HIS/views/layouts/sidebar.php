<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Patient_informationSearch;
use app\models\Patient_information;
use yii\helpers\ArrayHelper;

$model = new Patient_informationSearch();

$ic = ArrayHelper::getValue(Yii::$app->request->post(), 'Patient_informationSearch.nric');
$pid = Yii::$app->request->get('pid');

if(!empty($ic) || isset($pid))
{
    if(isset($ic))
        $info = Patient_information::findOne(['nric' => $ic]);
    else  $info = Patient_information::findOne(['patient_uid' => $pid]);
}
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
        <?php $form = ActiveForm::begin([
    //   'action' => ['patient_information/view'],
    //   'action' => [\yii\helpers\Url::current()],
    //   'method' => 'get',
        'options' => [
            'class' => 'input-group'
         ]
    ]); ?>

        <?= $form->field($model , 'nric')->textInput(['class' => 'form-control form-control-sidebar',
                    'style' => 'text-color: white !important;','placeholder'=>"Search IC"])->label(false)?>

        <div class="input-group-append">
            <?= Html::submitButton('<i class="fas fa-search fa-fw"></i>', ['class' => 'btn btn-sidebar']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>


    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <?php
        if(!empty($info)){
?>
        <div class="mt-1 ml-3 d-flex">
            <div class="info">
                <p class="text-white"><?php echo $info->nric;?></p>
                <p class="text-white"><?php echo $info->name;?></p>
                <p class="text-light">Balance Unclaimed | Owed</p>
            </div>
        </div>
        <?php
         }else{
?>
        <div class="mt-1 ml-3 d-flex">
            <div class="info">
                <p class="text-white">Patient IC</p>
                <p class="text-white">Patient Name</p>
                <p class="text-light">Balance Unclaimed | Owed</p>
            </div>
        </div>
        <?php
         }
                    if(!empty($info)){
                             echo \hail812\adminlte\widgets\Menu::widget([
                                'items' => [
                         ['label' => 'RN1, paid total, balance1', 'iconClass' => ''],
                         ['label' => 'RN2, paid total, balance1', 'iconClass' => ''],
                         ['label' => 'RN3, paid total, balance1', 'iconClass' => ''],
                         ['label' => 'New R/N | Labor R/N', 'iconClass' => '', 'url' => ['patient_admission/create', 'pid' => $info->patient_uid]],
                         ['label' => 'Print Transaction Records', 'iconClass' => '']]]);
                                }
                               
                         ?>
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel"> </div>

        <?php
                             echo \hail812\adminlte\widgets\Menu::widget([
                                'items' => [

                         ['label' => 'R/N Number', 'header' => true],
                    ['label' => 'Deposit', 'iconClass' => ''],
                    ['label' => 'Treatment Details', 'iconClass' => ''],
                    ['label' => 'Bill', 'iconClass' => ''],
                    ['label' => 'Payments', 'iconClass' => ''],
                      ]
                 ])
                ?>
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel"> <br></div>
    </nav>
    <!-- /.sidebar-menu -->


    </div>
    <!-- /.sidebar -->
</aside>