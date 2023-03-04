<?php

    class SubjectPeriodAssignmentHandout{

    private $con, $userLoggedInObj, $sqlData;

    // $input = subject_period_assignment OBJECT
    public function __construct($con, $input, $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->sqlData = $input;

        // $input SHOULD BE subject_period_assignment_id if it is not subject_period_assignment object
        if(!is_array($input)){
            $query = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
                WHERE subject_period_assignment_handout_id = :subject_period_assignment_handout_id");

            $query->bindParam(":subject_period_assignment_handout_id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

    }
    public function GetHandoutName(){
        return isset($this->sqlData['handout_name']) ? $this->sqlData["handout_name"] : ""; 
    }
    public function GetHandoutId(){
        return isset($this->sqlData['subject_period_assignment_handout_id']) ? $this->sqlData["subject_period_assignment_handout_id"] : ""; 
    }
    public function DoesStudentViewedTheHandout($student_id, $subject_period_assignment_handout_id){


        $query = $this->con->prepare("SELECT * FROM handout_viewed
            WHERE student_id=:student_id
            AND subject_period_assignment_handout_id=:subject_period_assignment_handout_id");
         
        $query->bindValue(":student_id" ,$student_id);
        $query->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
        $query->execute();

        $output = false;
        if($query->rowCount() > 0){
            $output = true;
        }

        return $output;

    }
    public function CreateStudentAssignmentHandout($subjectPeriodTypeName, $subject_period_id){


        $handOutName = $this->GetHandoutName();
        $subject_period_assignment_handout_id = $this->GetHandoutId();
        $student_id = $this->userLoggedInObj->GetId();

        $output = 0;
        $results = 0;
        $due_date = 0;
        $status = "~";

        $studentViewedHandout = $this->DoesStudentViewedTheHandout($student_id, $subject_period_assignment_handout_id);

        if($studentViewedHandout == true){
            $status = "
                <i style='color: green;' class='fas fa-check'></i>
            ";
        }

        $_SESSION['subject_period_id'] = $subject_period_id;

        $handoutViewed = "clickHandout($subject_period_assignment_handout_id, $student_id)";

        $assignmentArg = "
             <a onclick='$handoutViewed'
                href='student_assignment_view.php?handout=$subject_period_assignment_handout_id'>
                    <i class='fa-solid fa-file-pdf'></i> 0$subjectPeriodTypeName $handOutName
            </a>
        ";


        return "
            <tbody>
                    <tr>
                        <td>
                            $assignmentArg
                        </td>
                        <td class='text-center'>
                            
                        </td>
                        <td class='text-center'></td>
                        <td class='text-center'></td>
                        <td class='text-center'>
                            $status
                        </td>
                    </tr>
            </tbody>
        ";
    }

    public function CreateTeacherAssignmentHandout($subjectPeriodTypeName, $teacher_course_id){


        $handOutName = $this->GetHandoutName();
        $subject_period_assignment_handout_id = $this->GetHandoutId();
        $student_id = $this->userLoggedInObj->GetId();

        $output = 0;
        $results = 0;
        $due_date = 0;
        $status = "~";

        $status = "
                <i style='color: green;' class='fas fa-check'></i>
            ";
        // $studentViewedHandout = $this->DoesStudentViewedTheHandout($student_id, $subject_period_assignment_handout_id);

        // if($studentViewedHandout == true){
        //     $status = "
        //         <i style='color: green;' class='fas fa-check'></i>
        //     ";
        // }

        // $_SESSION['subject_period_id'] = $subject_period_id;
        $_SESSION['teacher_course_id'] = $teacher_course_id;

        $handoutViewed = "clickHandout($subject_period_assignment_handout_id, $student_id)";

        // echo $subject_period_assignment_handout_id;

        $assignmentArg = "
             <a 
                href='teacher_handout_view.php?handout_id=$subject_period_assignment_handout_id'>
                    <i class='fa-solid fa-file-pdf'></i> 0$subjectPeriodTypeName $handOutName
            </a>
        ";


        return "
            <tbody>
                    <tr>
                        <td>
                            $assignmentArg
                        </td>
                        <td class='text-center'>
                            
                        </td>
                        <td class='text-center'></td>
                        <td class='text-center'></td>
                        <td class='text-center'>
                            $status
                        </td>
                    </tr>
            </tbody>
        ";
    }

    public function create(){
        
        $subject_period_id = isset($_SESSION['subject_period_id']) ? $_SESSION['subject_period_id'] : 0;

        $handOutName = $this->GetHandoutName();
        $subject_period_assignment_handout_id = $this->GetHandoutId();

        $query = $this->con->prepare("SELECT handout_file_location FROM subject_period_assignment_handout_file
            WHERE subject_period_assignment_handout_id=:subject_period_assignment_handout_id");
        
        $query->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
        $query->execute();

        $handouts = "";

        $subjectPeriodTypeName = 0;

        if($query->rowCount() > 0){

            if($subject_period_id != 0){
                $subjectPeriod = new SubjectPeriod($this->con,
                    $subject_period_id, $this->userLoggedInObj, "student");

                $subjectPeriodTypeName = $subjectPeriod->GetSubjectPeriodTypeName();
            }

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $handout_file_location = $row['handout_file_location'];

                $handouts .= "
                    <a href='$handout_file_location' target='__blank' rel='noopener noreferrer'>
                        0$subjectPeriodTypeName $handout_file_location
                    </a>
                    <br>
                ";
            }
        }
        $handoutName = "";
        $handoutLink = "";
        $handoutContent = "";

        $output = "
            <div class='handout_inner'>
                <h3>0$subjectPeriodTypeName $handOutName</h3>

                <div class='handout_content'>
                    <p>
                        $handouts
                    </p>
                </div>
            </div>
        ";



        return $output;
    }

    public function CreateStudentViewedHandout($teacher_course_id, $subject_period_assignment_handout_id){

        $teacher_id = $this->userLoggedInObj->GetId();

        $table = "
            <section class='intro'>
                <div class='bg-image h-100' style='background-color: transparent;'>
                    <div class='container'>
                        
                        <div class='row justify-content-center'>
                            <div class='col-12'>
                                
                                <div class='card shadow-2-strong' style='background-color: #333;'>
                                    
                                    <div style='background-color: transparent; text-align: right' class='form-control  '>
                                        <a href=''>
                                            <button class='btn btn-success btn-sm'>Set Quiz to class</button>
                                        </a>
                                    </div>
                                    
                                    <div class='card-body'>
                                        <div class='table-responsive'>
                                            <table id='subjectPeriodQuizTable' class='table table-borderless mb-0 tb-right'>
                                                <thead>
                                                    <tr>
                                                        <th class='text-center' scope='col'>
                                                            <div class='form-check'>
                                                                <input class='form-check-input' type='checkbox' value='' id='flexCheckDefault' />
                                                            </div>
                                                        </th>
                                                        <th class='text-center' scope='col'>Name</th>
                                                        <th class='text-center' scope='col'>Viewed</th>
                                                        <th class='text-center' scope='col'>Date viewed</th>
                                                        <th class='text-center' scope='col'>View count</th>
                                                    </tr>
                                                </thead>
                                            
        ";

        $query = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE teacher_course_id=:teacher_course_id
            AND teacher_id=:teacher_id");

        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->execute();

        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $table .= $this->GenerateStudentViewHandoutBody($row, $subject_period_assignment_handout_id);
            }
        }

        $table .= "
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        ";

        return $table;
    }

    private function GenerateStudentViewHandoutBody($row, $subject_period_assignment_handout_id){

        // echo $subject_period_assignment_handout_id;
        $student_id = $row['student_id'];
   

        $student = new Student($this->con, $student_id);

        $studentName = $student->GetName();
        // $subject_period_assignment_handout_id = 0;

        $viewedStudentsQuery = $this->con->prepare("SELECT * FROM handout_viewed
            WHERE student_id=:student_id
            AND subject_period_assignment_handout_id=:subject_period_assignment_handout_id");

        $viewedStudentsQuery->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
        $viewedStudentsQuery->bindValue(":student_id", $student_id);
        $viewedStudentsQuery->execute();

        $date_viewed = "";
        $view_count = "";

        if($viewedStudentsQuery->rowCount() > 0){

            $viewedStudentsQuery = $viewedStudentsQuery->fetch(PDO::FETCH_ASSOC);

            $date_viewed = $viewedStudentsQuery['date_creation'];
            $view_count = $viewedStudentsQuery['count'];

            $didViewed = "
                <i class='fas fa-check' style='color: green;'></i>
            ";
        }
        elseif($viewedStudentsQuery->rowCount() == 0){
            $didViewed = "
                <i class='fas fa-times' style='color: orange;'></i>
            ";
        }

        $body = "
            <tbody>
                <tr>
                    <th class='text-center' scope='row'>
                        <div class='form-check'>
                            <input class='form-check-input' type='checkbox' value='' id='flexCheckDefault1' checked/>
                        </div>
                    </th>
                    <td class='text-center' scope='row'>$studentName</td>
                    <td class='text-center' scope='row'>$didViewed</td>
                    <td class='text-center' scope='row'>$date_viewed</td>

                    <td class='text-center' scope='row'>$view_count</td>
                    <td>

                        <button spq_id='' type='button' class='subjectPeriozQuizEdit btn btn-success btn-sm px-3'>
                            <i class='fas fa-pencil'></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        ";

        return $body;
    }
}
?>