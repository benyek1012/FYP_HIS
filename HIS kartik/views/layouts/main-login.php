<?php

/* @var $this \yii\web\View */
/* @var $content string */

\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700');
$this->registerCssFile('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
\hail812\adminlte3\assets\PluginAsset::register($this)->add(['fontawesome', 'icheck-bootstrap']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 3 | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column">
    <?php  $this->beginBody() ?>

    <div class="login-logo mt-5">
        <a href="<?=Yii::$app->homeUrl?>"><b>Hospital Information System</b></a>
    </div>
    <!-- /.login-logo -->

    <div class="row align-items-center v-100 mt-3">
        <div class="col-6 mx-auto">
            <div class="card shadow border">
                <div class="card-body">
                    <h1>Login</h1>
                    <?= $content ?>
                    <!-- /.content-wrapper -->
                </div>
            </div>
        </div>
    </div>

    <!-- /.login-box -->

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>