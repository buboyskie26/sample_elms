<?php 
    require_once('includes/config.php');
    require_once('includes/classes/User.php');
    // require_once('includes/classes/AdminNavigationMenu.php');
    require_once('admin/AdminNavigationMenu.php');
    

    $usernameLoggedIn = isset($_SESSION["userLoggedIn"]) 
        ? $_SESSION["userLoggedIn"] : "";
    
    $userLoggedInObj = new User($con, $usernameLoggedIn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELMS_THESIS</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> 

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/common.js"></script>
</head>
<body>
    
    <div id="pageContainer">
        <div id="mastHeadContainer">

            <button class="navShowHide">
                <img src="assets/images/icons/menu.png">
            </button>

            <a class="logoContainer" href="index.php">
                <img src="assets/images/icons/VideoTubeLogo.png" 
                title="logo" alt="Site logo">
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
                <a href="adminLogin.php">
                    Sign In
                </a>
            </div>
        </div>

        <div id="sideNavContainer" style="display: block;">
        <!-- <div id="sideNavContainer" style="display: none;"> -->
            <?php
                $nav = new AdminNavigationMenu($con, $userLoggedInObj);
                echo $nav->create();
            ?>
        </div>

        <div id="mainSectionContainer">
            <div id="mainContentContainer">
                
            