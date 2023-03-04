<?php

    class StudentPeriodAssignment{

    private $con, $subjectPeriodAssignment, $studentUserLoggedInObj, $sqlData;

    public function __construct($con, $subjectPeriodAssignment,
        $studentUserLoggedInObj)
    {
        $this->con = $con;
        $this->subjectPeriodAssignment = $subjectPeriodAssignment;
        $this->studentUserLoggedInObj = $studentUserLoggedInObj;
        // $this->sqlData = $input;

        // if(!is_array($input)){

        //     $query = $this->con->prepare("SELECT * FROM student_period_assignment 
        //         WHERE student_period_assignment_id = :student_period_assignment_id");

        //     $query->bindParam(":student_period_assignment_id", $input);
        //     $query->execute();

        //     $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        // }
    }
    // public function GetStudentPeriodStudentId(){
    //     return isset($this->sqlData['student_id']) ? $this->sqlData['student_id']  : 0;
    // }

    public function create($hasReachedMaxSubmission, $teacher_course_id){

        $subjectPeriodAssStudentSubmission = $this->StudentSubmission();
        $createForm = $this->createForm($hasReachedMaxSubmission, $teacher_course_id);

        $output = "
            <div class='column_1'>
                <div class='column_1_header'>
                    <span>BSCS-501 / APPDEV101 / School Year: 2013-2014 / Uploaded Assignments</span>
                </div>
                $subjectPeriodAssStudentSubmission
            </div>

            <div class='column'>
                $createForm
            </div>
                
        ";
        return $output;
    }
    private function createForm($hasReachedMaxSubmission, $teacher_course_id){
        
        $subjectPeriodAssignmentId = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        $submitOnTime = $this->subjectPeriodAssignment->CheckStudentSubmittedOnTime();

        $name = "student_assignment_submit_$subjectPeriodAssignmentId";
        $student_id = $this->studentUserLoggedInObj->GetId();


        $doesAssignmentChecked = $this->CheckedSubmittedAssignment();
        // * Enable the $style for real.
        // If student have reached max submission and doesnt submit on time (deadline)
        // enable the submission button.
        $style = "block";
        if($hasReachedMaxSubmission == true || $submitOnTime == false || $doesAssignmentChecked == true){
            $style = "none";
        }
        return "
        
             <form style='display: $style;' id='myForm' action='student_assignment_submit.php?subject_period_assignment_id=$subjectPeriodAssignmentId&tc_id=$teacher_course_id'
              method='POST' enctype='multipart/form-data'>
                    <div class='form-group row'>
                        <div class='form-group row'>
                            <div class='col-4'>
                                <label>Upload File</label>
                            </div>
                            <div class='col-8'>
                                <input required class='form-control mb-2' multiple='multiple' name='assignment_file[]' type='file'>
                            </div>
                        </div>

                        <input required type='text' class='form-control mb-2' name='file_name' placeholder='File Name'>

                         <div class='col-4 mt-3 mb-3'>
                                <label>Text Description</label>
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

    public function insertStudentAssignment($imageArray,$file_name,
        $description, $student_id, $subject_period_assignment_id){
        
        // $image = $_FILES['assignment_file'] ?? null;
        // $imagePath= '';

        if (!is_dir('assets/images/student_assignments_answers')) {
            mkdir('assets/images/student_assignments_answers');
        }

        // if ($image && $image['tmp_name']) {
        //     $imagePath = 'assets/images/student_assignments_answers' . '/' . $image['name'];
        //     // if(!file_exists($imagePath)){
        //     //     mkdir(dirname($imagePath));
        //     // }
        //     move_uploaded_file($image['tmp_name'], $imagePath);
        // }

        // If the student sends another answer, 
        // the subject_period_assignment_id with its is_final must be "yes" accordingly
        // if there`s  is_final = yes and student needs to submit, turn it into "no"
        // and mark the new one into yes.

        $wasSuccess = $this->SetToFinalToNo($subject_period_assignment_id);
        // if wasSuccess = false, this is your first time to submit on
        // the subject_period_assignment_id

        $query = $this->con->prepare("INSERT INTO student_period_assignment
            (assignment_file, file_name, description, student_id, subject_period_assignment_id, is_final)
            VALUES(:assignment_file, :file_name, :description, :student_id, :subject_period_assignment_id, :is_final)");
        
        // $query->bindValue(":assignment_file", $imagePath);
        $query->bindValue(":assignment_file", "");
        $query->bindValue(":file_name", $file_name);
        $query->bindValue(":description", $description);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":is_final", "yes");

        $successfullySubmitAssignment = $query->execute();

        $student_period_assignment_id = $this->con->lastInsertId();

        echo $student_period_assignment_id;

        $array_image = [];

        array_push($array_image, "");
        
        // If theres an image, store on specific table.
        if($student_period_assignment_id != 0 && sizeof($imageArray) > 0){

            // $image = $_FILES['assignment_file'] ?? null;
            // $imagePath= '';

            // if (!is_dir('assets/images/student_assignments_answers')) {
            //     mkdir('assets/images/student_assignments_answers');
            // }

            // if ($image && $image['tmp_name']) {
            //     $imagePath = 'assets/images/student_assignments_answers' . '/' . $image['name'];
            //     // if(!file_exists($imagePath)){
            //     //     mkdir(dirname($imagePath));
            //     // }
            //     move_uploaded_file($image['tmp_name'], $imagePath);
            // }

            $total_count = sizeof($imageArray);

            for( $i=0 ; $i < $total_count ; $i++ ) {
                //The temp file path is obtained
                $tmpFilePath = $_FILES['assignment_file']['tmp_name'][$i];
                $imagePath= '';

                // A file path needs to be present
                if ($tmpFilePath != ""){

                    // $newFilePath = "./uploadFiles/" . $_FILES['upload']['name'][$i];

                    // if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                    //     //Other code goes here
                    // }
                    $imagePath = 'assets/images/student_assignments_answers' 
                        . '/' . $_FILES['assignment_file']['name'][$i];
                        
                    move_uploaded_file($tmpFilePath, $imagePath);
                }

                $queryv2 = $this->con->prepare("INSERT INTO student_period_assignment_file
                    (assignment_file_path, student_id, student_period_assignment_id)
                    VALUES(:assignment_file_path, :student_id, :student_period_assignment_id)");
        
                $queryv2->bindValue(":assignment_file_path", $imagePath);
                $queryv2->bindValue(":student_id", $student_id);
                $queryv2->bindValue(":student_period_assignment_id", $student_period_assignment_id);
                $queryv2->execute();

            }

            
            
        }

        return $successfullySubmitAssignment;

    }

    public function NumberOfPassedDropboxAssignment($subject_period_assignment_id){

        $student_id = $this->studentUserLoggedInObj->GetId();
        // echo $subject_period_assignment_id;
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND is_final=:is_final");
 
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":is_final", "yes");

        $query->execute();
        $output = 0;
        if($query->rowCount() > 0){
            $output = $query->rowCount();
            // while($row = $query->fetch(PDO::FETCH_ASSOC)){
            //     $output .= $row['file_name'];
            //     // $output++;
            // }
        }

        return $query->rowCount();
    }

    public function NumberOfPassedQuizAssignment($subject_period_assignment_quiz_class_id,
        $student_id){
        
        $query = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
            WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id
            AND time_finish IS NOT NULL");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->rowCount();
    }
    
    private function CheckedSubmittedAssignment() : bool{

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();

        $student_id = $this->studentUserLoggedInObj->GetId();

        $isChecked = false;

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                AND student_id=:student_id
                AND is_final=:is_final
                AND grade > 0
                LIMIT 1");
            
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":is_final", "yes");
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function HasReachedMaxSubmission(){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();

        $max_submission = $this->subjectPeriodAssignment->GetMaxSubmission();

        $student_id = $this->studentUserLoggedInObj->GetId();

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

    public function CheckStudentSubmittedOnTime(){

        $due_date = $this->subjectPeriodAssignment->GetDueDate();

        // echo $due_date . " | ";
        $date_now = date("Y-m-d h:i:s");

        // echo $date_now . " || ";

        if($date_now > $due_date){
            return false;
        }
        return true;
    }

    
    private function SetToFinalToNo($subject_period_assignment_id) : bool{

        $isFinalNo = false;

        $haveOtherSub = $this->doesHaveOtherSubmission($subject_period_assignment_id);
        // We have past submission, the is_final in the past should be "no"
        if($haveOtherSub == true){

            $is_final_no = "no";
            $is_final = "yes";
            $student_id = $this->studentUserLoggedInObj->GetId();

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

        $student_id = $this->studentUserLoggedInObj->GetId();

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


    public function StudentSubmission(){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        $student_id = $this->studentUserLoggedInObj->GetId();

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
}
?>