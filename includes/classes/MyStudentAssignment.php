<?php

    class MyStudentAssignment{

    private $con, $teacherUserLoggedInObj, $subjectPeriodAssignment;

    public function __construct($con, $teacherUserLoggedInObj, $subjectPeriodAssignment)
    {
        $this->con = $con;
        $this->teacherUserLoggedInObj = $teacherUserLoggedInObj;
        $this->subjectPeriodAssignment = $subjectPeriodAssignment;
    }

    public function OtherSubmissions($student_id){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id= :subject_period_assignment_id 
            AND student_id= :student_id");

        // $query = $this->con->prepare("SELECT * FROM student_period_assignment 
        //     WHERE subject_period_assignment_id= :subject_period_assignment_id 
        //     AND student_period_assignment_id != (SELECT MAX(student_period_assignment_id) 
        //     AND student_id= :student_id
        //     FROM student_period_assignment)");
            
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        $table = "";

        // The other submission should not have a grade input, while the latest
        // must bw provided.
        
        if($query->rowCount() > 0){
            $table = "
                <table class='table  table-hover'>
                    <thead>
                        <tr>
                            <th class='text-center'>Date Upload</th>
                            <th class='text-center'>File Name</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
            ";

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $date = $row['passed_date'];
                $file_name = $row['file_name'];
                $am_pm = date("g:i a", strtotime($date));
                $datex = date("M j", strtotime($date));
                // $datex = date("d-m-y", strtotime($date));
                $datex .= " ".$am_pm;

                $table .= "
                    <tbody>
                        <tr>
                            <td class='text-center'>$datex</td>
                            <td class='text-center'>$file_name</td>
                        </tr>
                    </tbody>
                ";
            }
        } 
        $table .= "
            </table>
        ";
        return $table;
    }
    public function ShowListStudentAnsweredAssignment($teacher_course_id){

        $showingStudentPassedAssignment = $this->ShowingStudentPassedAssignment($teacher_course_id);
        $type_name = $this->subjectPeriodAssignment->GetTypeName();

        $teacherCourse = new TeacherCourse($this->con, $teacher_course_id, $this->teacherUserLoggedInObj);
        $courseName = $teacherCourse->GetCourseName();
        $courseCode = $teacherCourse->GetCourseSubjectCode();
        $schoolYear = $teacherCourse->GetSchoolYear();
        
        $output = "
            <div class='student_assignment_header'>
                <span>$courseName / $courseCode / School Year: $schoolYear / Uploaded Assignments</span>
            </div>

            <div class='student_assignment_table_header'>
                <span>All students submitted in : $type_name</span>
            </div>

            <div class='student_assignment_table_contentr'>
                $showingStudentPassedAssignment
            </div>
        ";
        return $output;
    }

    // rr
    private function ShowingStudentPassedAssignment($teacher_course_id){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();

        // Kagulo na PIOTANGINA
        // $query = $this->con->prepare("SELECT *  FROM student_period_assignment
        //     WHERE subject_period_assignment_id=:subject_period_assignment_id
        //     ORDER BY passed_date DESC");

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE student_period_assignment_id IN(SELECT DISTINCT MAX(student_period_assignment_id) 
            -- WHERE subject_period_assignment_id=:subject_period_assignment_id

            FROM student_period_assignment 
            WHERE subject_period_assignment_id=:subject_period_assignment_id

            GROUP BY student_id)");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();
        $table = "
                <table class='table  table-hover'>
                    <thead>
                        <tr>
                            <th class='text-center'>Date Upload</th>
                            <th class='text-center'>File Name</th>
                            <th class='text-center'>SUBMITTED BY:</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
            ";

        if($query->rowCount() > 0){
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $student_id = $row['student_id'];

                $checkOtherStudentSubmission = $this->con->prepare("SELECT * FROM student_period_assignment
                    WHERE subject_period_assignment_id=:subject_period_assignment_id
                    AND student_id=:student_id");

                $checkOtherStudentSubmission->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
                $checkOtherStudentSubmission->bindValue(":student_id", $student_id);
                $checkOtherStudentSubmission->execute();

                $student_user = new Student($this->con, $row['student_id']);

                $date = $row['passed_date'];
                $file_name = $row['file_name'];
                $student_name = $student_user->GetName();
                $am_pm = date("g:i a", strtotime($date));
                $datex = date("M j", strtotime($date));
                // $datex = date("d-m-y", strtotime($date));
                $datex .= " ".$am_pm;

                $student_id = $student_user->GetId();

                $student_period_assignment_id = $row['student_period_assignment_id'];

                $studenOtherSubmission = $checkOtherStudentSubmission->rowCount();

                $studenOtherSubmissionOutput = 2;

                if($studenOtherSubmission > 0){
                    $studenOtherSubmissionOutput = "
                        <a href='my_student_assignment.php?subject_period_assignment_id=$subject_period_assignment_id&student_id=$student_id'>
                            <button class='btn btn-sm btn-success' title='Other submission'>
                                <i class='fa fa-book'></i> $studenOtherSubmission
                            </button>
                        </a>
                    ";
                }
                $table .= "
                    <tbody>
                        <tr>
                            <td class='text-center'>$datex</td>
                            <td class='text-center'>$file_name</td>
                            <td class='text-center'>$student_name</td>
                            <td>
                                <button class='btn btn-sm btn-primary' title='Download'>
                                    <i class='fas fa-download'></i>
                                </button>
                                 <a href='my_student_assignment_view.php?subject_period_assignment_id=$subject_period_assignment_id&student_period_assignment_id=$student_period_assignment_id&tc_id=$teacher_course_id'>
                                    <button title='View student assignment'>
                                        <i class='fa fa-eye'></i>
                                    </button>
                                </a>
                                <a href=''>
                                    $studenOtherSubmissionOutput
                                </a>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                ";
            }
        }else{
            echo "No submission yet.";
        }

        $table .= "
            </table>
        ";

        return $table;
    }

    private function ShowingStudentPassedAssignmentv2(){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();

        // Kagulo na PIOTANGINA
        // $query = $this->con->prepare("SELECT *  FROM student_period_assignment
        //     WHERE subject_period_assignment_id=:subject_period_assignment_id
        //     ORDER BY passed_date DESC");

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE student_period_assignment_id IN(SELECT DISTINCT MAX(student_period_assignment_id) 
            -- WHERE subject_period_assignment_id=:subject_period_assignment_id

            FROM student_period_assignment 
            WHERE subject_period_assignment_id=:subject_period_assignment_id

            GROUP BY student_id)");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();
        
        $output = "

            <div class='leftColumn_assignment'>
                <h3>Instructions</h3>
                <div class='description_container'>
                    <p class='description'>
                    The Strategy pattern allows developers to define a family of algorithms, put each of them into a separate class, and make their objects interchangeable. Use NetBeans to create a Java program that implements this design pattern. Name your project TestStrategy.
                    </p>
                </div>

                <div class='form-group row col-3'>
                    <input class='form-control mb-3' type='text'>
                    <button class='btn btn-sm btn-success'>Mark Grade</button>
                </div>

            </div>

            <div class='rightColumn_assignment'>
                <div class='assignment_container'>
                    <div class='assignment_first'>
                        <h3>Assignment</h3>
                        <p>Type: Dropbox</p>
                        <p>Max score: 50</p>
                        <p>Start: Nov 3</p>
                        <p>Due: Nov 17, 11:58 pm</p>
                    </div>
                    <div class='assignment_second'>
                        <p>Score: <span>50/50</span></p>
                    </div>
                </div>
            </div>
        ";

        return $output;
    }

    public function create(){


        $generateStudentPassedAssignment = $this->GenerateStudentPassedAssignment();

        $output = "
            <div class='student_assignment_header'>
                <span>BSCS-501 / APPDEV101 / School Year: 2013-2014 / Uploaded Assignments</span>
            </div>

            <div class='student_assignment_table_header'>
                <span>Submit Assignment in : Town Hall Base</span>
            </div>

            <div class='student_assignment_table_content'>
                $generateStudentPassedAssignment
            </div>
        ";
        return $output;
    }

    private function GenerateStudentPassedAssignment(){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();


        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id");
        
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();

        if($query->rowCount() > 0){

            $table = "
                <table class='table  table-hover'>
                    <thead>
                        <tr>
                            <th class='text-center'>Date Upload</th>
                            <th class='text-center'>File Name</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
            ";

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $file_name = $row['file_name'];
                $current_grade = $row['grade'];
                $student_period_assignment_id = $row['student_period_assignment_id'];

                $date = $row['passed_date'];

                $am_pm = date("g:i a", strtotime($date));
                $datex = date("M j", strtotime($date));
                // $datex = date("d-m-y", strtotime($date));
                $datex .= " ".$am_pm;

                $teacher_id = 0;
                $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
                 
                $buttonAction = "inputGrade(this, $subject_period_assignment_id)";

                $action = "my_student_assignment.php?subject_period_assignment_id=$subject_period_assignment_id";

                $table .= "
                    <tbody>
                        <tr>
                            <td>$datex</td>
                            <td>$file_name</td>
                            <td>
                                <button class='btn btn-sm btn-primary' title='Download'>
                                    <i class='fas fa-download'></i>
                                </button>
                            </td>
                            <td width='140'>
                                <form action='$action' method='POST' style='display: flex; align-items: center;'>

                                    <input type='text' maxlength='3' size='3' name='grade_checked_number' value='$current_grade'>
                                    <input type='hidden' name='student_period_assignment_id' value='$student_period_assignment_id'>
                                    <button type='submit' name='checked_student_assignment_btn' style='margin-left: 5px;font-weight:bold;'
                                        class='btn btn-sm btn-success'>Save</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                ";
            }

            $table .= "
                </table>
            ";
        }
        return $table;
    }

    public function CheckedStudentAssignment($grade_checked_number, $student_period_assignment_id){

     
        $query = $this->con->prepare("UPDATE student_period_assignment
            SET grade=:grade
            WHERE student_period_assignment_id=:student_period_assignment_id");

        $query->bindValue(":grade", $grade_checked_number);
        $query->bindValue(":student_period_assignment_id", $student_period_assignment_id);
        
        return $query->execute();

    }
    public function ShowListStudentAnsweredQuiz($teacher_course_id){
        
        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        $subject_period_assignment_quiz_class_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentQuizClassId();
        $student_period_assignment_quiz_id = $this->subjectPeriodAssignment
            ->GetStudentQuizClassTable($subject_period_assignment_quiz_class_id, 0);

        $student_idx = $this->ReturnStudentComingFromTeacherCourse();
        
        // echo sizeof($student_idx);


        $table = "
            <table class='table  table-hover'>
                <thead>
                    <tr>
                        <th class='text-center'>Student Name</th>
                        <th class='text-center'>Score</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
        ";

        // $query = $this->con->prepare("SELECT * FROM ");
        
        // $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        // $query->bindValue(":student_id", $student_id);

        // $query->execute();
        



        $table .= "</table";

        return $table;


    }
    private function ReturnStudentComingFromTeacherCourse(){
        
        $arr = [];
        $teacher_course_id = $_GET['tc_id'];
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

    // private function ShowingStudentPassedAssignment($teacher_course_id)
    public function AllStudentPassedAssignmentOnTeacherCourse($teacher_course_id){


        $table = "
            <table class='table  table-hover'>
                <thead>
                    <tr>
                        <th class='text-center'>Submitted By:</th>
                        <th class='text-center'>Date Upload</th>
                        <th class='text-center'>File Name</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
        ";

        $sql = $this->con->prepare("SELECT * FROM subject_period_assignment t1
            INNER JOIN student_period_assignment t2 
            ON t1.subject_period_assignment_id = t2.subject_period_assignment_id
            WHERE t1.teacher_course_id=:teacher_course_id
            AND t2.grade= 0
            ORDER BY t2.passed_date");

        $sql->bindValue(":teacher_course_id", $teacher_course_id);
        $sql->execute();

        if($sql->rowCount() > 0){
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){

                // $file_name = $row['file_name'];

                $table .= $this->PassedAssignmentOnTeacherCourseBody($row, $teacher_course_id);
            }
        }

        $table .= "
            </table>
        ";

        return $table;
        
    }

    private function PassedAssignmentOnTeacherCourseBody($row, $teacher_course_id){

        $subject_period_assignment_id = $row['subject_period_assignment_id'];
        $student_period_assignment_id = $row['student_period_assignment_id'];
        $tc_id = $row['subject_period_assignment_id'];

        $file_name = $row['file_name'];
        $student_id = $row['student_id'];

        $student = new Student($this->con, $student_id);

        $studentName = $student->GetName();
        $date = $row['passed_date'];

        $am_pm = date("g:i a", strtotime($date));
        $datex = date("M j,", strtotime($date));
        // $datex = date("d-m-y", strtotime($date));
        $datex .= " ".$am_pm;

        $output = "";

        $output = "
        
            <tbody>
                <tr>
                    <td class='text-center'>$studentName</td>
                    <td class='text-center'>$datex</td>
                    <td class='text-center'>
                        <a href='my_student_assignment_view.php?subject_period_assignment_id=$subject_period_assignment_id&student_period_assignment_id=$student_period_assignment_id&tc_id=$teacher_course_id'>
                            <button class='btn btn-sm btn-success'>Check</button>
                        </a>
                    </td>
                </tr>
            </tbody>
            
        ";

        return $output;
    }
    
}

?>

<div>
</div>