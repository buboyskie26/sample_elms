<?php 

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/AddAssignmentQuiz.php');
    require_once('includes/classes/SubjectPeriod.php');


    if(isset($_SESSION['subject_id']) && isset($_GET['subject_period_id'])
        && isset($_SESSION['teacher_course_id'])){
        

        $subject_id = $_SESSION['subject_id'];
        $subject_period_id = $_GET['subject_period_id'];
        $teacher_course_id = $_SESSION['teacher_course_id'];

        $subjectPeriod = new SubjectPeriod($con, $subject_period_id, $teacherUserLoggedInObj, "teacher");

        $assignment = new AddAssignmentQuiz($con, $subjectPeriod, $teacherUserLoggedInObj);

        $createQuiz = $assignment->createFormQuiz($teacher_course_id);
        $create = $assignment->createTable();

        echo "
            <div class='column'>
                $create
            </div>
        ";
        
    }else{
        echo "No Session Or invalid subject_id";
        exit();
    }


?>