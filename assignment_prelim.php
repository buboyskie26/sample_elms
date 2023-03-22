<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/Assignment.php');
    require_once('includes/classes/TeacherCourse.php');
    
    if(isset($_GET['teacher_course_id'])){
        $teacher_course_id = $_GET['teacher_course_id'];
    }

    $term = "";

    if(isset($_GET['term']) && $_GET['term'] == 'prelim'){
        $term = "prelim";
    }
    else if(isset($_GET['term']) && $_GET['term'] == 'midterm'){
        $term = "midterm";
    }
    else if(isset($_GET['term']) && $_GET['term'] == 'pre-final'){
        $term = "pre-final";
    }
    else if(isset($_GET['term']) && $_GET['term'] == 'final'){
        $term = "final";
    }
    if(isset($_SESSION['teacher_course_id'])) {
        $teacher_course_id = $_SESSION['teacher_course_id'];
        echo $teacher_course_id;
    }
    // echo "for teacher section ";
    // echo $_SESSION['teacherUserLoggedIn'];

    $teacher_course = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);
    $assignment = new Assignment($con, $teacher_course, $teacherUserLoggedInObj);
    
    if(isset($_POST['submit_assignment_prelim'])){
        // Rewrite in another function.

        $additional = "";
        if($term == "prelim"){
            $additional = " (Prelim)";
        }
        else if($term == "midterm"){
            $additional = " (Midterm)";
        }
        else if($term == "pre-final"){
            $additional = " (Pre-Final)";
        }
        else if($term == "final"){
            $additional = " (Final)";
        }

        $POST_TITLE = $_POST['title'];
        $POST_TITLE = $POST_TITLE . $additional;

        $wasSuccessful = $assignment->AddSubjectTerm
            (
                $_POST['term'],
                $POST_TITLE,
                $_POST['description'],
                $_POST['subject_id'],
                $teacher_course_id
            );

        header("Location: assignment.php?teacher_course_id=$teacher_course_id");
    }

?>


<div class="column">
    <?php
        echo $assignment->createForm($term);
    ?>
</div>