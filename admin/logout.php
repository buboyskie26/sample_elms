<?php
    session_start();
    session_destroy();
    header("Location: /elms/adminLogin.php");
?>