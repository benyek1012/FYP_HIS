<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\controllers\SiteController;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<?php if(YII::$app->user->isGuest){ ?>
<?= $this->render('main-login', ['content' => $content, 'assetDir' => $assetDir]) ?>
<?php }else{
?>

<body class="d-flex flex-column  <?php if((new SiteController(null,null)) -> accessControl() == false){ echo "sidebar-collapse"; }?>">
    <?php $this->beginBody() ?>

    <div class="wrapper">

        <!-- Navbar -->
        <?= $this->render('navbar', ['assetDir' => $assetDir]) ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?= $this->render('temp', ['assetDir' => $assetDir]) ?>

        <div class="card">
            <div class="card-body">
                <!-- Content Wrapper. Contains page content -->

                <?= $this->render('content', ['content' => $content, 'assetDir' => $assetDir]) ?>
                <!-- /.content-wrapper -->
            </div>
        </div>

    </div>

    <?php $this->endBody() ?>
</body>
<?php } ?>

</html>
<?php $this->endPage() ?>