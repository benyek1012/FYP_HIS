<?php

use app\models\Patient_admission;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Patient_informationSearch;
use app\models\Patient_information;
use yii\helpers\ArrayHelper;

if (Yii::$app->user->isGuest){ 
    ?>
  
<style type="text/css">#sidebarx{
display:none;
}</style>

<?php
}

$model = new Patient_informationSearch();


$ic = ArrayHelper::getValue(Yii::$app->request->post(), 'Patient_informationSearch.nric');
$pid = Yii::$app->request->get('pid');
$rn = Yii::$app->request->get('rn');
$id = Yii::$app->request->get('id');


if(!empty($ic) || isset($pid) || isset($rn)||isset($id))
{
    if(isset($ic))
        $info = Patient_information::findOne(['nric' => $ic]);
    else if(isset($pid)) $info = Patient_information::findOne(['patient_uid' => $pid]);
    else if(isset($id)) $info = Patient_information::findOne(['patient_uid' => $id]);
    else $info = Patient_admission::findOne(['rn' => $rn]);
    
    if(isset($rn))
    {
        $info = Patient_information::findOne(['patient_uid' => $info->patient_uid]);
        // echo '<pre>';
        // var_dump($info);
        // exit();
        // echo '</pre>';
    }
   
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
    <div id="sidebarx" class="sidebar">
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
            function items()
            {
                $ic = ArrayHelper::getValue(Yii::$app->request->post(), 'Patient_informationSearch.nric');
                $info = Patient_information::findOne(['nric' => $ic]);
                $rn = Yii::$app->request->get('rn');
                $id = Yii::$app->request->get('id');
                if(!empty($ic) || isset($pid) || isset($rn)||isset($id))
                {
                    if(isset($ic))
                        $info = Patient_information::findOne(['nric' => $ic]);
                    else if(isset($pid)) $info = Patient_information::findOne(['patient_uid' => $pid]);
                    else if(isset($id)) $info = Patient_information::findOne(['patient_uid' => $id]);
                    else $info = Patient_admission::findOne(['rn' => $rn]);
                    
                    if(isset($rn))
                    {
                        $info = Patient_information::findOne(['patient_uid' => $info->patient_uid]);
                        // echo '<pre>';
                        // var_dump($info);
                        // exit();
                        // echo '</pre>';
                    }
                
                }

                $rows = (new \yii\db\Query())
                ->select(['rn'])

                ->from('patient_admission')

                ->where(['patient_uid' => $info->patient_uid])

                ->all();
            
                $items = [];
                foreach ($rows as $row) {
                    array_push($items, ['label' => '' .  $row['rn'] .'','iconClass' => '', 'url' => ['/patient_admission/view', 'rn' =>  $row['rn']]]);
                  //  , 'url' => Url::to(['index', 'rn' =>  $row['rn']])
                }
                array_push($items, ['label' => 'New R/N | Labor R/N', 'iconClass' => '', 'url' => ['patient_admission/create', 'id' => $info->patient_uid]]);
               // array_push($items, ['label' => 'New Waris', 'iconClass' => '', 'url' => ['patient_next_of_kin/create', 'id' => $info->patient_uid]]); 
                array_push($items,['label' => 'Print Transaction Records', 'iconClass' => '']);
                
                return $items;
            }


                 echo \hail812\adminlte\widgets\Menu::widget([
                    'items' => items()]);
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