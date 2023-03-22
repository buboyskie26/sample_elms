<?php

    class SubjectPeriodAssignment{

    private $con, $userLoggedInObj, $sqlData;

    // $input = subject_period_assignment OBJECT
    public function __construct($con, $input, $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->sqlData = $input;

        // $input SHOULD BE subject_period_assignment_id if it is not subject_period_assignment object
        if(!is_array($input)){
            $query = $this->con->prepare("SELECT * FROM subject_period_assignment
                WHERE subject_period_assignment_id = :subject_period_assignment_id");

            $query->bindParam(":subject_period_assignment_id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

    }
    public function GetTypeName(){
        return isset($this->sqlData['type_name']) ? $this->sqlData["type_name"] : ""; 
    }
 
    public function GetMaxSubmission(){
        return isset($this->sqlData['max_submission']) ? $this->sqlData["max_submission"] : ""; 
    }
    public function GetViewed(){
        return isset($this->sqlData['viewed']) ? $this->sqlData["viewed"] : ""; 
    }
    public function GetDueDate(){
        return isset($this->sqlData['due_date']) ? $this->sqlData["due_date"] : ""; 
    }

    public function GetAssignmentFileUpload(){
        return isset($this->sqlData['assignment_upload']) ? $this->sqlData["assignment_upload"] : "Error Image Upload"; 
    }

    public function AllowLateSubmission(){
        return isset($this->sqlData['allow_late_submission']) ? $this->sqlData["allow_late_submission"] : ""; 
    }
    public function GetSubjectPeriodAssignmentId(){
        return isset($this->sqlData['subject_period_assignment_id']) ? $this->sqlData["subject_period_assignment_id"] : 0; 
    }

    public function GetSubjectPeriodAssType(){
        return isset($this->sqlData['ass_type']) ? $this->sqlData["ass_type"] : "Asstype Undefined"; 
    }
    public function GetSubjectPeriodSubjectId(){
        return isset($this->sqlData['subject_id']) ? $this->sqlData["subject_id"] : 0; 
    }
    public function GetSubjectPeriodDescription(){
        return isset($this->sqlData['description']) ? $this->sqlData["description"] : ""; 
    }
    public function GetSubjectPeriodCreation(){
        $date= isset($this->sqlData['dateCreation']) ? $this->sqlData["dateCreation"] : ""; 
        return date("M j Y", strtotime($date));
    }
    public function GetMaxScore(){
        return isset($this->sqlData['max_score']) ? $this->sqlData["max_score"] : 0; 
    }
    public function GetAssignmentType(){
        return isset($this->sqlData['ass_type']) ? $this->sqlData["ass_type"] : "Undefined"; 
    }
    public function GetSetQuiz(){
        return isset($this->sqlData['set_quiz']) ? $this->sqlData["set_quiz"] : "Undefined"; 
    }
    public function GetAllowLateSubmission(){
        return isset($this->sqlData['allow_late_submission']) ? $this->sqlData["allow_late_submission"] : "Undefined"; 
    }

    public function DoesShowCorrectAnswer(){

        $subject_period_assignment_id = $this->GetSubjectPeriodAssignmentId();

        $queryQuizClass = $this->con->prepare("SELECT show_correct_answer FROM subject_period_assignment_quiz_class
                WHERE subject_period_assignment_id=:subject_period_assignment_id");
        
        $queryQuizClass->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $queryQuizClass->execute();

        return $queryQuizClass->fetchColumn();
    }

    

    public function HasReachedMaxSubmission(){

        $subject_period_assignment_id = $this->GetSubjectPeriodAssignmentId();

        $max_submission = $this->GetMaxSubmission();

        $student_id = $this->userLoggedInObj->GetId();

        $hasReached = false;

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                AND student_id=:student_id");
            
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();

        $my_submission_count = $query->rowCount();

        if($my_submission_count >= $max_submission){
            $hasReached = true;
        }
        return $hasReached;
    }

    public function GetSubjectPeriodAssignmentQuizClassId(){
        // It should be unique, for every quiz there`s only one set of question
        // Not One quiz have more than one  set of questions.
        // Quiz 1 = one set ✔, not Quiz 1, another set and again Quiz 1 another set
        $subject_period_assignment_id = $this->GetSubjectPeriodAssignmentId();

        $queryQuizClass = $this->con->prepare("SELECT subject_period_assignment_quiz_class_id FROM subject_period_assignment_quiz_class
                WHERE subject_period_assignment_id=:subject_period_assignment_id");
        
        $queryQuizClass->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $queryQuizClass->execute();

        return $queryQuizClass->fetchColumn();
    }

    public function GetStudentQuizClassTable($subject_period_assignment_quiz_class_id,
        $student_id){
        
        $query = $this->con->prepare("SELECT student_period_assignment_quiz_id FROM student_period_assignment_quiz
            WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();

    }
    public function GetStudentQuizClassTotalScore($subject_period_assignment_quiz_class_id,
        $student_id){
        
        $query = $this->con->prepare("SELECT total_score FROM student_period_assignment_quiz
            WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();

    }

    public function GetStudentQuizTime($subject_period_assignment_quiz_class_id,
        $student_id){
        
        $query = $this->con->prepare("SELECT student_quiz_time FROM student_period_assignment_quiz
            WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();

    }

    public function DoesQuizHadTaken($subject_period_assignment_quiz_class_id, $student_id) : bool{

        $query = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
                WHERE  subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
                AND student_id=:student_id
                AND time_finish IS NOT NULL
                LIMIT 1");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        if($query->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    public function DoesQuizHadTakenTimeFinishNull($subject_period_assignment_quiz_class_id, $student_id) : bool{

        $query = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
                WHERE  subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
                AND student_id=:student_id
                AND time_finish IS NULL
                LIMIT 1");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        if($query->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    public function GetTakeQuizCount($subject_period_assignment_quiz_class_id, $student_id){

        $query = $this->con->prepare("SELECT take_quiz_count FROM student_period_assignment_quiz
            WHERE  subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id
            AND time_taken IS NOT NULL
            LIMIT 1");
        
        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();
        return $query->fetchColumn();
    }
    public function GetSubjectTitle(){

        $subjectId = isset($this->sqlData['subject_id']) ? $this->sqlData["subject_id"] : 0; 

        $query = $this->con->prepare("SELECT subject_title FROM subject
            WHERE subject_id=:subjectId");

        $query->bindValue(":subjectId", $subjectId);
        $query->execute();

        return $query->fetchColumn();
    }
 
    public function createTeacherAssignmentSection($teacherCourseId, $subjectPeriodTypeName){

        $type_name = $this->GetTypeName();
        $due_date = $this->GetDueDate();
        $due_date = date("M j", strtotime($due_date));

        $assignment_upload = "";
        $viewed = $this->GetViewed();
        
        $subjectPeriodAssId = $this->GetSubjectPeriodAssignmentId();
        $subjectId = $this->GetSubjectPeriodSubjectId();

        $query = $this->con->prepare("SELECT student_id FROM teacher_course_student
            WHERE teacher_course_id= :teacher_course_id");

        $query->bindValue(":teacher_course_id", $teacherCourseId);
        $query->execute();
        
        $getUniquePassedStudent = $this->con->prepare("SELECT DISTINCT(student_id) FROM student_period_assignment
            WHERE subject_period_assignment_id= :subject_period_assignment_id");


        $getUniquePassedStudent->bindValue(":subject_period_assignment_id", $subjectPeriodAssId);
        $getUniquePassedStudent->execute();


        $overStudentPassed = $getUniquePassedStudent->rowCount();
        $overAllStudents = $query->rowCount();

        $assignmentType = $this->GetAssignmentType();
        $isSet = $this->GetSetQuiz();

        $assignmentShowLink = "
            <a href='my_student_assignment.php?subject_period_assignment_id=$subjectPeriodAssId&tc_id=$teacherCourseId'>
                0$subjectPeriodTypeName $type_name
            </a>
        ";
        if($assignmentType == "Quiz" && $isSet == "yes"){

            // $assignmentShowLink = "
            //     <a href='my_student_assignment.php?subject_period_assignment_quiz_id=$subjectPeriodAssId&tc_id=$teacherCourseId'>
            //         0$subjectPeriodTypeName $type_name
            //     </a>
            // ";

            $assignmentShowLink = "
                <a href='quiz_student.php'>
                    0$subjectPeriodTypeName $type_name
                </a>
            ";
        }
        
        return "
            <tbody>
                    <tr>
                        <td>
                           $assignmentShowLink
                        </td>
                        <td class='text-center'>$overStudentPassed/$overAllStudents</td>
                        <td class='text-center'>$due_date</td>
                        <td></td>
                        <td class='text-center'>
                            <a href='assignment.php?subject_period_assignment_id=$subjectPeriodAssId&tc_id=$teacherCourseId'>
                                <button>
                                    <i class='fas fa-edit'></i>
                                </button>
                            </a>
                        </td>
                    </tr>
            </tbody>
        ";
    }

    public function EditSubjectPeriodAssignmentForm($teacher_course_id){

        $subject_period_assignment_id = $this->GetSubjectPeriodAssignmentId();

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();

        $query = $query->fetch(PDO::FETCH_ASSOC);

        $type = $query['type_name'];
        $description = $query['description'];
        $max_submission = $query['max_submission'];

        $due_date = $query['due_date'];

        $assignment_upload = $query['assignment_upload'];
        $subject_period_id = $query['subject_period_id'];
        $subject_id = $query['subject_id'];
       
        // Cant populate the date and assignment_upload
        // echo $due_date;
        $image = "<img src='assets/images/profilePictures/assignment_pic.jpg' alt='Assignment Image'";
        if($assignment_upload){
            $image = "
                <img class='mb-3' style='height: 270px; width:500px;' src='$assignment_upload' alt='Image' >
            ";
        }
        // echo $assignment_upload;
        $output = "
            <div>
                <form action='assignment.php?subject_period_assignment_id=$subject_period_assignment_id&tc_id=$teacher_course_id'
                    method='POST' enctype='multipart/form-data'>
                        <div class='form-group'>
                            $image
                            <input class='form-control mb-3 col-6' name='assignment_upload' placeholder='File' type='file' value=$assignment_upload>

                            <input class='form-control mb-3' value='$type' name='type_name' placeholder='Type' type='text'>
                            <input class='form-control mb-3' name='max_submission' 
                                placeholder='Max Submission' maxlength='1' size='3'  type='text' value='$max_submission'>

                            <input class='form-control mb-3' name='due_date' type='date' value=$due_date >
                        
                            <textarea class='form-control mb-3 summernote' type='text'  
                                name='description' placeholder='Description'>$description</textarea>
                        
                            <input type='hidden' name='subject_period_id' value='$subject_period_id'>
                            <input type='hidden' name='subject_id' value='$subject_id'>
                            <input type='hidden' name='assignment_upload_value' value='$assignment_upload'>
                        </div>
                        <button type='submit' class='btn btn-primary' name='submit_edit_assignment'>Save</button>
                </form>
               
            </div>
        ";

        return $output;
    }
    public function EditSubjectPeriodAssignment($subject_period_assignment_id,
        $type_name, $max_submission, $due_date, $description, $subject_period_id,
        $subject_id, $image_value){


        // $subject_period_assignment_id = $this->GetSubjectPeriodAssignmentId();

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();

        $query = $query->fetch(PDO::FETCH_ASSOC);


        $image = $_FILES['assignment_upload'] ?? null;
        $imagePath='';


        if (!is_dir('assets')) {
            mkdir('assets');
        }
        
        if (!is_dir('assets/images')) {
            mkdir('assets/images');
        }
        if (!is_dir('assets/images/student_assignments')) {
            mkdir('assets/images/student_assignments');
        }

        
        if ($image) {
            
            if ($query['image']) {
                unlink($query['image']);
            }
            $imagePath = 'assets/images/student_assignments/' . $image['name'];
            // mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        if($imagePath == 'assets/images/student_assignments/'){

            // If user didnt edit the image, let the image stay.
            $imagePath = $image_value;
        }

        $query = $this->con->prepare("UPDATE subject_period_assignment
            SET assignment_upload=:assignment_upload,
                type_name=:type_name,max_submission=:max_submission,
                description=:description, due_date=:due_date
            
            WHERE subject_period_assignment_id=:subject_period_assignment_id 
            AND subject_period_id=:subject_period_id 
            AND subject_id=:subject_id");
        
        $query->bindValue(":assignment_upload", $imagePath);
        $query->bindValue(":type_name", $type_name);
        $query->bindValue(":max_submission", $max_submission);
        $query->bindValue(":description", $description);
        $query->bindValue(":due_date", $due_date);
        //
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":subject_id", $subject_id);

        return $query->execute();
            // echo $type_name;
            // echo "<br>";
            // echo $max_submission;
            // echo "<br>";
            // echo $due_date;
            // echo "<br>";
            // echo $description;
            // echo "<br>";
            // echo $subject_period_id;
            // echo "<br>";
            // echo $subject_id;
    }


    public function createStudentAssignmentSection($teacherCourseId, $subjectPeriodTypeName){

        $description = $this->GetTypeName();

        $due_date = $this->GetDueDate();
        $due_date = date("M j", strtotime($due_date));

        $assignment_upload = "";
        $viewed = $this->GetViewed();

        $student_id = $this->userLoggedInObj->GetId();
        
        $subjectPeriodAssId = $this->GetSubjectPeriodAssignmentId();
       
        $gradedItemsScore =  $this->GradedPassedAssignment($subjectPeriodAssId);

        $overAllItems = $this->GetMaxScore();

        $results = "--";
        if($gradedItemsScore != ""){
            $results = $gradedItemsScore."/".$overAllItems;
        }

        // To check if student Submitted the specific assignment.
        $output = "~";
        $hasSubmitted = $this->CheckStudentSubmittedAssignment($subjectPeriodAssId);
        $submitOnTime = $this->CheckStudentSubmittedOnTime();

        $status="";
        if($hasSubmitted == true){
            $output = "<i style='color: green;' class='fas fa-check'></i>";
            $status = "<i style='color: green;' class='fas fa-check'></i>";

        }else if($hasSubmitted == false && $submitOnTime == false){
            $output = "<i style='color: orange;' class='fas fa-flag'></i>";
            $status = "<i style='color: red;' class='fas fa-times'></i>";
            $results = "M";
        }
        // If was reached by deadline.

        // To check the status.
        // When the teacher have been checked the assignment.

        $assType = $this->GetAssignmentType();
        $getSetQuiz = $this->GetSetQuiz();

        $assignmentArg = "
             <a 
                href='student_assignment_view.php?subject_period_assignment_id=$subjectPeriodAssId&tc_id=$teacherCourseId'>
                    0$subjectPeriodTypeName $description
            </a>
        ";
        if($assType == "Quiz" && $getSetQuiz == "yes"){
            // $_SESSION['subject_period_assignment_quiz_class_id'] = '';

            $query1 = $this->con->prepare("SELECT subject_period_assignment_quiz_class_id FROM subject_period_assignment_quiz_class
                WHERE subject_period_assignment_id=:subject_period_assignment_id");

            $query1->bindValue(":subject_period_assignment_id", $subjectPeriodAssId);
            $query1->execute();

            if($query1->rowCount() > 0){
                $subject_period_assignment_quiz_class_id = $query1->fetchColumn();

                // echo $subject_period_assignment_quiz_class_id;
                // echo "<br>";

                $query2 = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
                    WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
                    AND student_id=:student_id
                    LIMIT 1");


                $query2->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
                $query2->bindValue(":student_id", $student_id);
                $query2->execute();
                
                $output = "<i style='color: orange;' class='fas fa-flag'></i>";
                $status = "<i style='color: red;' class='fas fa-times'></i>";


                if($query2->rowCount() > 0){
                    $query2 = $query2->fetch(PDO::FETCH_ASSOC);

                    $total_score = $query2['total_score'];
                    
                    if($query2['time_finish'] != NULL){
                        $output = "<i style='color: green;' class='fas fa-check'></i>";
                        $status = "<i style='color: green;' class='fas fa-check'></i>";
                    }
                   

                    $results = $total_score ."/". $overAllItems;
                }

            }
            // echo $subjectPeriodAssId;
            
            $assignmentArg = "
                <a  href='student_assignment_view.php?subject_period_assignment_id=$subjectPeriodAssId&tc_id=$teacherCourseId'>
                    0$subjectPeriodTypeName $description
                </a>";

        }
        // echo $subjectPeriodAssId;
        return "
            <tbody>
                    <tr>
                        <td>
                            $assignmentArg
                        </td>
                        <td class='text-center'>
                            $output
                        </td>
                        <td class='text-center'>$results</td>
                        <td class='text-center'>$due_date</td>
                        <td class='text-center'>
                            $status
                        </td>
                    </tr>
            </tbody>
        ";
    }
    
    public function CheckStudentSubmittedOnTime(){

        $due_date = $this->GetDueDate();
        
        // echo $due_date . " | ";
        $date_now = date("Y-m-d h:i:s");

        // echo $date_now . " || ";

        if($date_now >= $due_date){
            return false;
        }

        // Guide
        $datetime_1 = '2022-02-03 11:15:30'; 
        $datetime_2 = '2022-02-04 13:30:45'; 
        
        $start_datetime = new DateTime($datetime_1); 
        $diff = $start_datetime->diff(new DateTime($datetime_2)); 
        
        // echo $diff->days.' Days total<br>'; 
        // echo $diff->y.' Years<br>'; 
        // echo $diff->m.' Months<br>'; 
        // echo $diff->d.' Days<br>'; 
        // echo $diff->h.' Hours<br>'; 
        // echo $diff->i.' Minutes<br>'; 
        // echo $diff->s.' Seconds<br>';

        return true;
    }
    public function CheckStudentSubmittedAssignment($subjectPeriodAssId){

        $student_id = $this->userLoggedInObj->GetId();
        // echo $subjectPeriodAssId;
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id");
 
        $query->bindValue(":subject_period_assignment_id", $subjectPeriodAssId);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        return $query->rowCount() > 0;
    }
    private function GradedPassedAssignment($subjectPeriodAssId){

        $query = $this->con->prepare("SELECT DISTINCT grade FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND grade > 0
            ORDER BY passed_date DESC");
 
        $query->bindValue(":subject_period_assignment_id", $subjectPeriodAssId);

        $query->execute();
        
        return $query->fetchColumn();
    }

    public function StudentSubmission(){

        $subject_period_assignment_id = $this->GetSubjectPeriodAssignmentId();
        $student_id = $this->userLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id");
        
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        
        if($query->rowCount() > 0){
            $table = "
                <table class='table table-hover'>
                    <thead>
                        <tr>
                            <th>Date Upload</th>
                            <th>File Name</th>
                            <th>Submitted By:</th>
                            <th>Grade</th>
                        </tr>
                    </thead>";

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $user = new Student($this->con, $row['student_id']);
                $studentName = $user->GetName();
                
                $file_name = $row['file_name'];
                $date = $row['passed_date'];
                $date = date("M j Y", strtotime($date));

                $table .= "
                    <tbody>
                        <tr>
                            <td>$date</td>
                            <td>$file_name</td>
                            <td>$studentName</td>
                            <td></td>
                        </tr>
                    </tbody>
                ";
            }
        }else{
            $table = "Pass your assignment now.";
        }
        
        $table .= "
            </table>";

        return $table;
        
    }
    
    public function createForm($hasReachedMaxSubmission){

        $subjectPeriodAssignmentId = $this->GetSubjectPeriodAssignmentId();
        $submitOnTime = $this->CheckStudentSubmittedOnTime();

        $name = "student_assignment_submit_$subjectPeriodAssignmentId";
        $student_id = $this->userLoggedInObj->GetId();

        $style = "block";

        // * Enable the $style for real.
        // If student have reached max submission and doesnt submit on time (deadline)
        // enable the submission button.
        if($hasReachedMaxSubmission == true || $submitOnTime == false){
            // $style = "none";
        }
        return "
                <form style='display: $style;' action='student_assignment_submit.php?subject_period_assignment_id=$subjectPeriodAssignmentId' method='POST' enctype='multipart/form-data'>
                    <div class='form-group row'>
                        <div class='form-group row'>
                            <div class='col-4'>
                                <label>Upload File</label>
                            </div>
                            <div class='col-8'>
                                <input required class='form-control mb-2' name='assignment_file' type='file'>
                            </div>
                        </div>

                        <input required type='text' class='form-control mb-2' name='file_name' placeholder='File Name'>

                         <div class='col-4 mt-3 mb-3'>
                                <label>Description</label>
                            </div>
                        <textarea placeholder='Description'
                            class='form-control mb-2 summernote' name='description'></textarea>
                        
                        <input type='hidden' name='student_id' value='$student_id'>
                        <input type='hidden' name='subject_period_assignment_id' value='$subjectPeriodAssignmentId'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='$name'>Save</button>
                </form>
            ";
    }

    public function insertStudentAssignment($assignment_file,$file_name,
        $description, $student_id, $subject_period_assignment_id){
        
        $image = $_FILES['assignment_file'] ?? null;
        $imagePath= '';

        if (!is_dir('assets/images/student_assignments_answers')) {
            mkdir('assets/images/student_assignments_answers');
        }

        if ($image && $image['tmp_name']) {
            $imagePath = 'assets/images/student_assignments_answers' . '/' . $image['name'];
            // if(!file_exists($imagePath)){
            //     mkdir(dirname($imagePath));
            // }
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        // If the student sends another answer, 
        // the subject_period_assignment_id with its is_final must be "yes" accordingly
        // if there`s  is_final = yes and student needs to submit, turn it into "no"
        // and mark the new one into yes.

        $wasSuccess = $this->SetToFinalToNo($subject_period_assignment_id);
        // if wasSuccess = false, this is your first time to submit on
        // the subject_period_assignment_id

        $query = $this->con->prepare("INSERT INTO student_period_assignment(assignment_file, file_name,
            description, student_id, subject_period_assignment_id, is_final)
            VALUES(:assignment_file, :file_name, :description,
                :student_id, :subject_period_assignment_id, :is_final)");
        
        $query->bindValue(":assignment_file", $imagePath);
        $query->bindValue(":file_name", $file_name);
        $query->bindValue(":description", $description);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":is_final", "yes");

        return $query->execute();
    }
    
    private function SetToFinalToNo($subject_period_assignment_id) : bool{

        $isFinalNo = false;

        $haveOtherSub = $this->doesHaveOtherSubmission($subject_period_assignment_id);
        // We have past submission, the is_final in the past should be "no"
        if($haveOtherSub == true){

            $is_final_no = "no";
            $is_final = "yes";
            $student_id = $this->userLoggedInObj->GetId();

            $set_is_final_to_no = $this->con->prepare("UPDATE student_period_assignment
                SET is_final=:is_final_no
                WHERE student_id=:student_id 
                AND subject_period_assignment_id=:subject_period_assignment_id
                AND is_final=:is_final");

            $set_is_final_to_no->bindValue(":student_id", $student_id);
            $set_is_final_to_no->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
            $set_is_final_to_no->bindValue(":is_final", $is_final);
            $set_is_final_to_no->bindValue(":is_final_no", $is_final_no);

            $successfulSet = $set_is_final_to_no->execute();

            if($successfulSet)
                $isFinalNo = true;
        }

        return $isFinalNo;
    }
    private function doesHaveOtherSubmission($subject_period_assignment_id) : bool{

        $student_id = $this->userLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE student_id=:student_id 
            AND subject_period_assignment_id=:subject_period_assignment_id
            AND is_final=:is_final
            LIMIT 1");
        
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":is_final", "yes");
        $query->execute();

        return $query->rowCount() > 0;
    }
}        
?>