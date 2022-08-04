<?php

use app\models\Bill;
use app\models\Patient_admission;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Patient_information;
use app\models\Receipt;
use yii\helpers\Url;

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
function items($rn)
{
    $items = [];

    $model_bill = Bill::findOne(['rn' => Yii::$app->request->get('rn'), 'deleted' => 0]);

    if(empty($model_bill))
        $url_bill = 'bill/create';
    else if(empty($model_bill->bill_generation_datetime))
        $url_bill = 'bill/generate';
    else $url_bill = 'bill/print';

    if(!empty($model_bill))
    {
        array_push($items, 
        ['label' => '' .  $rn .'','iconClass' => '', 'url' => ['patient_admission/update', 'rn' =>  $rn]]);

        if($rn == Yii::$app->request->get('rn'))
        {
            array_push($items,['label' => Yii::t('app','Bill'), 'iconClass' => '', 'url' => [$url_bill, 
            'bill_uid' =>  $model_bill->bill_uid,  'rn' => $rn]]);
            array_push($items, ['label' => Yii::t('app','Payment'), 'iconClass' => '',
            'url' => ['receipt/index', 'rn' =>  $rn]]);
        }
    }
    else{
        array_push($items, 
        ['label' => '' .  $rn .'','iconClass' => '', 'url' => ['patient_admission/update', 'rn' =>  $rn]]);

        if($rn == Yii::$app->request->get('rn'))
        {
            array_push($items, ['label' => Yii::t('app','Bill'), 'iconClass' => '', 
                        'url' => ['bill/create', 'rn' =>  $rn]]);
            array_push($items, ['label' => Yii::t('app','Payment'), 'iconClass' => '',
            'url' => ['receipt/index', 'rn' =>  $rn]]);
        }
    }

    return $items;
}


if(!empty(Yii::$app->request->queryParams))
    $info = getInfo();

if(!empty($info))
{
    // check whether any rn belongs to patient
    $model_rn = Patient_admission::findOne(['patient_uid' =>  $info->patient_uid]);
    $rows = (new \yii\db\Query())
    ->select(['rn'])
    ->from('patient_admission')
    ->where(['patient_uid' => $info->patient_uid])
    ->orderBy(['entry_datetime' => SORT_DESC, 'rn' => SORT_DESC])
    ->all();
}

$url = Url::toRoute(['site/sidebar']);
$urlAdmission = Url::toRoute(['site/admission']);
$urlPatientAdmission = Url::toRoute(['patient_admission/update']);
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= Url::to(['/site/index']); ?>" class="brand-link">
        <!-- <img src="<?=$assetDir?>/img/AdminLTELogo.png" alt="HIS Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8"> -->
        <span class="brand-text font-weight-light "><b>SGHIS</b></span>
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
                    // 'action' => ['site/admission'],
                    'enableClientValidation'=> false,
                    'options' => [
                        'class' => 'input-group'
                    ]]); 
                ?>
                <?= $form->field($model , 'nric')->textInput(['autocomplete' =>'off', 'class' => 'form-control form-control-sidebar',
                    'style' => 'text-color: white !important;','placeholder'=>Yii::t('app','Search IC/RN')])->label(false)?>

                <div class="input-group-append">
                    <?= Html::button('<i class="fas fa-search fa-fw"></i>', ['class' => 'btn btn-sidebar', 'id' => 'searchButton','onclick' => "sidebar('{$url}', '{$urlAdmission}', '{$urlPatientAdmission}')"]) ?>
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
        </div>
        <!-- Return all RN from particular patient -->
        <?php 
            if(!empty($info)){
                if(!empty($model_rn)){
                    echo '<div class="mt-2"></div><div class="user-panel">'.
                        \hail812\adminlte\widgets\Menu::widget(['items' => 
                            [   
                                ['label' => Yii::t('app','Print Transaction Records'), 'iconClass' => '',
                                'url' => ['receipt/record', 'rn' =>  Yii::$app->request->get('rn'), 'id' => Yii::$app->request->get('id')]]
                            ]
                        ]). '</div><div class="mt-2"></div>';
                }

                foreach ($rows as $row) {
                    echo '<div class="mt-2"></div><div class="user-panel">'
                            .\hail812\adminlte\widgets\Menu::widget(['items' => items($row['rn'])]).
                        '</div><div class="mt-2"></div>';
                  //  echo '<div class="mt-2"></div><div class="user-panel "></div><div class="mt-2"></div>';
                }
            }
        ?>
        <!-- /.sidebar -->
    </div>

</aside>

<script>
document.getElementById("patient_information-nric")
    .addEventListener("keypress", function(event) {
    if (event.keyCode == 13) {
        document.getElementById("searchButton").onclick();
        event.preventDefault();
    }
});

function sidebar(url, urlAdmission, urlPatientAdmission) {
    var search = document.getElementById("patient_information-nric").value;
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange  = function() {
        if(xhttp.readyState == 4 && xhttp.status == 200){
            if(this.responseText != false){
                var data = $.parseJSON(this.responseText);
                if(data.rn != null){
                    location.href = urlPatientAdmission + "&rn=" + data.rn;
                }
                else if(data.patient_uid != null){
                    location.href = urlAdmission + "&id=" + data.patient_uid;
                }
            }
            else{
                confirmActionPatient();
            }
        }
    }
    xhttp.open("GET", url + "&search=" + search, true);
    xhttp.send();
}

<?php if( Yii::$app->language == "en"){ ?>
// The function below will start the confirmation dialog
function confirmActionPatient() {
    var answer = confirm("Are you sure to create patient information?");
    if (answer) {
        window.location.href =  '<?php echo Url::toRoute(['/patient_information/create']) ?>';
    } else {
        // window.location.href = '<?php echo Url::toRoute(['/site/admission']) ?>';
        // window.location.href = history.back();
    }   
}

// The function below will start the confirmation dialog
function duplicateIC() {
   alert('NRIC is existed in system!');
   window.location.href = history.go(-1);
}

<?php }else{?>
// The function below will start the confirmation dialog
function confirmActionPatient() {
    var answer = confirm("Adakah anda pasti untuk membuat butiran pesakit?");
    if (answer) {
        window.location.href =  '<?php echo Url::toRoute(['/patient_information/create']) ?>';
    } else {
        // window.location.href = '<?php echo Url::toRoute(['/site/admission']) ?>';
        // window.location.href = history.back();
    }   
}

// The function below will start the confirmation dialog
function duplicateIC() {
   alert('NRIC wujud dalam sistem!');
    window.location.href = history.go(-1);
}

<?php } ?>

</script>

<?php 
$script = <<< JS
$(document).on('focus', '.select2.select2-container', function (e) {
    var isOriginalEvent = e.originalEvent // don't re-open on closing focus event
    var isSingleSelect = $(this).find(".select2-selection--single").length > 0 // multi-select will pass focus to input

    if (isOriginalEvent && isSingleSelect) {
        $(this).siblings('select:enabled').select2('open');
    } 
});
JS;
$this->registerJS($script);
?>