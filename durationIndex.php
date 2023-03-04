<?php

    require_once('includes/studentHeader.php');


    echo "Duration Index ";
    if(isset($_SESSION['end_time'])){
        $end_time = $_SESSION['end_time'];
    }
?>


<div id='response'></div>

<script>

setInterval(function() {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.open("GET", "response.php" ,false);
    xmlHttp.send(null);
    document.getElementById("response").innerHTML = xmlHttp.responseText;

}, 1000);
 
</script>






