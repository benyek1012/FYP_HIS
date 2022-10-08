<?php
// password : 12345

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use app\models\LoginForm;
use yii\helpers\Url;

$this->title = Yii::t('app','Login');
$this->params['breadcrumbs'][] = $this->title;
//echo LoginForm::hashPassword("12345");
?>
<div class="site-login ">

    <div class="row">
        <div class="col-sm">
            <p><?php echo Yii::t('app',"Please fill out the following fields to login:")?></p>
        </div>
        <div class="col-sm">
            <!-- Navbar -->
            <nav class="navbar navbar-expand navbar-white navbar-light">
                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"
                            class="nav-link dropdown-toggle"><?php echo  Yii::t('app','Languages'); ?></a>
                            <div class="dropdown-content">
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow" style="margin-top: 0px; margin-right: 20px ;">
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
        </div>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
         //   'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
       //     'inputOptions' => ['class' => 'col-lg-3 form-control'],
            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

    <?= $form->field($model, 'username', ['labelOptions' => [ 'class' => 'col-lg-3' ]])->textInput(['autocomplete' =>'off', 'autofocus' => true]) ?>

    <?= $form->field($model, 'password', ['labelOptions' => [ 'class' => 'col-lg-3' ]])->passwordInput(['autocomplete' =>'off', ]) ?>

    <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\" col-lg-3 custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ]) ?>

    <div class="form-group">
        <div class="col-lg-11 justify-content-center row">
            <?= Html::submitButton(Yii::t('app','Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

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

$this->registerJs('
const $dropdown = $(".dropdown");
const $dropdownToggle = $(".dropdown-toggle");
const $dropdownMenu = $(".dropdown-menu");
const showClass = "show";
 
$(window).on("load resize", function() {
  if (this.matchMedia("(min-width: 768px)").matches) {
    $dropdown.hover(
      function() {
        const $this = $(this);
        $this.addClass(showClass);
        $this.find($dropdownToggle).attr("aria-expanded", "true");
        $this.find($dropdownMenu).addClass(showClass);
      },
      function() {
        const $this = $(this);
        $this.removeClass(showClass);
        $this.find($dropdownToggle).attr("aria-expanded", "false");
        $this.find($dropdownMenu).removeClass(showClass);
      }
    );
  } else {
    $dropdown.off("mouseenter mouseleave");
  }
});'
);

?>

</div>