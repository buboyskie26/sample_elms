<?php

    class AddAssignment{

    private $con, $subjectPeriod, $teacherLoggedInObj, $teacherCourse;

    public function __construct($con, $subjectPeriod, $teacherLoggedInObj, $teacherCourse)
    {
        $this->con = $con;
        $this->subjectPeriod = $subjectPeriod;
        $this->teacherLoggedInObj = $teacherLoggedInObj;
        $this->teacherCourse = $teacherCourse;

    }

    public function createForm($subjectId, $subjectPeriodId){

        $link = "";
        $subject_period_id = $this->subjectPeriod->GetSubjectPeriodId();
        $subject_id =  $this->subjectPeriod->GetSubjectId();
        $teacherCourseId = $this->teacherCourse->GetTeacherCourseId();

        echo "";
        return "
            <form action='add_assignment.php?subject_id=$subjectId&subject_period_id=$subjectPeriodId&teacher_course_id=$teacherCourseId'
                method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        <input class='form-control mb-3' name='assignment_upload' placeholder='File' type='file'>

                        <input class='form-control mb-3' name='type_name' placeholder='Type' type='text'>
                        <input class='form-control mb-3' name='max_submission' 
                            placeholder='Max Submission' maxlength='1' size='3'  type='text'>

                        <input class='form-control mb-3' name='due_date' placeholder='Deadline' type='date'>
                        <input class='form-control mb-3' name='max_score' placeholder='Max Score' type='text' maxlength='3'>
                       
                         

                        <textarea class='form-control mb-3 summernote' type='text'
                            name='description' placeholder='Description'></textarea>
                        
                        <input class='form-control' type='hidden' name='subject_period_id' value='$subject_period_id'>
                        <input class='form-control' type='hidden' name='subject_id' value='$subject_id'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_add_assignment'>Save</button>
                </form>
        ";
    }
    
    public function AddAssignment($assignment_upload, $type_name,
        $subject_period_id, $subject_id, $max_submission, $due_date,
        $description, $max_score){

         
        $image = $_FILES['assignment_upload'] ?? null;
        $imagePath='';

        // if (!is_dir('images')) {
        //     mkdir('images');
        // }
        
        // if (!is_dir('images/student_assignments')) {
        //     mkdir('images/student_assignments');
        // }

        if (!is_dir('assets')) {
            mkdir('assets');
        }
        
        if (!is_dir('assets/images')) {
            mkdir('assets/images');
        }
        if (!is_dir('assets/images/student_assignments')) {
            mkdir('assets/images/student_assignments');
        }

        if ($image && $image['tmp_name']) {
            $imagePath = 'assets/images/student_assignments' . '/' . $image['name'];
            // mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }
        $teacher_id = $this->teacherLoggedInObj->GetId();

        $query = $this->con->prepare("INSERT INTO subject_period_assignment(assignment_upload, 
            type_name, subject_period_id, subject_id, viewed, due_date,
            max_submission, description, max_score, ass_type,teacher_id)
            VALUES(:assignment_upload, :type_name, :subject_period_id,
                :subject_id, :viewed, :due_date, :max_submission, :description,
                :max_score, :ass_type, :teacher_id)");
        
        $query->bindValue(":assignment_upload", $imagePath);
        $query->bindValue(":type_name", $type_name);
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":subject_id", $subject_id);
        $query->bindValue(":viewed", 'no');
        $query->bindValue(":due_date", $due_date);
        $query->bindValue(":max_submission", $max_submission);
        $query->bindValue(":description", $description);
        $query->bindValue(":max_score", $max_score);
        $query->bindValue(":ass_type", "Dropbox");
        $query->bindValue(":teacher_id", $teacher_id);

        $firstQuery = $query->execute();

        // $firstQueryId = $this->con->lastInsertId();
        // $wasSuccess = $this->InsertStudentPeriodAssignment($firstQueryId);

        return $firstQuery;
    }
    private function InsertStudentPeriodAssignment($firstQueryId){

        $isInserted = false;

        // Another class
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherLoggedInObj->GetId();

        $allStudentInMySubject = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE teacher_course_id=:teacher_course_id AND teacher_id=:teacher_id");
        
        $allStudentInMySubject->bindValue(":teacher_course_id", $teacher_course_id);
        $allStudentInMySubject->bindValue(":teacher_id", $teacher_id);
        $allStudentInMySubject->execute();

        if($allStudentInMySubject->rowCount() > 0){

            while($row = $allStudentInMySubject->fetch(PDO::FETCH_ASSOC)){

                // $student = new Student($this->con, $row['student_id']);

                $subject_period_assignment_id = $firstQueryId;
                $student_id = $row['student_id'];

                $insertStudPeriodAss = $this->con->prepare("INSERT INTO student_period_assignment(subject_period_assignment_id, student_id)
                    VALUES(:subject_period_assignment_id, :student_id)");
                
                $insertStudPeriodAss->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
                $insertStudPeriodAss->bindValue(":student_id", $student_id);

                $insertStudPeriodAss->execute();
            }

            $isInserted = true;
        }

        return $isInserted;
    }
    
    public function HandOutForm($subject_period_id, $teacher_course_id){
        $subject_id = $this->subjectPeriod->GetSubjectId();

        // echo $subject_id;
        return "
            <form action='add_assignment_handout.php?subject_period_id=$subject_period_id'
                method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        <input class='form-control mb-3' multiple='multiple' name='handout_file_location[]' placeholder='File' type='file'>

                        <input class='form-control mb-3' name='handout_name' placeholder='Handout Name' type='text'>
 
                        <input class='form-control' type='hidden' name='subject_period_id' value='$subject_period_id'>
                        <input class='form-control' type='hidden' name='subject_id' value='$subject_id'>
                        <input class='form-control' type='hidden' name='teacher_course_id' value='$teacher_course_id'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_add_assignment_handout'>Save</button>
                </form>
        ";  
    }
    
    public function AddHandoutAssignment($imageArray, $handout_name,
        $subject_period_id, $subject_id, $teacher_course_id){
        $wasSuccess = false;
        
        // $subject_period_id = $this->subjectPeriod->GetSubjectPeriodId();
        // $subject_period_id = $this->subjectPeriod->GetSubjectPeriodId();

        if (!is_dir('assets/images/handouts')) {
            mkdir('assets/images/handouts');
        }

        if($handout_name != ""){

            $query = $this->con->prepare("INSERT INTO subject_period_assignment_handout
                (handout_name, subject_period_id, subject_id, teacher_course_id)
                VALUES(:handout_name, :subject_period_id, :subject_id, :teacher_course_id)");

            $query->bindValue(":handout_name", $handout_name);
            $query->bindValue(":subject_period_id", $subject_period_id);
            $query->bindValue(":subject_id", $subject_id);
            $query->bindValue(":teacher_course_id", $teacher_course_id);
            
            if($query->execute()){

                $subject_period_assignment_handout_id = $this->con->lastInsertId();

                $total_count = sizeof($imageArray);
                
                if($total_count > 0){
                    for( $i=0 ; $i < $total_count ; $i++ ) {
                    //The temp file path is obtained
                    $tmpFilePath = $_FILES['handout_file_location']['tmp_name'][$i];
                    $imagePath= '';

                    // A file path needs to be present
                    if ($tmpFilePath != ""){

                        // $newFilePath = "./uploadFiles/" . $_FILES['upload']['name'][$i];

                        // if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                        //     //Other code goes here
                        // }
                        $imagePath = 'assets/images/handouts' 
                            . '/' . $_FILES['handout_file_location']['name'][$i];
                            
                        move_uploaded_file($tmpFilePath, $imagePath);
                    }


                    $queryv2 = $this->con->prepare("INSERT INTO subject_period_assignment_handout_file
                            (subject_period_assignment_handout_id, handout_file_location)
                            VALUES(:subject_period_assignment_handout_id, :handout_file_location)");
                
                    $queryv2->bindValue(":handout_file_location", $imagePath);
                    $queryv2->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);

                    if($queryv2->execute()){
                        $wasSuccess =  true;
                    }

                }
            }
            }
        }
        
        return $wasSuccess;
    }

    private function randomString($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $str .= $characters[$index];
        }
        return $str;
    } 
}
?>