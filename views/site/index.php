<?php


if (Yii::$app->user->isGuest){ 
    $this->title = 'Please Sign In';
    $this->params['breadcrumbs'] = [['label' => $this->title]];
    ?>
    
<style type="text/css">#card1{
display:none;
}</style>

<?php
    
}
else {
    $this->title = 'Home Page';
    $this->params['breadcrumbs'] = [['label' => $this->title]];
    ?>
    <style type="text/css">#loginButton{
display:none;
}</style>

<?php
    }    
?>

<div id="card1" class="container-fluid">
    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title">Patient Admission Summary</h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            The body of the card
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title">Patient Information</h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body d-flex flex-column">
            The body of the card
            <button type="button" class="btn btn-outline-primary align-self-end" style="width: 8rem;">Update</button>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title">Waris</h3>
            <div class="card-tools">
                <!-- Collapse Button -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body d-flex flex-column">
            <button type="button" class="btn btn-outline-primary align-self-start" style="width: 8rem;">Add
                Waris</button>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
    
<div id="loginButton" class="container-fluid">
<h1><a href="?r=site%2Flogin" class="nav-link">Click here to login.</a></h1>
</div>
