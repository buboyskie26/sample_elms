<?php 
    require_once('includes/config.php');
    require_once('includes/classes/User.php');
    require_once('includes/classes/TeacherNavigationMenu.php');
    require_once('admin/classes/ButtonProvider.php');
    require_once('includes/classes/Teacher.php');
    

    $teacherLoggedIn = isset($_SESSION["teacherUserLoggedIn"]) 
        ? $_SESSION["teacherUserLoggedIn"] : "";
    
    $teacherUserLoggedInObj = new Teacher($con, $teacherLoggedIn);

    // echo "Login in as Teacher";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELMS_THESIS</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="assets/css/style.css">


    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    
	<script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/common.js"></script>



    <link rel="stylesheet" href="assets/css/TimeCircles.css">
    <script src="assets/js/TimeCircles.js"></script>

    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.css">
    <script src="assets/js/bootstrap-datetimepicker.js"></script>

  	<script src="https://cdn.jsdelivr.net/gh/guillaumepotier/Parsley.js@2.9.1/dist/parsley.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    

</head>
<body>
    
    <div id="pageContainer">
        <div id="mastHeadContainer">

            <button class="navShowHide">
                <img src="assets/images/icons/menu.png">
            </button>

            <a class="logoContainer" href="index.php">
                <!-- <img src="assets/images/icons/VideoTubeLogo.png" 
                title="logo" alt="Site logo"> -->
            </a>

            <div class="searchBarContainer">
                <form action="search.php" method="GET">
                    <input type="text" class="searchBar" 
                        name="term" placeholder="Search...">

                    <button class="searchButton">
                        <img src="assets/images/icons/search.png">
                    </button>
                </form>
            </div>

            <div class="rightIcons">
                <a href="upload.php">
                    <img class="upload" src="assets/images/icons/upload.png">
                </a>

                <?php
                    echo ButtonProvider::teacherProfileNav($con, $teacherLoggedIn);
                ?>
                <!-- <a href="adminLogin.php">
                    Sign In
                </a> -->
            </div>
        </div>

        <div id="sideNavContainer" style="display: block;">
        <!-- <div id="sideNavContainer" style="display: none;"> -->
            <?php
                $nav = new TeacherNavigationMenu($con, $teacherUserLoggedInObj);
                echo $nav->create();
            ?>
        </div>

        <div id="mainSectionContainer">
            <div id="mainContentContainer">
                
            