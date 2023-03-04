<?php  
 
    include('../includes/classes/Department.php');
    include('./adminHeader.php');

    $department = new Department($con, $adminLoggedInObj);

    if(isset($_POST['submit_department'])){
        
        $wasSuccessful = $department->insertDepartment($_POST['department_name'],
            $_POST['dean']);
    }
   
?>

<div class="column">
    <?php
        echo $department->createForm();
    ?>
</div>

<?php  include('../includes/footer.php');?>