<?php

    class MyStudentAssignmentView{

    private $con, $teacherUserLoggedInObj, $subjectPeriodAssignment, $student_period_assignment_id;

    public function __construct($con, $teacherUserLoggedInObj, $subjectPeriodAssignment, $student_period_assignment_id)
    {
        $this->con = $con;
        $this->teacherUserLoggedInObj = $teacherUserLoggedInObj;
        $this->subjectPeriodAssignment = $subjectPeriodAssignment;
        $this->student_period_assignment_id = $student_period_assignment_id;
    }

    public function create($teacher_course_id){

        $type_name = $this->subjectPeriodAssignment->GetTypeName();

        $generateStudentPassedAssignment = $this->GenerateStudentPassedAssignmentv2();

        $teacherCourse = new TeacherCourse($this->con, $teacher_course_id, $this->teacherUserLoggedInObj);
        $courseName = $teacherCourse->GetCourseName();
        $courseCode = $teacherCourse->GetCourseSubjectCode();
        $schoolYear = $teacherCourse->GetSchoolYear();

        $tabsSection = $this->createTabs();

        $output = "
            <div class='student_assignment_header'>
                <span>$courseName / $courseCode / School Year: $schoolYear / Assignment Section</span>
            </div>

            <div class='student_assignment_table_header'>
                <span>Viewing Assignment in : $type_name</span>
            </div>
 
            <div class='content_outer'>
                $tabsSection
                <div class='tab-content channelContent' id='myTabContent'>
                    $generateStudentPassedAssignment
                </div>
            </div>
        ";
        return $output;
    }
    private function createTabs(){

        return "
            <ul class='nav nav-tabs' role='tablist'>
                <li class='nav-item'>
                    <a class='nav-link active' id='instructions-tab_t' data-bs-toggle='tab' 
                        href='#instructions_t' role='tab' aria-controls='instructions_t' aria-selected='true'>
                        Instructions
                    </a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' id='submission-tab_t' data-bs-toggle='tab' href='#submission_t' role='tab' 
                        aria-controls='submission_t' aria-selected='false'>
                        Submissions
                    </a>
                </li>
            </ul>
        ";
    }
    
    private function GenerateStudentPassedAssignment(){

        $student_period_assignment_id = $this->student_period_assignment_id;


        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE student_period_assignment_id=:student_period_assignment_id
            LIMIT 1");
        
        $query->bindValue(":student_period_assignment_id", $student_period_assignment_id);
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

    private function GenerateStudentPassedAssignmentv2(){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        $ass_type = $this->subjectPeriodAssignment->GetSubjectPeriodAssType();
        $student_period_assignment_id = $this->student_period_assignment_id;
        
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND is_final=:is_final
            AND student_period_assignment_id=:student_period_assignment_id
            LIMIT 1");

        $query->bindValue(":student_period_assignment_id", $student_period_assignment_id);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":is_final", "yes");
        $query->execute();

        $output = "";
        
        if($query->rowCount() > 0){

            $query = $query->fetch(PDO::FETCH_ASSOC);
            
            $student_period_assignment_id = $query['student_period_assignment_id'];

            $currentGrade = $query['grade'];
            $dateCreation = $this->subjectPeriodAssignment->GetSubjectPeriodCreation();
            $dateCreation = date("M j", strtotime($dateCreation));

            $due_date = $this->subjectPeriodAssignment->GetDueDate();
            $due_date = date("F j, g:i a", strtotime($due_date));

            $maxScore = $this->subjectPeriodAssignment->GetMaxScore();
            $percentageScore = ($currentGrade/$maxScore) * 100;

            $student = new Student($this->con, $query['student_id']);

            $student_id = $student->GetId();

            $description = $query['description'];
            $student_name = $student->GetName();

            $subject_description =  $this->subjectPeriodAssignment->GetSubjectPeriodDescription();

            $btnClick = "markSTudentAssignment(this, $student_period_assignment_id,
                $subject_period_assignment_id, $student_id)";

            $submission_pic = $query['assignment_file'];
 
            $max_submission = $this->subjectPeriodAssignment->GetMaxSubmission();
            $totalAttempts = $this->TotalAttempts($subject_period_assignment_id, $student_id);
            $allow_late_submission = $this->subjectPeriodAssignment->AllowLateSubmission();

            $passed_date = $query['passed_date'];
            $passed_date = date("F j, g:i a", strtotime($passed_date));

            $rightColumn = $this->rightColumn($maxScore, $dateCreation, $due_date,
                $currentGrade, $percentageScore, $max_submission,
                $totalAttempts, $allow_late_submission, $passed_date, $ass_type);


            // <ul class='nav nav-tabs' role='tablist'>
            //     <li class='nav-item'>
            //         <a class='nav-link active' id='instructions-tab' data-toggle='tab' 
            //             href='#instructions' role='tab' aria-controls='instructions' aria-selected='true'>
            //             Instructions
            //         </a>
            //     </li>
            //     <li class='nav-item'>
            //         <a class='nav-link' id='submission-tab' data-toggle='tab' href='#submission' role='tab' 
            //             aria-controls='submission' aria-selected='false'>
            //             Submissions
            //         </a>
            //     </li>
            // </ul>

            // yuyu

            // Image array populate.
            
            $images = $this->PopulateStudentImagesAnswer($student_period_assignment_id, $student_id);

            $output = "
                <div class='tab-pane fade show active' id='instructions_t' role='tabpanel' aria-labelledby='home-tab'>
                    <div class='student_assignment_table_content' >
                        <div class='leftColumn_assignment'>
                            <h3>Instructions</h3>
                            <div class='description_container'>
                                <p class='description'>
                                    $subject_description
                                </p>
                            </div>
                        </div>

                        $rightColumn
                    </div>

                </div>

                <div class='tab-pane fade' id='submission_t' role='tabpanel' aria-labelledby='profile-tab'>
                    <div class='student_assignment_table_content' >
                        <div class='leftColumn_assignment'>
                            <h3>Submissions</h3>
                            
                            <div class='student_answer'>
                                <h3 class='mb-3'>$student_name Answer</h3>
                                <textarea id='summernoteDisabled' class='form-control summernote' name='assignment_description'>$description</textarea>
                            </div>

                           $images

                            <div style='margin-left: 1px; margin-top: 12px;' class='form-group row col-3'>
                                <input class='form-control mb-3' type='text' value='$currentGrade' id='mark_value'>
                                <button type='submit' onclick='$btnClick' class='btn btn-sm btn-success'>Mark Grade</button>
                            </div>
                        </div>

                        $rightColumn
                    </div>
                </div>
            ";

            }
            return $output;
    }
    private function PopulateStudentImagesAnswer($student_period_assignment_id, $student_id){

        $queryStudentAnswers = $this->con->prepare("SELECT * FROM student_period_assignment_file
                WHERE student_period_assignment_id=:student_period_assignment_id
                AND student_id=:student_id");
            
        $queryStudentAnswers->bindValue(":student_period_assignment_id", $student_period_assignment_id);
        $queryStudentAnswers->bindValue(":student_id", $student_id);
        $queryStudentAnswers->execute();

        $images = "";

        if($queryStudentAnswers->rowCount() > 0){
            while($rowAns = $queryStudentAnswers->fetch(PDO::FETCH_ASSOC)){
                $submission_pic = $rowAns['assignment_file_path'];

                $images .= "
                    <div class='submission_picture'>
                        <a href='$submission_pic' target='_blank' rel='noopener noreferrer'>
                            <img style='height: 270px; width:500px;' src='$submission_pic'>
                        </a>
                        <a href='$submission_pic' download='$submission_pic'>    
                            <span class='mt-3'>$submission_pic</span>
                        </a>
                    </div>
                ";
            }
        }else{
            $images = "No image answer.";

        }

        return $images;
    }
    private function TotalAttempts($subject_period_assignment_id, $student_id){

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        return $query->rowCount();
    }

    private function rightColumn($maxScore, $dateCreation, $due_date,
        $currentGrade, $percentageScore, $max_submission, $totalAttempts,
        $allow_late_submission, $passed_date, $ass_type){

        $doesAllow = "
            <p>Allow late submissions: <i style='color: red;' class='fas fa-times'></i></p>
        ";
        if($allow_late_submission === "yes"){
            $doesAllow = "
                <p>Allow late submissions: <i style='color: green;' class='fas fa-check'></i></p>
            ";
        }

        $isChecked = $this->CheckedSubmittedAssignment();

        $waitingForGrade = "
            <h4>Score: <span>$currentGrade/$maxScore</span></h4> <h3>$percentageScore%</h3>
        ";

        if($isChecked == false){
            $waitingForGrade = "
                <h4>Score</h4>
                <div>
                    <i style='color:orange;' class='fas fa-flag'></i> <span>Not Checked</span>
                </div>
            ";
        }
        

        $output = "
            <div class='rightColumn_assignment'>
                <div class='assignment_container'>
                        <div class='assignment_first'>
                            <h3>Assignment</h3>
                            <p>Type: $ass_type</p>
                            <p>Max score: $maxScore</p>
                            <p>Start: $dateCreation</p>
                            <p>Due: $due_date</p>
                        </div>
                        <div class='assignment_score'>
                            $waitingForGrade
                        </div>
                        <div class='submission_details'>
                            <div class='submission_details_inner'>
                                <h3>Submission</h3>
                                <p>Submitted: $passed_date</p>
                                <p>Attempts: $totalAttempts</p>
                                <p>Max attempts: $max_submission</p>
                                $doesAllow
                            </div>
                        </div>
                </div>

                
            </div>    
        ";
        return $output;
    }

    private function CheckedSubmittedAssignment() : bool{

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();

        $student_period_assignment_id = $this->student_period_assignment_id;

        $getStudPeriodAss = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE student_period_assignment_id=:student_period_assignment_id
            LIMIT 1");
        
        $getStudPeriodAss->bindValue(":student_period_assignment_id", $student_period_assignment_id);
        $getStudPeriodAss->execute();
        $getStudPeriodAss = $getStudPeriodAss->fetch(PDO::FETCH_ASSOC);


        $student_id = $getStudPeriodAss['student_id'];


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
    public function CheckedStudentAssignment($grade_checked_number, $student_period_assignment_id){

     
        $query = $this->con->prepare("UPDATE student_period_assignment
            SET grade=:grade
            WHERE student_period_assignment_id=:student_period_assignment_id");

        $query->bindValue(":grade", $grade_checked_number);
        $query->bindValue(":student_period_assignment_id", $student_period_assignment_id);
        
        return $query->execute();

    }
}
?>
