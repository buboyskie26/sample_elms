<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/DashboardTeacher.php');
    require_once('includes/classes/Teacher.php');
    require_once('includes/classes/DashboardTeacherItem.php');
    require_once('includes/classes/TeacherCourse.php');

    // echo "for teacher section ";
    // echo $_SESSION['teacherUserLoggedIn'];

    $dashboard = new DashboardTeacher($con, $teacherUserLoggedInObj);

    $teacher = new Teacher($con, $teacherLoggedIn);
    $id = $teacher->getId();
    
    if(isset($_POST['submit_teacher_course'])){
        $wasSuccessful = $dashboard->insertTeacherCourse(
            $_POST['course_id'],
            $teacher->getId(),
            $_POST['subject_id'],
            $_POST['school_year_id'],
            $_FILES['thumbnail'],
        );
        if($wasSuccessful){
            header("Location: dashboard_teacher.php");
        }
    }

    

?>
<div class="dashboard_section">
    <div class='left'>
        <?php

            if(isset($_GET['search_school_year'])){
                $search_school_year = $_GET['search_school_year'];

                echo $dashboard->createTeacherDashboardSearch($search_school_year);
                // exit();
            }
            
            // Show up the Active status in SChool Year at default.
            // Note: If the Id didnt have correspoinding ACtive Status
            // The column IsActive with Latest School Id have been modified.
            $latest = $dashboard->GetLatestSchoolYearAndPeriod();
            $latestSchoolYear = $latest->fetchColumn(0);
            $latestSchoolYearStatus = $dashboard->GetLatestSchoolYearAndPeriodStatus();

            if(!isset($_POST['search_school_year']) && !isset($_GET['search_school_year'])){
                echo $dashboard->create($latestSchoolYear);
            }

            // Responsible for search school year term.
            if(isset($_POST['search_school_year']) && !isset($_GET['search_school_year'])){
                echo $dashboard->ShowingSelectedSchoolYear($_POST['school_year_id'], $latestSchoolYear);
            }

        ?>
    </div>
    <div class="right">
        <?php
            echo $dashboard->showSchoolYearForm();
        ?>
    </div>
</div>