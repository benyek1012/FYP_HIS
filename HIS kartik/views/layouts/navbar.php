<?php

use yii\helpers\Url;
use app\controllers\SiteController;

if (Yii::$app->user->isGuest){ 
?>
<style type="text/css">
#dropdownSubMenu1 {
    display: none;
}
</style>
<style type="text/css">
#dropdownSubMenu2 {
    display: none;
}
</style>
<style type="text/css">
#report {
    display: none;
}
</style>

<?php
}
    
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <?php 
            if((new SiteController(null,null)) -> accessControl() == true)
            { 
                echo '<li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>'; 
            }
        ?>

        <li class="nav-item dropdown">

            <a id="admission"  href="<?php echo Url::to(['/site/admission']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    class="nav-link dropdown-toggle"><?php echo Yii::t('app','Admission'); ?></a>
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                    <li><a href="<?php echo Url::to(['/patient_admission']); ?>" class="dropdown-item"><?php echo Yii::t('app','Search Admission'); ?></a></li>
                </ul>
        </li>

        <li class="nav-item dropdown">
            <a id="report" href="#" class="nav-link"><?php echo Yii::t('app','Reports'); ?></a>
        </li>
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app','Maintenance'); ?></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="<?php echo Url::to(['/lookup_general']); ?>" class="dropdown-item"><?php echo Yii::t('app','General Lookup'); ?></a>
                </li>
                <li><a href="<?php echo Url::to(['/new_user']); ?>" class="dropdown-item"><?php echo Yii::t('app','User Management'); ?></a></li>
                <li><a href="<?php echo Url::to(['/lookup_ward']); ?>" class="dropdown-item"><?php echo Yii::t('app','Ward Codes'); ?></a></li>
                <li><a href="<?php echo Url::to(['/lookup_status']); ?>" class="dropdown-item"><?php echo Yii::t('app','Status Lookup'); ?></a></li>
                <li><a href="<?php echo Url::to(['/lookup_treatment']); ?>" class="dropdown-item"><?php echo Yii::t('app','Treatment Codes'); ?></a>
                </li>
                <li><a href="<?php echo Url::to(['/lookup_department']); ?>"
                        class="dropdown-item"><?php echo Yii::t('app','Department Codes'); ?></a></li>
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app', "Others"); ?></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <!-- <li><a href="/patient_admission" class="dropdown-item"><?php echo Yii::t('app','Search'); ?></a></li> -->
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Reminder Letters'); ?></a></li>
                <li><a href="#" class="dropdown-item"><?php echo Yii::t('app','Batch Entry'); ?></a></li>
                <li><a href="<?php echo Url::to(['/batch']); ?>" class="dropdown-item"><?php echo Yii::t('app','Testing csv upload'); ?></a></li>
            </ul>
        </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown">
            <a id="admission" href="<?php echo Url::to(['/site/logout']); ?>" class="nav-link"><?php echo  Yii::t('app','Logout'). 
                ' (' . Yii::$app->user->identity->username . ')'; ?></a>
        </li>

        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo  Yii::t('app','Languages'); ?></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <?php
                    foreach(Yii::$app->params['languages'] as $key => $language){
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
        var str = window.location.pathname;
        var lastChar = str[str.length - 1];
        $.post('". Url::to(['/site/language'])."', {'lang':lang}, function(data){
            if(lastChar == '/') window.location.href = '". Url::to(['/site/index'])."';
            else location.reload();
        });
    });"
);

?>
