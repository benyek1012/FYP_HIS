<?php

use yii\helpers\Url;
use app\controllers\SiteController;
use app\models\New_user;


if (Yii::$app->user->isGuest){ 
?>
<style type="text/css">
#report {
    display: none;
}
</style>
<style type="text/css">
body {
    background: #f2f2f2;
}
</style>
<style type="text/css">
.dropdown-content a:hover {
    background-color: #ddd;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropdown:hover .dropbtn {
    background-color: #3e8e41;
}
</style>
<?php
}
    
?>
<!-- Navbar -->
<!-- <nav class="main-header navbar navbar-expand navbar-white navbar-light ">


    Left navbar links -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light ">
    <ul class="navbar-nav">
        <?php 
            if((new SiteController(null,null)) -> accessControl() == true)
            { 
                echo '<li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>'; 
            }
            if(Yii::$app->controller->action->id != "guest_printer_dashboard"){
                ?>
        <?php /* <div class="dropdown">
            <a id="admission" href="<?php echo Url::to(['/site/admission']); ?>" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle"><?php echo Yii::t('app','Admission'); ?></a>
        <div class="dropdown-content">
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                style="margin-top: 0px; margin-right: 20px ;">
                <li><a href="<?php echo Url::to(['/patient_admission']); ?>"
                        class="dropdown-item"><?php echo Yii::t('app','Search Admission'); ?></a></li>
                <!-- <li><a href="<?php echo Url::to(['/patient_information']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Search Patient by Entry Date'); ?></a></li> -->
                <li><a href="<?php echo Url::to(['/site/admission']); ?>"
                        class="dropdown-item"><?php echo Yii::t('app','Patient Admission Summary'); ?></a></li>
                <!-- <li><a href="<?php echo Url::to(['/site/batch_entry']);?>"
                            class="dropdown-item"><?php echo Yii::t('app','Batch Entry'); ?></a></li> -->
            </ul>
        </div>
        </div> */ ?>

        <div class="dropdown">
            <a id="admission" href="<?php echo Url::to(['/site/admission']); ?>"
                class="nav-link"><?php echo Yii::t('app','Admission'); ?></a>
        </div>

        <div class="dropdown">
            <a id="admission" href="<?php echo Url::to(['/site/admission']); ?>" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app','Search'); ?></a>
            <div class="dropdown-content">
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                    style="margin-top: 0px; margin-right: 20px ;">
                    <li><a href="<?php echo Url::to(['/patient_information/search_name']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Search Patient by Name'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/patient_information/search_date']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Search Patient by Entry Date'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/patient_information/search_ward']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Search Patient by Ward'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/patient_information/search_discharge']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Search Patient by Discharge Date'); ?></a>
                    <li><a href="<?php echo Url::to(['/patient_information/search_by_bill_number']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Search Patient by Bill Number'); ?></a>
                    </li>
                </ul>
            </div>
        </div>


        <!-- <div class="dropdown">
                    <a id="admission"  href="<?php echo Url::to(['/site/admission']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    class="nav-link dropdown-toggle"><?php echo Yii::t('app','Admission'); ?></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a href="<?php echo Url::to(['/patient_admission']); ?>" class="dropdown-item"><?php echo Yii::t('app','Search Admission'); ?></a>
                        <a href="<?php echo Url::to(['/site/admission']); ?>" class="dropdown-item"><?php echo Yii::t('app','Patient Admission Summary'); ?></a>
                    </div>
                </div> -->

        <div class="dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app','Maintenance'); ?></a>
            <div class="dropdown-content">
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                    style="margin-top: 0px; margin-right: 20px ;">
                    <li><a href="<?php echo Url::to(['/lookup_general']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','General Lookup'); ?></a>
                    </li>
                    <?php if((new New_user()) -> isAdmin()) { ?>
                    <li><a href="<?php echo Url::to(['/new_user']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','User Management'); ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo Url::to(['/lookup_ward']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Ward Codes'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/lookup_status']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Status Lookup'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/lookup_treatment']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Treatment Codes'); ?></a>
                    </li>
                    <li><a href="<?php echo Url::to(['/lookup_department']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Department Codes'); ?></a></li>
                    <?php if(Yii::$app->params['hide_fpp'] != "true"){ ?>
                            <li><a href="<?php echo Url::to(['/lookup_fpp']); ?>"
                                    class="dropdown-item"><?php echo Yii::t('app','FPP Lookup'); ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo Url::to(['/lookup_inpatient_treatment_cost']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Inpatient Treatment Lookup'); ?></a></li>
                </ul>
            </div>
        </div>

        <li class="nav-item dropdown">
            <!-- <a id="report" href="#" class="nav-link"><?php echo Yii::t('app','Reports'); ?></a> -->
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app', "Reports"); ?></a>
            <div class="dropdown-content">
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                    style="margin-top: 0px; margin-right: 20px ;">
                    <li><a href="<?php echo Url::to(['/report/report1']);?>"
                            class="dropdown-item"><?php echo Yii::t('app','Report Senarai Baki Pendeposit'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/report/report5']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Report Kutipan Hasil Bil'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/report/report7']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Report Serahan Wang Kutipan'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/report/report8']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Report Serahan Wang Kutipan Mengikut Keadeh Bayaran'); ?></a>
                    </li>
                    <li><a href="#" class="dropdown-item"
                            style="color: grey"><?php echo Yii::t('app','Report Bill Pesakit'); ?></a></li>
                    <li><a href="#" class="dropdown-item"
                            style="color: grey"><?php echo Yii::t('app','Report Bulanan Kutipan dan PATI dan PADI'); ?></a>
                    </li>
                    <li><a href="#" class="dropdown-item"
                            style="color: grey"><?php echo Yii::t('app','Report Senarai Terperinci Bill Hospital'); ?></a>
                    </li>
                    <li><a href="#" class="dropdown-item"
                            style="color: grey"><?php echo Yii::t('app','Report Penyata Akaun Belum Terima'); ?></a>
                    </li>
                    <!-- <li><a href="<?php echo Url::to(['/cancellation/deleted']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Deleted'); ?></a></li> -->
                    <!--<li><a href="<?//php echo Url::to(['/dbupdate']); ?>"
                    class="dropdown-item"><?//php echo Yii::t('app','Testing database update'); ?></a></li> -->
                </ul>
            </div>
        </li>

        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo Yii::t('app', "Others"); ?></a>
            <div class="dropdown-content">
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                    style="margin-top: 0px; margin-right: 20px ;">
                    <li><a href="<?php echo Url::to(['/reminder']);?>"
                            class="dropdown-item"><?php echo Yii::t('app','Reminder Letters'); ?></a></li>
                    <?php if((new New_user()) -> isAdmin()) { ?>
                    <li><a href="<?php echo Url::to(['/pekeliling_import']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Pekeliling Imports'); ?></a></li>
                    <?php } ?>
                    <?php if((new New_user()) -> isAdmin()) { ?>
                    <li><a href="<?php echo Url::to(['/cancellation/deleted']); ?>"
                            class="dropdown-item"><?php echo Yii::t('app','Deleted'); ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo Url::to(['/site/batch_entry']);?>"
                            class="dropdown-item"><?php echo Yii::t('app','Batch Entry'); ?></a></li>
                    <li><a href="<?php echo Url::to(['/receipt/index', 'rn' => 'LAINLAIN']);?>"
                            class="dropdown-item"><?php echo Yii::t('app','Other Payments'); ?></a></li>
                    <!-- <li><a href="<?php echo Url::to(['/site/adjust_print']);?>"
                            class="dropdown-item"><?php echo Yii::t('app','Adjust Print'); ?></a></li> -->
                    <!--<li><a href="<?//php echo Url::to(['/dbupdate']); ?>"
                    class="dropdown-item"><?//php echo Yii::t('app','Testing database update'); ?></a></li> -->
                    <li><a href="<?php echo Url::to(['/site/forgive_bill']);?>"
                            class="dropdown-item"><?php echo Yii::t('app','Biil Forgive'); ?></a></li>
                   
            </div>
        </li>
    </ul>
    <?php
            
            }
            ?>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <?php 
        if(!(new New_user()) -> isAdmin())
        {
        ?>
        <li class="nav-item dropdown">
            <a id="password" href="<?php echo Url::to(['/new_user/change_password']); ?>"
                class="nav-link"><?php echo Yii::t('app', 'Change Password') ?></a>
        </li>
        <?php
        }
        ?>

        <li class="nav-item dropdown">
            <a id="admission" href="<?php echo Url::to(['/site/logout']); ?>" class="nav-link"><?php echo  Yii::t('app','Logout'). 
                        ' (' . Yii::$app->user->identity->username . ')'; ?></a>
        </li>

        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><?php echo  Yii::t('app','Languages'); ?></a>
            <div class="dropdown-content">
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                    style="margin-top: 0px; margin-right: 20px ;">
                    <?php
                            foreach(Yii::$app->params['languages'] as $key => $language){
                                echo '<li><a href="#" class="dropdown-item language" id="'.$key.'">'.$language.'</a></li>';
                            }
                        ?>
                </ul>
            </div>
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

$this->registerJs(
    "$('.navbar .dropdown').hover(function() {
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(20);
      }, function() {
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(20)
      });"
);

?>
<script>

</script>
<?php
// $this->registerJs('
// const $dropdown = $(".dropdown");
// const $dropdownToggle = $(".dropdown-toggle");
// const $dropdownMenu = $(".dropdown-menu");
// const showClass = "show";
 
// $(window).on("load resize", function() {
//   if (this.matchMedia("(min-width: 768px)").matches) {
//     $dropdown.hover(
//       function() {
//         const $this = $(this);
//         $this.addClass(showClass);
//         $this.find($dropdownToggle).attr("aria-expanded", "true");
//         $this.find($dropdownMenu).addClass(showClass);
//       },
//       function() {
//         const $this = $(this);
//         $this.removeClass(showClass);
//         $this.find($dropdownToggle).attr("aria-expanded", "false");
//         $this.find($dropdownMenu).removeClass(showClass);
//       }
//     );
//   } else {
//     $dropdown.off("mouseenter mouseleave");
//   }
// });'
// );
?>