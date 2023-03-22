<?php

    class Assignment{

    private $con, $teacherCourse, $teacherUserLoggedInObj;

    public function __construct($con, $teacherCourse, $teacherUserLoggedInObj)
    {
        $this->con = $con;
        $this->teacherCourse = $teacherCourse;
        $this->teacherUserLoggedInObj = $teacherUserLoggedInObj;
    }

    public function create(){

        $subject_period_button = $this->createSubjectPeriodButtons();

        $generateSubjectPeriod = $this->GenerateSubjectPeriod();

        return "
            $subject_period_button
           
            <div class='subjectPeriodContainer'>
                $generateSubjectPeriod
            </div>
        ";
    }
    
    private function GenerateSubjectPeriod(){

        $subjectId = $this->teacherCourse->GetSubjectId();
        $teacherCourseId = $this->teacherCourse->GetTeacherCourseId();
        // query
        // create an order by according to the wants of teacher.
        $query= $this->con->prepare("SELECT * FROM subject_period
            WHERE subject_id=:subject_id
            AND teacher_course_id=:teacher_course_id
            
            ");
        
        $query->bindValue(":subject_id", $subjectId);
        $query->bindValue(":teacher_course_id", $teacherCourseId);
        $query->execute();

        $output = "";

        if($query->rowCount() > 0){
             while($row = $query->fetch(PDO::FETCH_ASSOC)){
                // 1st
                $subjectPeriod = new SubjectPeriod($this->con, $row,
                    $this->teacherUserLoggedInObj, "teacher");

                $output .= $subjectPeriod->create($teacherCourseId);
            }
        }else{
            $output = "The Teacher Course Admin setted to you is not set. Please Set it now.";
        }
       
        return $output;
    }

    private function createSubjectPeriodButtons(){

        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $_SESSION['teacher_course_id'] = $teacher_course_id;
        
        $createGroupChat = "createGroupChat($teacher_course_id, $teacher_id)";

        // Check if group chat that the teacher`s teacher_course have been added.

        $checkGroupChatExist = $this->con->prepare("SELECT group_chat_id FROM group_chat
                WHERE teacher_course_id=:teacher_course_id
                AND teacher_id=:teacher_id
                LIMIT 1");

        $checkGroupChatExist->bindValue(":teacher_course_id", $teacher_course_id); 
        $checkGroupChatExist->bindValue(":teacher_id", $teacher_id); 
        $checkGroupChatExist->execute(); 

        $gc_btn = "";
        if($checkGroupChatExist->rowCount() == 0){
            $gc_btn = "
                <button onclick='$createGroupChat' 
                    class='btn btn-sm btn-success'>Add GC
                </button>
        "   ;
        }else if($checkGroupChatExist->rowCount() > 0){
            $group_chat_id = $checkGroupChatExist->fetchColumn();

            $_SESSION['teacher_groupchat_id'] = $group_chat_id;

            $gc_btn = "
                <a href='teacher_groupchat.php'>
                    <button class='btn btn-sm btn-success'>
                        Course Chat
                    </button>
                </a>
            ";
        }

        // Inefficient
        // <a href='quiz_student.php'>
        //     <button class='btn btn-sm btn-outline-primary'>Quizzes</button>
        // </a>
        // <a href='handout_student.php'>
        //     <button class='btn btn-sm btn-outline-success'>Handouts</button>
        // </a>
        // <a href='assignment_prelim.php?teacher_course_id=$teacher_course_id&term=prelim'>
        //     <button class='btn btn-sm btn-success'>Prelim</button>
        // </a>
        // <a href='assignment_prelim.php?teacher_course_id=$teacher_course_id&term=midterm'>
        //     <button class='btn btn-sm btn-primary'>Midterm
        //     </button>
        // </a>
        // <a href='assignment_prelim.php?teacher_course_id=$teacher_course_id&term=pre-final'>
        //     <button class='btn btn-sm btn-secondary'>Pre-Finals
        //     </button>
        // </a>
        // <a href='assignment_prelim.php?teacher_course_id=$teacher_course_id&term=final'>
        //     <button class='btn btn-sm btn-success'>Finals
        //     </button>
        // </a>
        //
        return "
            <div class='assignment_buttons'>

                $gc_btn
                <a href='add_subject_period.php'>
                    <button class='btn btn-success btn-sm'>
                        Add Subject Period
                    </button>
                </a>
                
            </div>
        ";
    }
   
    public function AddSubjectTerm($term,$title,$description,$subject_id, $teacher_course_id){

        $query = $this->con->prepare("INSERT INTO subject_period(term,title,
                description, subject_id, teacher_course_id)
            VALUES(:term,:title,:description, :subject_id, :teacher_course_id)");
        
        $query->bindValue(":term", $term);
        $query->bindValue(":title", $title);
        $query->bindValue(":description", $description);
        $query->bindValue(":subject_id", $subject_id);
        $query->bindValue(":teacher_course_id", $teacher_course_id);
       
        return $query->execute();
    }
    public function insertTeacherCourse($course_id, $teacher_id, $subject_id,
            $school_year){

        $query = $this->con->prepare("INSERT INTO teacher_course(course_id,teacher_id, subject_id, school_year)
            VALUES(:course_id, :teacher_id, :subject_id, :school_year)");
        
        $query->bindValue(":course_id", $course_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->bindValue(":subject_id", $subject_id);
        // $query->bindValue(":thumbnail", $thumbnail);
        $query->bindValue(":school_year", $school_year);

        return $query->execute();
    }
    public function createForm($term){
        
        $createCourseCategory = $this->createCourseCategory();
        $createSubjectCategory = $this->createSubjectCategory();
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $subject_id = $this->teacherCourse->GetCourseSubjectId();

        $output = "";

        if($term == "prelim"){
            $output = "
            <form action='assignment_prelim.php?teacher_course_id=$teacher_course_id&term=prelim' method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        
                        <input value='Prelim' type='hidden' name='term'>
                        <input class='form-control' type='text' 
                            placeholder='Subject Title' name='title'>
                        <textarea class='form-control' name='description' placeholder='Description'></textarea>
                        <input name='subject_id' type='hidden' value='$subject_id'>

                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_assignment_prelim'>Save</button>
                </form>
            ";
        }
        else if($term == "midterm"){
            $output = "
            <form action='assignment_prelim.php?teacher_course_id=$teacher_course_id&term=midterm' method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        
                        <input value='Midterm' type='hidden' name='term'>
                        <input class='form-control' type='text' 
                            placeholder='Subject Title' name='title'>
                        <textarea class='form-control' name='description' placeholder='Description'></textarea>
                        <input name='subject_id' type='hidden' value='$subject_id'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_assignment_prelim'>Save</button>
                </form>
            ";
        }
        else if($term == "pre-final"){
            $output = "
            <form action='assignment_prelim.php?teacher_course_id=$teacher_course_id&term=pre-final' method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        
                        <input value='Midterm' type='hidden' name='term'>
                        <input class='form-control' type='text' 
                            placeholder='Subject Title' name='title'>
                        <textarea class='form-control' name='description' placeholder='Description'></textarea>
                        <input name='subject_id' type='hidden' value='$subject_id'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_assignment_prelim'>Save</button>
                </form>
            ";
        }
        
        return $output;

        // return "
        //     <form action='assignment_prelim.php?teacher_course_id=$teacher_course_id' method='POST' enctype='multipart/form-data'>
        //             <div class='form-group'>
                        
        //                 <input value='Prelim' type='hidden' name='term'>
        //                 <input class='form-control' type='text' 
        //                     placeholder='Subject Title' name='title'>
        //                 <textarea class='form-control' name='description' placeholder='Description'></textarea>
        //                 <input name='subject_id' type='hidden' value='$subject_id'>
        //             </div>

        //             <button type='submit' class='btn btn-primary' name='submit_assignment_prelim'>Save</button>
        //         </form>
        // ";
    }
    private function createCourseCategory(){

        $query = $this->con->prepare("SELECT * FROM course");
        $query->execute();

            $html = "<div class='form-group'>
                    <select class='form-control' name='course_id'>";

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $html .= "
                    <option value='".$row['course_id']."'>".$row['course_name']."</option>
                ";
            }
            $html .= "</select>
                    </div>";
            return $html;
    }
    private function createSubjectCategory(){

        $query = $this->con->prepare("SELECT * FROM subject");
        $query->execute();

            $html = "<div class='form-group'>
                    <select class='form-control' name='subject_id'>";

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $html .= "
                    <option value='".$row['subject_id']."'>".$row['subject_title']."</option>
                ";
            }
            $html .= "</select>
                    </div>";
            return $html;
    }


    public function createStudentsQuizView(){

        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();

        $table = "
            <table class='table tb-right'>
                <thead>
                    <tr  class='text-center'>
        ";

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE teacher_course_id=:teacher_course_id
            AND ass_type=:ass_type
            AND set_quiz=:set_quiz
            ORDER BY subject_period_id ASC
            ");

        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->bindValue(":ass_type", "Quiz");
        $query->bindValue(":set_quiz", "yes");
        $query->execute();

        if($query->rowCount() > 0){

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $type_name = $row['type_name'];
                $subject_period_id = $row['subject_period_id'];
                // $subject_period_assignment_handout_id = $row['subject_period_assignment_handout_id'];

                $subjectPeriod = new SubjectPeriod($this->con, $subject_period_id,
                    $this->teacherUserLoggedInObj, "teacher");

                $periodName = $subjectPeriod->GetTerm();
                $table .= "
                    <th>$type_name ($periodName) $subject_period_id</th>
                ";
            }
        }
        $table .= "
            </tr>
        ";
        
        $student_idx = $this->ReturnStudentComingFromTeacherCourse();

        $table .= "
            <tr class='text-center'>
         ";

        // foreach ($student_idx as $key => $student_ids) {
        //     $queryForScore = $this->con->prepare("SELECT * FROM subject_period_assignment
        //         WHERE teacher_course_id=:teacher_course_id
        //         AND ass_type=:ass_type
        //         AND set_quiz=:set_quiz
        //         ORDER BY subject_period_id ASC");

        //     $queryForScore->bindValue(":teacher_course_id", $teacher_course_id);
        //     $queryForScore->bindValue(":ass_type", "Quiz");
        //     $queryForScore->bindValue(":set_quiz", "yes");

        //     if($query->rowCount() > 0){

        //         while($row = $queryForScore->fetch(PDO::FETCH_ASSOC)){

        //         }
        //     }
        // }
       

        $table .= "
            </tr>
        ";


        foreach ($student_idx as $key => $row_t) {
            // $student_id = $row['student_id'];

            $student = new Student($this->con, $row_t);
            $fullName = $student->GetName();
          
            $query = $this->con->prepare("SELECT * FROM subject_period_assignment
                WHERE teacher_course_id=:teacher_course_id
                AND ass_type=:ass_type
                AND set_quiz=:set_quiz
                ORDER BY subject_period_id ASC");

            $query->bindValue(":teacher_course_id", $teacher_course_id);
            $query->bindValue(":ass_type", "Quiz");
            $query->bindValue(":set_quiz", "yes");
            $query->execute();

            // echo $queryHandoutView->fetchColumn();

            $table .= "
                <tr class='text-center'>
            ";

            if($query->rowCount() > 0){

                while($row = $query->fetch(PDO::FETCH_ASSOC)){

                    $subject_period_id = $row['subject_period_id'];
                    $subject_period_assignment_id = $row['subject_period_assignment_id'];

                    $subjectPeriodAssignment = new SubjectPeriodAssignment($this->con,
                        $subject_period_assignment_id, $this->teacherUserLoggedInObj);

                    $subject_period_assignment_quiz_class_id = $subjectPeriodAssignment->GetSubjectPeriodAssignmentQuizClassId();


                    $queryStudentQuizAns = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
                        WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
                        AND student_id=:student_id
                        ");

                    $queryStudentQuizAns->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
                    $queryStudentQuizAns->bindValue(":student_id", $row_t);
                    $queryStudentQuizAns->execute();

                    $result = "";
                    $score = 0;

                    if($queryStudentQuizAns->rowCount() > 0){
                        $queryStudentQuizAns = $queryStudentQuizAns->fetch(PDO::FETCH_ASSOC);
                        $result = "
                            <i class='fas fa-check' style='color: green;'></i>
                        ";
                        $score = $queryStudentQuizAns['total_score'];
                    }else{
                        $result = "
                            <i class='fas fa-times' style='color: orange;'></i>
                        ";
                    }
 
                    
                    // $subject_period_assignment_quiz_class_id [$subject_period_assignment_id 
                    $table .= "<th>$fullName $result $score</th>";
                }
            }
            $table .= "</tr>"; 
        }


        return $table;
    }
    private function Get(){

    }
    public function createStudentsHandoutView(){

        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();

        $table = "
            <table class='table tb-right'>
                <thead>
                    <tr  class='text-center'>
        ";

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
            WHERE teacher_course_id=:teacher_course_id
            ORDER BY subject_period_id ASC
            ");

        // $query = $this->con->prepare("SELECT * FROM subject_period_assignment_handout as sh
        //     INNER JOIN handout_viewed as hv ON hv.subject_period_assignment_handout_id = sh.subject_period_assignment_handout_id
        //     -- AND teacher_course_id=:teacher_course_id
        //     ");

        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->execute();

        if($query->rowCount() > 0){

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $handout_name = $row['handout_name'];
                $subject_period_id = $row['subject_period_id'];
                $subject_period_assignment_handout_id = $row['subject_period_assignment_handout_id'];

                $subjectPeriod = new SubjectPeriod($this->con, $subject_period_id,
                    $this->teacherUserLoggedInObj, "teacher");

                $periodName = $subjectPeriod->GetTerm();
                $table .= "
                    <th>$handout_name ($periodName)</th>
                ";
            }
        }
        $table .= "
            </tr>
        ";

        $student_idx = $this->ReturnStudentComingFromTeacherCourse();

        foreach ($student_idx as $key => $row_t) {
            // $student_id = $row['student_id'];

            $student = new Student($this->con, $row_t);
            $fullName = $student->GetName();

            $querySubHandout = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
                WHERE teacher_course_id=:teacher_course_id
                ORDER BY subject_period_id ASC
                
                ");
            //
            $querySubHandout->bindValue(":teacher_course_id", $teacher_course_id);
            $querySubHandout->execute();

            $table .= "
                <tr class='text-center'>
            ";

            if($querySubHandout->rowCount() > 0){

                while($row = $querySubHandout->fetch(PDO::FETCH_ASSOC)){

                    $subject_period_assignment_handout_id = $row['subject_period_assignment_handout_id'];

                    $result = "";
                    $viewedStudentId = $this->GetViewedStudentId($subject_period_assignment_handout_id,
                        $row_t);

                    $doeStudentViewed = $this->CheckIfViewed($subject_period_assignment_handout_id, $viewedStudentId);

                    if($viewedStudentId == $row_t){
                        $result = "
                            <i class='fas fa-check' style='color: green;'></i>
                        ";
                    }else{
                        $result = "
                            <i class='fas fa-times' style='color: orange;'></i>
                        ";
                    }
                    $table .= "<th>$fullName $result</th>";
                }
            }

            $table .= "</tr>"; 

        }

        

        

        // $queryHandoutView = $this->con->prepare("SELECT * FROM handout_viewed
        //     WHERE teacher_course_id=:teacher_course_id
        //     -- AND subject_period_assignment_handout_id=:subject_period_assignment_handout_id
        //     ");
        // $queryHandoutView->bindValue(":teacher_course_id", $teacher_course_id);
        // // $queryHandoutView->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
        // $queryHandoutView->execute();

        // if($queryHandoutView->rowCount() > 0){

        //     while($row = $queryHandoutView->fetch(PDO::FETCH_ASSOC)){

        //         $student_id = $row['student_id'];

        //         $student = new Student($this->con, $student_id);
        //         $student_name = $student->GetName();

        //         // $table .= "<th>$student_name</th>";
                
        //     }
        // }
       

        $table .= "
            </tr>
        ";


        $table .= "
                </thead>
            </table>
        ";

        return $table;
    }

    private function GetSubjectPeriod(){

    }
    private function CheckIfViewed($subject_period_assignment_handout_id, $student_id){

        $querySubHandoutView = $this->con->prepare("SELECT student_id FROM handout_viewed
            WHERE subject_period_assignment_handout_id=:subject_period_assignment_handout_id
            AND student_id=:student_id
            -- AND 
                ");

        $querySubHandoutView->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
        $querySubHandoutView->bindValue(":student_id", $student_id);
        $querySubHandoutView->execute();

        return $querySubHandoutView->fetchColumn();
    }
    private function GetViewedStudentId($subject_period_assignment_handout_id, $student_id){

        $querySubHandoutView = $this->con->prepare("SELECT student_id FROM handout_viewed
            WHERE subject_period_assignment_handout_id=:subject_period_assignment_handout_id
            AND student_id=:student_id
                ");

        $querySubHandoutView->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
        $querySubHandoutView->bindValue(":student_id", $student_id);
        $querySubHandoutView->execute();

        return $querySubHandoutView->fetchColumn();

    }   
    private function ReturnStudentComingFromTeacherCourse(){

        $arr = [];
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherCourse->GetTeacherCourseTeacherId();

        $query_teacher_c = $this->con->prepare("SELECT student_id FROM teacher_course_student
            WHERE (teacher_course_id=:teacher_course_id AND teacher_id=:teacher_id)
            AND deleted =''
            ORDER BY student_id DESC");
        
        $query_teacher_c->bindValue(":teacher_course_id", $teacher_course_id);
        $query_teacher_c->bindValue(":teacher_id", $teacher_id);
        $query_teacher_c->execute();

        if($query_teacher_c->rowCount() > 0){
            while($row = $query_teacher_c->fetch(PDO::FETCH_ASSOC)){
                array_push($arr, $row['student_id']);
            }
        }
        return $arr;
    }
}
?>