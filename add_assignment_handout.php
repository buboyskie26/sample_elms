<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/AddAssignment.php');
    require_once('includes/classes/SubjectPeriod.php');
    require_once('includes/classes/TeacherCourse.php');

    if(isset($_GET['subject_period_id']) 
        && isset($_SESSION['teacher_course_id'])
        && isset($_SESSION['subject_id'])){

        $subject_period_id = $_GET['subject_period_id'];
        $teacher_course_id = $_SESSION['teacher_course_id'];
        $subject_id = $_SESSION['subject_id'];

        // echo $subject_id;

        $subjectPeriod = new SubjectPeriod($con, $subject_period_id, $teacherUserLoggedInObj, "teacher");

        $assignment = new AddAssignment($con, $subjectPeriod,
            $teacherUserLoggedInObj, null);
    
        $createForm = $assignment->HandOutForm($subject_period_id, $teacher_course_id);
        
        echo "
            <div class='column'>
                $createForm
            </div>
        ";

        // echo $teacher_course_id;/

        if(isset($_POST['submit_add_assignment_handout'])){

            $imageArray = $_FILES['handout_file_location']['name'];

            $wasSuccessful = $assignment->AddHandoutAssignment(
                $imageArray,
                $_POST['handout_name'],
                $_POST['subject_period_id'],
                $_POST['subject_id'],
                $_POST['teacher_course_id'],
            );
            
            if($wasSuccessful){
                header("Location: assignment.php?teacher_course_id=$teacher_course_id");
            }
        }
         
    }


?>