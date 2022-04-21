<?php

use yii\helpers\Html;
use kartik\bs4dropdown\ButtonDropdown;
use yii\bootstrap4\Dropdown;

?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app','Admission'); ?></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="/patient_admission" class="dropdown-item"><?php echo Yii::t('app','Search'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Reminder Letters'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Batch Entry'); ?></a></li>
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a href="#" class="nav-link"><?php echo Yii::t('app','Reports'); ?></a>
        </li>
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app','Maintenance'); ?></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
            <li><a href="/lookup_general" class="dropdown-item"><?php echo Yii::t('app','General Lookup'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Users'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Kod Wad'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Kod Taraf'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Kod Rawatan'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Other Codes'); ?></a></li>
            </ul>
        </li>

    </ul>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link"><?php echo Yii::t('app','Login'); ?></a>
        </li>

        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo "Languages"; ?></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <?php
                    foreach(Yii::$app->params['languages'] as $key => $language){
                      //  echo '<span class = "language" id="'.$key.'">'.$language.' | </span>'; 
                        echo '<li><a href="#" class="dropdown-item language" id="'.$key.'">'.$language.'</a></li>';
                    }
                ?>
            </ul>
        </li>
    </ul>



</nav>


<?php

$this->registerJs(
    "$(document).on('click', '.language', function() {
        var lang = $(this).attr('id');
       
        $.post('/site/language', {'lang':lang}, function(data){
            location.reload();
        });
    });",
    
);

?>

<!-- /.navbar -->
<!-- <script>
    $(function(){
        $(document).on('click','.language',function(){
            var lang = $(this).alter('id');

            
        })
    })
</script> -->