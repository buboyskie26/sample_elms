<?php

    require_once('includes/studentHeader.php');
    require_once('includes/classes/StudentAssignment.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/SubjectPeriod.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');
    require_once('includes/classes/SubjectPeriodQuiz.php');
    require_once('includes/classes/SubjectPeriodQuizClass.php');
    require_once('includes/classes/SubjectPeriodAssignmentHandout.php');
    require_once('includes/classes/StudentPeriodAssignment.php');
    
    
    if(isset($_GET['teacher_course_id'])){
        $teacher_course_id = $_GET['teacher_course_id'];

        $teacherCourse = new TeacherCourse($con, $teacher_course_id,
            $studentLoggedIn);

        $student_assignment = new StudentAssignment($con, $teacherCourse,
            $studentUserLoggedInObj);

        $create = $student_assignment->create();
        // $progress = $student_assignment->createProgress();

        $_SESSION['teacher_course_id'] = $teacher_course_id;

        $query_teacher_course = $con->prepare("SELECT teacher_id FROM teacher_course
                WHERE teacher_course_id=:teacher_course_id
                LIMIT 1");

        $query_teacher_course->bindValue(":teacher_course_id", $teacher_course_id);
        $query_teacher_course->execute();
        $teacher_id = $query_teacher_course->fetchColumn();

        $groupChatBtn = "";

        if($teacher_id != 0 && $teacher_course_id != 0){

            $check_gc = $con->prepare("SELECT group_chat_id FROM group_chat
                WHERE teacher_course_id=:teacher_course_id
                AND teacher_id=:teacher_id
                LIMIT 1");

            $check_gc->bindValue(":teacher_course_id", $teacher_course_id);
            $check_gc->bindValue(":teacher_id", $teacher_id);
            $check_gc->execute();


            if($check_gc->rowCount()  == 1){

                $group_chat_id = $check_gc->fetchColumn();

                $_SESSION['group_chat_id'] = $group_chat_id;

                $groupChatBtn = "
                    <a href='group_chat.php'>
                        <button class='btn btn-sm btn-success'>Group Chat </button>
                    </a>
                ";
            }
        }
       


        echo "
            <div class='lesson_section'>
                <div class='assignment_top'>
                    <div style='text-align: right;'>
                       
                        $groupChatBtn
                        <a href='student_teacher_message.php'>
                            <button class='btn btn-sm btn-success'>Message Teacher</button>
                        </a>
                    </div>
                    <div class='assignment_top_progress'>
                     
                    </div>
                </div>
                <div class='assignment_section'>
                    $create
                </div>
            </div>
            
        ";
    }

   
?>

<!-- <script src="assets/js/common.js"></script> -->

