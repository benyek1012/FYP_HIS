<?php

use yii\helpers\Html;

?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>


        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Patients</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="/patient_information/create" class="dropdown-item">New Patients</a></li>
            </ul>
        </li>

        
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Admissions</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
            <li><a href="/patient_admission" class="dropdown-item">Search </a></li>
                <li><a href="/patient_admission/create" class="dropdown-item">New Admission</a></li>
                <li><a href="#" class="dropdown-item">New Admission(Labor)</a></li>
                <li><a href="#" class="dropdown-item">Reminder Letters</a></li>
                <li><a href="#" class="dropdown-item">Batch Entry</a></li>
            </ul>
        </li>

        <li class="nav-item dropdown">
            <a href="#" class="nav-link">Reports</a>
        </li>
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Maintenance</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="#" class="dropdown-item">Users</a></li>
                <li><a href="#" class="dropdown-item">Kod Wad</a></li>
                <li><a href="#" class="dropdown-item">Kod Taraf</a></li>
                <li><a href="#" class="dropdown-item">Kod Rawatan</a></li>
                <li><a href="#" class="dropdown-item">Other Codes</a></li>
            </ul>
        </li>

    </ul>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Login</a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->