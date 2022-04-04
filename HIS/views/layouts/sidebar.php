<?php
use yii\widgets\ActiveForm;
use app\models\Patient_informationSearch;
use yii\helpers\Html;

$model1 = new Patient_informationSearch;
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
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
            <div class="form-inline mt-3 pb-3 mb-4 d-flex">
                <div class="input-group" data-widget="sidebar-search">

                    <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                     ]); ?>
                    <?= $form->field($model1, 'nric')->textInput(['class' => 'form-control form-control-sidebar',
                    'style' => 'text-color: white !important;','placeholder'=>"Search IC"])->label(false)?>
                    <div class="input-group-append">
                            <?= Html::submitButton('<i
                                class="fas fa-search fa-fw"></i>', ['class' => 'btn btn-sidebar']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        
<?php /*
            <!-- Sidebar -->
    <div class="sidebar">
        <!-- Search Bar -->
        <div class="user-panel">
            <!-- SidebarSearch Form -->
            <!-- href be escaped -->
            <div class="form-inline mt-3 pb-3 mb-4 d-flex">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                        aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        */ ?>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            echo \hail812\adminlte\widgets\Menu::widget([
                      'items' => [
                         /* [
                              'label' => 'Starter Pages',
                             'icon' => 'far',
                              'items' => [
                                  ['label' => 'Active Page', 'url' => ['site/index'], 'iconStyle' => 'far'],
                                    ['label' => 'Inactive Page', 'iconStyle' => 'far'],
                              ]
                          ], */
                          
                         ['label' => 'Patient NRIC', 'iconClass' => '']  ]
                         ])
                         ?>
            <!-- Sidebar user panel (optional) -->
            <div class="mt-1 ml-3 d-flex">
                <div class="info">
                    <p class="text-white">Patient Name</p>
                    <p class="text-light">Balance Unclaimed | Owed</p>
                </div>
            </div>

            <?php
                             echo \hail812\adminlte\widgets\Menu::widget([
                                'items' => [
                         ['label' => 'RN1, paid total, balance1', 'iconClass' => ''],
                         ['label' => 'RN2, paid total, balance1', 'iconClass' => ''],
                         ['label' => 'RN3, paid total, balance1', 'iconClass' => ''],
                         ['label' => 'New R/N | Labor R/N', 'iconClass' => ''],
                         ['label' => 'Print Transaction Records', 'iconClass' => '']]
                         ])
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