<?php 

    class Gradebook{

    private $con, $teacherCourse, $teacherUserLoggedInObj;

    public function __construct($con, $teacherCourse, $teacherUserLoggedInObj)
    {
        $this->con = $con;
        $this->teacherCourse = $teacherCourse;
        $this->teacherUserLoggedInObj = $teacherUserLoggedInObj;
    }

    public function create(){

        $generateGradebookItems = $this->GenerateGradebookStudentList();
        
        return "
            <div style='width: 450px;' class='gradebook_inner'>
                $generateGradebookItems
            </div>
             
        ";
    }
    private function GenerateGradebookStudentList(){
        
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE (teacher_course_id=:teacher_course_id AND teacher_id=:teacher_id) AND deleted =''
            ORDER BY student_id DESC");
        
        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->execute();
        $subject_id = $this->teacherCourse->GetCourseSubjectId();
 
        $output = "
            <table class='table table-hover tb-left'>
                <thead>
                    <tr class='text-center'>
                        <th colspan='4'>Assignments</th>
                    </tr>
                    <tr style='text-align:right;'>
                         <th colspan='4'>Category</th>
                    </tr>
                    <tr style='text-align:right;'>
                        <th colspan='4'>Due</th>
                    </tr>
                    <tr>
                        <th>Students</th>
                        <th></th>
                        <th></th>
                        <th class='text-center'>Overall</th>
                    </tr>
                </thead>
            ";

        if($query->rowCount() > 0){
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                
                $student_id = $row['student_id'];
                $student = new Student($this->con, $row['student_id']);

                $fullName = $student->GetName();

                $output .= "
                     <tbody>
                        <tr>
                            <td>$fullName</td>
                            <td>
                                <a class='ssamp' style='display:inline-block;'href='gradebook.php?subj_id=$subject_id&tc_id=$teacher_course_id&student_id=$student_id'>
                                    <button class='btn btn-sm btn-success'>View</button>
                                </a>
                            </td>
                            <td></td>
                            <td>
                                87%|1.5
                            </td>
                        </tr>
                     </tbody>
                ";
            }
        }else{
            $output .= "qweqwe";
        }
            $output .= "</table>";
        
        return $output;
    }

    
    private function GenerateGradebookItems(){

        $subject_id = $this->teacherCourse->GetCourseSubjectId();
        // $subject_period_id = $this->GetSubjectPeriodId($subject_id);

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");

        $query->bindValue(":subject_id", $subject_id);
        $query->execute();


        $table = "<table class='table'>
                <tbody>
                    <tr>

            ";
        
        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $type_name = $row['type_name'];
            $subject_period_assignment_id = $row['subject_period_assignment_id'];

            $table .="
                <td>$type_name</td>
            ";
             
        }
        
        
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $query_two = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE (teacher_course_id=:teacher_course_id AND teacher_id=:teacher_id) AND deleted =''
            ORDER BY student_id DESC");
        
        $query_two->bindValue(":teacher_course_id", $teacher_course_id);
        $query_two->bindValue(":teacher_id", $teacher_id);
        $query_two->execute();
 
        
        $table .= "
                    </tr>
                </tbody>

            </table>
        ";
        return $table;
    }

    private function GetSubjectPeriodId($subject_id){

        $query_subject_period = $this->con->prepare("SELECT subject_period_id FROM subject_period
            WHERE subject_id=:subject_id");

        $query_subject_period->bindValue(":subject_id", $subject_id);
        $query_subject_period->execute();

        
        // Not loop so it was only one.
        return $query_subject_period->fetchColumn();
    }

   
    private function GetStudetPeriodAssignmentId($subject_period_assignment_id,
                $student_id){

        $query = $this->con->prepare("SELECT student_period_assignment_id FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND is_final=:is_final
            AND grade = 0");

        $query->bindValue(":is_final", "yes");
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();
        
        return $query->fetchColumn();
    }

    private function GetStudentCheckedAssignment($subject_period_assignment_id){

        // $student_id = $this->userLoggedInObj->GetId();
        $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : 0;

        // Student is not yet given a grade.
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND grade > 0
            ");
 
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        
        return $query->rowCount() > 0;
    }

    private function GetStudentPassedNotYetChecked($subject_period_assignment_id){

        // $student_id = $this->userLoggedInObj->GetId();
        $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : 0;

        // Student is not yet given a grade.
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND grade = 0
            AND is_final=:is_final
            ");
 
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":is_final", "yes");

        $query->execute();

        return  $query->rowCount();
    }

    private function GetStudentReachedDeadline($subject_period_assignment_id){

        $due_date = $this->GetDueDate($subject_period_assignment_id);
        
        // echo $due_date . " | ";
        $date_now = date("Y-m-d h:i:s");

        if($date_now > $due_date){
            return true;
        }
        return false;
    }
    private function GetStudentHasntReachedDeadline($subject_period_assignment_id){

        $due_date = $this->GetDueDate($subject_period_assignment_id);
        
        // echo $due_date . " | ";
        $date_now = date("Y-m-d h:i:s");

        // echo $due_date;
        // echo "<br>";
        // echo $date_now;
        // As long as due date is greater than equal to date now, it is valid
        // due_date = 5.2, date_now = 5.1, if due_Date is low, it is not valid,
        if($due_date >= $date_now){
            return true;
        }
        return false;
    }

    public function GetDueDate($subject_period_assignment_id){
        $subject_period_assignment = new SubjectPeriodAssignment($this->con,
            $subject_period_assignment_id, $this->teacherUserLoggedInObj);

        return $subject_period_assignment->GetDueDate();
        
    }

    private function GetStudentCheckedScore($subject_period_assignment_id){

        // $student_id = $this->userLoggedInObj->GetId();
        $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : 0;

        // Student is not yet given a grade.
        $query = $this->con->prepare("SELECT grade FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND grade > 0
            ");
 
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        return $query->fetchColumn();
    }


    public function GradeBookSummary(){

        $subject_id = $this->teacherCourse->GetCourseSubjectId();
        // $subject_period_id = $this->GetSubjectPeriodId($subject_id);

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");

        $query->bindValue(":subject_id", $subject_id);
        $query->execute();


        $table = "<table class='table'>
                <tbody>
                    <tr>
            ";
        
        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $type_name = $row['type_name'];
            $subject_period_assignment_id = $row['subject_period_assignment_id'];

            $table .= "
                <td>$type_name</td>
            ";
             
        }
        
        $queryv2 = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");

        $queryv2->bindValue(":subject_id", $subject_id);
        $queryv2->execute();
    

        $table .= "
                    </tr>
                
        ";
        //</tbody
        //<tbody
        $table .= "
                    <tr>
            ";
        while($row = $queryv2->fetch(PDO::FETCH_ASSOC)){

            $subject_period_assignment_id = $row['subject_period_assignment_id'];

            $table .="
                <td>$subject_period_assignment_id</td>
            ";
        }
        $table .= "
                    </tr>
        ";

        $queryv3 = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");

        $queryv3->bindValue(":subject_id", $subject_id);
        $queryv3->execute();

         $table .= "
            <tr>
        ";
        while($row = $queryv3->fetch(PDO::FETCH_ASSOC)){

            $max_score = 85;    
            $subject_period_assignment_id = $row['subject_period_assignment_id'];

            $isChecked = $this->GetStudentCheckedAssignment($subject_period_assignment_id);
            $notChecked = $this->GetStudentPassedNotYetChecked($subject_period_assignment_id);
            $reachedDeadline = $this->GetStudentReachedDeadline($subject_period_assignment_id);
            $notyetreachedDeadline = $this->GetStudentHasntReachedDeadline($subject_period_assignment_id);

            $result = 0;

            $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : 0;


            $student_period_assignment_id = $this->GetStudetPeriodAssignmentId($subject_period_assignment_id,
                $student_id);


            $btnGrade = "markGradebook(this,
                $student_period_assignment_id,$subject_period_assignment_id, $student_id)";

            echo $student_period_assignment_id;

            if($isChecked == true){
                $score = $this->GetStudentCheckedScore($subject_period_assignment_id, $student_id);
                $result = "
                    <span style='color:green;'>$score</span>
                ";
            }
            else if($notChecked == true){
                $result = "
                    <a style='
                        display: flex;
                        flex-direction: row;
                        height: 30px;' href='#'>
                        <input style='width: 50px;' id='gradebook_input' type='text' class='form-control'  >
                        <button style='width: 40px;' onclick='$btnGrade' class='btn btn-sm btn-success'>
                            <i class='fas fa-pencil'></i>
                        </button>
                    </a>
                ";
            }else if($reachedDeadline == true){
                $result = "
                    <i style='color: red;' class='fas fa-times'></i>
                ";
            }else if($notyetreachedDeadline == true){
                // student has a chance to pass
                $result = "
                    <i style='color: orange;' class='fas fa-flag'></i>
                ";
            }
           
            $table .="
                <td>$result</td>
            ";
        }

        $table .= "
            </tr>
        ";
        // Show specific student gradebook,
        // showing what are passed and not yet passed assignmen

        $table .= "
                </tbody>

            </table>
        ";
        return $table ;
    }

    public function add(){

        $left = $this->GenerateGradebookStudentListv2();
        $right = $this->GradeBookSummaryv2();

        $std =  $this->GetStudentOrderByLastName();
        
        foreach ($std as $key => $value) {
            # code...
            // echo $value;
        }
        return $left . $right;
    }
    public function GradeBookSummaryv2(){

        $subject_id = $this->teacherCourse->GetCourseSubjectId();
        $subject_period_id = $this->GetSubjectPeriodId($subject_id);

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");

        $query->bindValue(":subject_id", $subject_id);
        $query->execute();

        $table = "
            <table class='table tb-right'>
                <thead>
                    <tr  class='text-center'>
        ";

        // 1 Assignment Type Name
        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $type_name = $row['type_name'];
            $subject_period_assignment_id = $row['subject_period_assignment_id'];

            // $length = strlen($type_name);

            $qwe = explode(" ", $type_name);
            $size =  sizeof($qwe);

            // echo $size;

            $string = "Hello World";
            $delimiter = " ";

            $array = explode($delimiter, $string);
            
            // echo sizeof($array);
            $th = " 
                    <th>$type_name</th>
                ";
            if($size >= 3){
                $th = " 
                    <th style='min-width: 110px;'>$type_name</th>
                ";
            }
           
            $table .= "
                $th
            ";
            
            // array_push($array, $row['subject_period_assignment_id']);
        }

        $table .= "
            </tr>
        ";

        // $table .= "
        //         <tr class='text-center'>
        //     ";
        $spq1 = $this->con->prepare("SELECT * FROM subject_period_quiz
            WHERE subject_period_id=:subject_period_id");

        $spq1->bindValue(":subject_period_id", $subject_period_id);
        $spq1->execute();

        // while($row = $spq1->fetch(PDO::FETCH_ASSOC)){

        //     $quiz_title = $row['quiz_title'];

        //     $table .= "
        //         <th>$quiz_title</th>
        //     ";
        // }
        // $table .= "
        //     </tr>
        // ";

        //// 2 Assignment Type

        $table .= "
                <tr class='text-center'>
            ";
        $query_ass = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");
        $query_ass->bindValue(":subject_id", $subject_id);
        $query_ass->execute();
        while($row = $query_ass->fetch(PDO::FETCH_ASSOC)){

            $type_name = $row['type_name'];
            $assignment_type = $row['ass_type'];
            
            if($assignment_type === "Dropbox"){
                $assignment_type = "~";
            }
            $table .= "
                <th>$assignment_type</th>
            ";
        }
        $table .= "
            </tr>
        ";
        
        //// Assignment DueDate
        $table .= "
                <tr class='text-center'>
            ";
        $query_due = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");

        $query_due->bindValue(":subject_id", $subject_id);
        $query_due->execute();
        while($row = $query_due->fetch(PDO::FETCH_ASSOC)){

            $due_date = $row['due_date'];
            // $am_pm = date("g:i a", strtotime($due_date));

            $datex = date("M j", strtotime($due_date));
                // $datex = date("d-m-y", strtotime($date));
            // $datex .= " ".$am_pm;            
            $table .= "
                <th>$datex</th>
            ";
        }
        $table .= "
            </tr>
        ";

        //// MaxScore
        $table .= "
                <tr class='text-center'>
            ";
        $query_maxScore = $this->con->prepare("SELECT max_score FROM subject_period_assignment
            WHERE subject_id=:subject_id");
        $query_maxScore->bindValue(":subject_id", $subject_id);
        $query_maxScore->execute();

        while($row = $query_maxScore->fetch(PDO::FETCH_ASSOC)){

            $max_score = $row['max_score'];
            // $am_pm = date("g:i a", strtotime($due_date));

                // $datex = date("d-m-y", strtotime($date));
            // $datex .= " ".$am_pm;            
            $table .= "
                <th>$max_score</th>
            ";
        }
        $table .= "
            </tr>
        ";

        //// Users Data Gradebook
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $query_teacher_c = $this->con->prepare("SELECT student_id FROM teacher_course_student
            WHERE (teacher_course_id=:teacher_course_id AND teacher_id=:teacher_id)
            AND deleted =''
            ORDER BY student_id DESC");
        
        $query_teacher_c->bindValue(":teacher_course_id", $teacher_course_id);
        $query_teacher_c->bindValue(":teacher_id", $teacher_id);
        $query_teacher_c->execute();

        // Query the student with the same student_id of query_teacher_c
        // Studenet object must be place in the loop for Order by Surname.

        $student_idx = $this->ReturnStudentComingFromTeacherCourse();
        // $student_idx = $this->GetStudentOrderByLastName();

        
        // echo sizeof($student_idx);
        // echo "<br>";
        foreach ($student_idx as $key => $row_t) {
       

            $student_id = $row_t;
            $student = new Student($this->con, $student_id);

            // $student_id = $row_t['student_id'];
            // $student = new Student($this->con, $row_t['student_id']);

            $fullName = $student->GetFirstName();
            
            $queryv2 = $this->con->prepare("SELECT * FROM subject_period_assignment
                WHERE subject_id=:subject_id");

            $queryv2->bindValue(":subject_id", $subject_id);
            $queryv2->execute();

            $table .= "
                <tr class='text-center'>
            ";
            
            while($row = $queryv2->fetch(PDO::FETCH_ASSOC)){

                $subject_period_assignment_id = $row['subject_period_assignment_id'];

                $isChecked = $this->GetStudentCheckedAssignmentv2($subject_period_assignment_id,  $student_id);
                $notChecked = $this->GetStudentPassedNotYetCheckedv2($subject_period_assignment_id,  $student_id);
                $reachedDeadline = $this->GetStudentReachedDeadlinev2($subject_period_assignment_id,  $student_id);
                $notyetreachedDeadline = $this->GetStudentHasntReachedDeadlinev2($subject_period_assignment_id,$student_id);

                $subjectPeriodAssignment = new SubjectPeriodAssignment($this->con,
                    $subject_period_assignment_id, $this->teacherUserLoggedInObj);
                
                $assignment_type = $subjectPeriodAssignment->GetAssignmentType();
                $setTypeQuiz = $subjectPeriodAssignment->GetSetQuiz();

                $subject_period_assignment_quiz_class_id = $subjectPeriodAssignment->GetSubjectPeriodAssignmentQuizClassId();

                $quizHadTaken = $subjectPeriodAssignment->DoesQuizHadTaken($subject_period_assignment_quiz_class_id,
                    $student_id);

                // echo $subject_period_assignment_quiz_class_id;
                $quizScore = $subjectPeriodAssignment->GetStudentQuizClassTotalScore($subject_period_assignment_quiz_class_id,
                    $student_id);
                    
                $result = 0;

                // $student_id = isset($_GET[$student_id]) ? $_GET[$student_id] : 0;

                $student_period_assignment_id = $this->GetStudetPeriodAssignmentId($subject_period_assignment_id,
                    $student_id);

                $btnGrade = "markGradebook(this,
                    $student_period_assignment_id,
                    $subject_period_assignment_id,
                    $student_id)";

                // Quiz Gradebook. 
                if($assignment_type == "Quiz" && $setTypeQuiz == "yes")
                {
                    if($quizScore > 0){
                        $result = $quizScore;
                    }
                    else if($quizScore <= 0 && $quizHadTaken == true){
                        $result = $quizScore;
                    }
                    else if($quizScore <= 0 && $quizHadTaken == false){

                        // Quiz reached the deadline
                        if($reachedDeadline == true){
                            $result = "
                                <i style='color: orange;' class='fas fa-times'></i>
                            ";
                        }

                        // Student has a chance to pass.
                        else if($notyetreachedDeadline == true){
                            $result = "
                                <i style='color: orange;' class='fas fa-flag'></i>
                            ";
                        }
                       
                    }
                }

                if($isChecked == true && $assignment_type == "Dropbox"){
                    $score = $this->GetStudentCheckedScorev2($subject_period_assignment_id, $student_id);
                    $result = "
                        <span style='color:green;'>$score</span>
                    ";
                }
                else if($notChecked == true && $assignment_type == "Dropbox"){

		            $grade_input = "gradebook_input_$student_period_assignment_id";

                    $result = "
                        <a style='
                            display: flex;
                            flex-direction: row;
                            height: 25px;'>
                            <input style='width: 50px;' id='gradebook_input_'".$grade_input."'' type='text' class='form-control'  >

                            <button style='width: 40px;' onclick='$btnGrade' class='btn btn-sm btn-success'>
                                <i class='fas fa-pencil'></i>
                            </button>
                        </a>
                    ";
                }else if($reachedDeadline == true && $assignment_type == "Dropbox"){
                    $result = "
                        <i style='color: red;' class='fas fa-times'></i>
                    ";
                }else if($notyetreachedDeadline == true && $assignment_type == "Dropbox"){
                    // student has a chance to pass
                    $result = "
                        <i style='color: orange;' class='fas fa-flag'></i>
                    ";
                }


                echo $student_period_assignment_id;
                $table .= "<th>$fullName $result</th>";

            }
                
            $table .= "</tr>"; 
        }
        //

        $table .= "
                </thead>
            </table>
        ";
        
        return $table ;
    }
    private function GetStudentOrderByLastName(){

        $student_ids = $this->ReturnStudentComingFromTeacherCourse();

        // $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        // $teacher_id = $this->teacherUserLoggedInObj->GetId();

        // $student_id = 0;

        if(sizeof($student_ids) > 0){
            $i=0;
            $condition = "";

            while($i < sizeof($student_ids)){
                if($i == 0){
                    $condition .= "WHERE student_id=?";
                }else{
                    $condition .= " OR student_id=?";
                }
                $i++;
            }

            $query = $this->con->prepare("SELECT * FROM student
                $condition
                ORDER BY lastname DESC");

            $j = 1;

            foreach ($student_ids as $key => $student_id) {
                $query->bindValue($j, $student_id);
                $j++;
            }

            $query->execute();

            $array = [];
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                array_push($array, $row['student_id']);
            }

            return $array;
        }
        
    }
    private function ReturnStudentComingFromTeacherCourse(){

            $arr = [];
            $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
            $teacher_id = $this->teacherUserLoggedInObj->GetId();

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

    private function GenerateGradebookStudentListv2(){
        
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE (teacher_course_id=:teacher_course_id AND teacher_id=:teacher_id) AND deleted =''
            ORDER BY student_id DESC");
        
        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->execute();
        $subject_id = $this->teacherCourse->GetCourseSubjectId();
 
        $output = "
            <table class='table table-hover tb-left'>
                <thead>
                    <tr class='text-center'>
                        <th colspan='4'>Assignments</th>
                    </tr>
                    <tr style='text-align:right;'>
                         <th colspan='4'>Category</th>
                    </tr>
                    <tr style='text-align:right;'>
                        <th colspan='4'>Due</th>
                    </tr>
                    <tr>
                        <th>Students</th>
                        <th></th>
                        <th></th>
                        <th class='text-center'>Overall</th>
                    </tr>
                </thead>
            ";

        $student_idx = $this->GetStudentOrderByLastName();

        if($query->rowCount() > 0){
            $i=0;
          
            // foreach ($student_idx as $key => $value) {

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                
                // $student = new Student($this->con, $value);

                $student_id = $row['student_id'];
                $student = new Student($this->con, $row['student_id']);

                $fullName = $student->GetName();

                $output .= "
                     <tbody>
                        <tr>
                            <td>$fullName</td>
                            <td>
                                 
                            </td>

                            <td></td>

                            <td>
                                87%|1.5
                            </td>
                        </tr>
                     </tbody>
                ";
            }
        }else{
            $output .= "qweqwe";
        }
            $output .= "</table>";
        
        return $output;
    }
    //

    private function GetStudentCheckedAssignmentv2($subject_period_assignment_id, $student_id){

        // $student_id = $this->userLoggedInObj->GetId();
        // $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : 0;

        // Student is not yet given a grade.
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND grade > 0
            ");
 
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        
        return $query->rowCount() > 0;
    }
    private function GetStudentPassedNotYetCheckedv2($subject_period_assignment_id, $student_id){

        // $student_id = $this->userLoggedInObj->GetId();
        // $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : 0;

        // Student is not yet given a grade.
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND grade = 0
            AND is_final=:is_final
            ");
 
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":is_final", "yes");

        $query->execute();

        return  $query->rowCount();
    }

    private function GetStudentReachedDeadlinev2($subject_period_assignment_id, $student_id){

        $due_date = $this->GetDueDate($subject_period_assignment_id);
        
        // echo $due_date . " | ";
        $date_now = date("Y-m-d h:i:s");

        if($date_now > $due_date){
            return true;
        }
        return false;
    }
    private function GetStudentHasntReachedDeadlinev2($subject_period_assignment_id, $student_id){

        $due_date = $this->GetDueDate($subject_period_assignment_id);
        
        // echo $due_date . " | ";
        $date_now = date("Y-m-d h:i:s");

        // echo $due_date;
        // echo "<br>";
        // echo $date_now;
        // As long as due date is greater than equal to date now, it is valid
        // due_date = 5.2, date_now = 5.1, if due_Date is low, it is not valid,
        if($due_date >= $date_now){
            return true;
        }
        return false;
    }

    public function GetDueDatev2($subject_period_assignment_id, $student_id){
        $subject_period_assignment = new SubjectPeriodAssignment($this->con,
            $subject_period_assignment_id, $this->teacherUserLoggedInObj);

        return $subject_period_assignment->GetDueDate();
        
    }

    private function GetStudentCheckedScorev2($subject_period_assignment_id, $student_id){

        // $student_id = $this->userLoggedInObj->GetId();

        // Student is not yet given a grade.
        $query = $this->con->prepare("SELECT grade FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND grade > 0
            ");
 
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        return $query->fetchColumn();
    }
    //


}


?>