<?php 
    require_once('includes/teacherHeader.php');
    require_once('includes/classes/Gradebook.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');


    if(isset($_GET['teacher_course_id'])){
        $teacher_course_id = $_GET['teacher_course_id'];

        $teacherCourse = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);

        $gradebook = new Gradebook($con, $teacherCourse, $teacherUserLoggedInObj);
        $create = $gradebook->create();
        $add = $gradebook->add();

        // echo "
        //     <div class='gradebook_section'>
        //         $create 
        //     </div>
        // ";
        echo "
            <div class='gradebook_summary'>
                $add 
            </div>
        ";
    }

    if(isset($_GET['subj_id']) && isset($_GET['tc_id']) && isset($_GET['student_id'])){

        $student_id = $_GET['student_id'];
        $subject_id = $_GET['subj_id'];
        $teacher_course_id = $_GET['tc_id'];

        $teacherCourse = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);
        $gradebook = new Gradebook($con, $teacherCourse, $teacherUserLoggedInObj);

        $createGradeBook = $gradebook->GradeBookSummaryv2();


        $add = $gradebook->add();
        echo "
            <div style='width: 100%' class='gradebook_summary'>
                $add 
            </div>
        ";
    }

?>




