
<?php

    class SubjectPeriod{

    private $con, $userLoggedInObj, $sqlData, $userType;

    public function __construct($con, $input, $userLoggedInObj, $userType)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->sqlData = $input;
        $this->userType = $userType;

        if(!is_array($input)){

            $query = $this->con->prepare("SELECT * FROM subject_period 
                WHERE subject_period_id = :subject_period_id
                -- AND teacher_course_id=:teacher_course_id
                ");

            $query->bindParam(":subject_period_id", $input);
            // $query->bindParam(":teacher_course_id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
    }
    public function GetSubjectPeriodTypeName(){
        return isset($this->sqlData['section_num']) ? $this->sqlData["section_num"] : 0; 
    }
    public function GetSubjectPeriodId(){
        return isset($this->sqlData['subject_period_id']) ? $this->sqlData["subject_period_id"] : 0; 
    }
    public function GetSubjectId(){
        return isset($this->sqlData['subject_id']) ? $this->sqlData["subject_id"] : 0; 
    }
    public function GetSubjectTerm(){
        return isset($this->sqlData['term']) ? $this->sqlData["term"] : ""; 
    }

    public function GetTitle(){
        return isset($this->sqlData['title']) ? $this->sqlData["title"] : ""; 
    }
    public function GetDescription(){
        return isset($this->sqlData['description']) ? $this->sqlData["description"] : ""; 
    }
    public function GetTerm(){
        return isset($this->sqlData['term']) ? $this->sqlData["term"] : ""; 
    }
    public function GetThumbnail(){
        return isset($this->sqlData['thumbnail']) ? $this->sqlData["thumbnail"] : ""; 
    }

    public function GetSubjectPeriodQuizId($teacher_id){

        $subject_period_id = $this->GetSubjectPeriodId();
        // $teacher_id = $this->userLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT subject_period_quiz_id FROM subject_period_quiz
            WHERE subject_period_id=:subject_period_id
            AND teacher_id=:teacher_id");

        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->execute();

        return $query->fetchColumn();
    }

    public function create($teacherCourseId){

        $subject_period_id = $this->GetSubjectPeriodId();
        $subject_id = $this->GetSubjectId();

        $description = $this->GetDescription();
        $title = $this->GetTitle();
        $thumbnail = $this->GetThumbnail();
        
        $term = $this->GetTerm();
        
        $generateSubjectAssignmentTeacher = $this->GenerateSubjectAssignmentTeacher($teacherCourseId);

        $switching = "";

        $generateSubjectAssignmentStudent = $this->GenerateSubjectAssignmentStudent($teacherCourseId);

        if($this->userType == "teacher"){
            $switching = $generateSubjectAssignmentTeacher;
        }else if($this->userType == "student"){
            $switching = $generateSubjectAssignmentStudent;
        }

        $addAssignmentBtn = "";
        
        if($this->userType == "teacher"){

            $teacher_id = $this->userLoggedInObj->GetId();

            $_SESSION['subject_id'] = $subject_id; 
            $_SESSION['teacher_course_id'] = $teacherCourseId; 

            // if($this->GetSubjectTerm() === "Prelim"){
            //     $_SESSION['subject_period_id_pre'] = $subject_period_id; 

            // }else if($this->GetSubjectTerm() === "Midterm"){
            //     $_SESSION['subject_period_id_mid'] = $subject_period_id; 
            // }
            // $subject_period_assignment_id = 0;
            $_SESSION['subject_period_id'] = $subject_period_id; 

            $addAssignmentBtn = "

                <a style='margin-right: 10px;' href='add_assignment_handout.php?subject_period_id=$subject_period_id'>
                    <button style='font-weight:bold ' class='btn btn-sm btn-outline-primary'>Give Handout</button>
                </a>
                
                <a  href='add_assignment.php?subject_id=$subject_id&subject_period_id=$subject_period_id&teacher_course_id=$teacherCourseId'>
                    <button style='font-weight:bold ' class='btn btn-sm btn-success'>Give Assignment</button>
                </a>
            
                <a style='margin-left: 10px;'class='ml-3' href='subject_period_quiz.php?teacher_id=$teacher_id&subject_period_id=$subject_period_id'>
                    <button style='font-weight:bold ' class='btn btn-sm btn-primary'>Add Quiz</button>
                </a>

                <a style='margin-left: 10px;'
                    class='ml-3' 
                    href='subject_period_assignment_quiz.php?subject_period_id=$subject_period_id'>
                    <button style='font-weight:bold ' class='btn btn-sm btn-primary'>Add Quiz (New)</button>
                </a>
            ";
        }
        $querySubjectPeriodAss = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_period_id=:subject_period_id
            AND teacher_course_id=:teacher_course_id
            ");
        $querySubjectPeriodAss->bindValue(":subject_period_id", $subject_period_id);
        $querySubjectPeriodAss->bindValue(":teacher_course_id", $teacherCourseId);
        $querySubjectPeriodAss->execute();

        $student_id = $this->userLoggedInObj->GetId();
        
        $progressPerSubjectPeriod = "";

        if($this->userType == "student"){
            $array = [];
            $totalItemsPerSubjectPeriod = 0;
            $totalCompleted = 0;

            $percentage = 0;

            $totalHandoutViewed = 0;

            if($querySubjectPeriodAss->rowCount() > 0){

                $totalItemsPerSubjectPeriod = $querySubjectPeriodAss->rowCount();

                while($row = $querySubjectPeriodAss->fetch(PDO::FETCH_ASSOC)){

                    $subject_period_assignment_id = $row['subject_period_assignment_id'];

                    $subjectPeriodAssignment = new SubjectPeriodAssignment($this->con,
                        $row, $this->userLoggedInObj);
                    
                    $studentPeriodAssignment = new StudentPeriodAssignment($this->con,
                        $subjectPeriodAssignment, $this->userLoggedInObj);

                    $myAss = $studentPeriodAssignment->NumberOfPassedDropboxAssignment($subject_period_assignment_id);
                    $totalCompleted += $myAss;

                    $subject_period_assignment_quiz_class_id = $subjectPeriodAssignment
                        ->GetSubjectPeriodAssignmentQuizClassId();
                    
                    // echo $subject_period_assignment_quiz_class_id;
                    // echo "<br>";
                    $totalCompleted += $studentPeriodAssignment->NumberOfPassedQuizAssignment($subject_period_assignment_quiz_class_id, $student_id);

                    if(!in_array($subject_period_id, $array)){
                        array_push($array, intval($subject_period_id));
                    }

                }
            }

            $arraySubAssHandoutId = [];
            $samp = 0;
            foreach ($array as $key => $subject_period_id) {
                // $ff = $this->GetSubjectPeriodAssignmentHandoutId($value);

                $queryAssignmentHandout = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
                    WHERE subject_period_id=:subject_period_id");
                $queryAssignmentHandout->bindValue(":subject_period_id", $subject_period_id);
                $queryAssignmentHandout->execute();

                if($queryAssignmentHandout->rowCount() > 0){

                    $totalHandoutInTheSubjectPeriod = $queryAssignmentHandout->rowCount();
                    $totalItemsPerSubjectPeriod += $totalHandoutInTheSubjectPeriod;

                    while($row = $queryAssignmentHandout->fetch(PDO::FETCH_ASSOC)){
                        $subject_period_assignment_handout_id = $row['subject_period_assignment_handout_id'];
                        array_push($arraySubAssHandoutId, $subject_period_assignment_handout_id);
                    }
                }

                // $totalSubjectPeriodHandouts = $this->GetTotalSubjectPeriodHandouts($subject_period_id, $arraySubAssHandoutId);
                // // $totalItemsPerSubjectPeriod += $totalSubjectPeriodHandouts[0];
                // $samp = $totalSubjectPeriodHandouts[0];
                // $arraySubAssHandoutId = $totalSubjectPeriodHandouts[1];

            }
            // echo $samp;
            // echo sizeof($arraySubAssHandoutId);
            // echo "<br>";

            foreach ($arraySubAssHandoutId as $key => $subject_period_assignment_handout_id) {

                // echo $subject_period_assignment_handout_id;
                $queryHandoutViewed = $this->con->prepare("SELECT * FROM handout_viewed
                    WHERE subject_period_assignment_handout_id=:subject_period_assignment_handout_id
                    AND student_id=:student_id");

                $queryHandoutViewed->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
                $queryHandoutViewed->bindValue(":student_id", $student_id);
                $queryHandoutViewed->execute();

                if($queryHandoutViewed->rowCount() > 0){
                    $totalHandoutViewed++;
                }
            }

            $totalCompleted += $totalHandoutViewed;

            if($totalItemsPerSubjectPeriod >= $totalCompleted && $totalCompleted != 0){
                $percentage = intval(($totalCompleted/$totalItemsPerSubjectPeriod) * 100);
                
            }

            $progressPerSubjectPeriod = "
                <span>Total $totalItemsPerSubjectPeriod</span>
                <span>Completed: $totalCompleted</span>
                <span>Percentage: $percentage</span>
                <div class='progress'>
                            <div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' 
                            aria-valuenow='$percentage' aria-valuemin='0' aria-valuemax='100'
                            style='width: $percentage%'>$percentage%</div>
                </div>
            ";

            // Student side only.
            
        }

        return "
            <div class='subjectPeriodContainerItem'>
                <div class='align-straight'>
                    <div class='thumbnail'>
                        <a href='edit_subject_period.php?subject_period_id=$subject_period_id'>
                            <img src='$thumbnail'>                     
                        </a>

                    </div>
                    <div class='details'>
                        <h3>$title</h3>
                        <p>At the end of this session, the students should be able to</p>
                        <p>$description</p>
                        $progressPerSubjectPeriod
                        
                    </div>
                </div>
                <div style='display:flex;flex:1; justify-content: flex-end;'>
                     
                    $addAssignmentBtn
                </div>
            </div>
            $switching
        ";
    }
    private function GetTotalSubjectPeriodHandouts($subject_period_id,
        $arraySubAssHandoutId){

        $return = [];

        $totalItemsPerSubjectPeriod = 0;

        $queryAssignmentHandout = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
                WHERE subject_period_id=:subject_period_id");

        $queryAssignmentHandout->bindValue(":subject_period_id", $subject_period_id);
        $queryAssignmentHandout->execute();

        if($queryAssignmentHandout->rowCount() > 0){

            $totalHandoutInTheSubjectPeriod = $queryAssignmentHandout->rowCount();
            $totalItemsPerSubjectPeriod += $totalHandoutInTheSubjectPeriod;

            while($row = $queryAssignmentHandout->fetch(PDO::FETCH_ASSOC)){
                $subject_period_assignment_handout_id = $row['subject_period_assignment_handout_id'];
                array_push($arraySubAssHandoutId, $subject_period_assignment_handout_id);
            }
        }
        array_push($return, $totalItemsPerSubjectPeriod);
        array_push($return, $arraySubAssHandoutId);

        // return $totalItemsPerSubjectPeriod;
        return $return;
    }
    private function NumberOfPassedQuizAssignment($subject_period_assignment_quiz_class_id,
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

    private function NumberOfPassedDropboxAssignment($subject_period_assignment_id){

        $student_id = $this->userLoggedInObj->GetId();
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
    private function GenerateSubjectAssignmentStudent($teacherCourseId){

        $sampArray = [];
        $subjectPeriodTypeName = $this->GetSubjectPeriodTypeName();

        $subject_period_id = $this->GetSubjectPeriodId();

        $output = "
            <table class='table table-hover'>
                <thead>
                    <tr >
                        <th>Section</th>
                        <th class='text-center'>Submitted</th>
                        <th class='text-center'>Score</th>
                        <th class='text-center'>Due</th>
                        <th class='text-center'>Status</th>
                    </tr>
                </thead>
            ";

        // For student handout view
        $queryHandout = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
            WHERE subject_period_id=:subject_period_id");

        $queryHandout->bindValue(":subject_period_id", $subject_period_id);
        $queryHandout->execute();

        if($queryHandout->rowCount() > 0){
            while($row = $queryHandout->fetch(PDO::FETCH_ASSOC)){
                $handout = new SubjectPeriodAssignmentHandout($this->con,
                    $row, $this->userLoggedInObj);
                
                $output .= $handout->CreateStudentAssignmentHandout($subjectPeriodTypeName, $subject_period_id);
            }
        }

        // For Assignment Table
        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            -- FIRST CONDITION
            WHERE (subject_period_id=:subject_period_id 
            AND ass_type=:ass_type_box)
            -- AND teacher_course_id=:teacher_course_id
            -- SECOND CONDITION
            OR subject_period_id=:subject_period_id 
            AND ass_type=:ass_type_quiz
            -- AND teacher_course_id=:teacher_course_id

            -- OR ass_type=:ass_type_quiz
            AND set_quiz=:set_quiz
            ORDER BY dateCreation ASC");

        
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":ass_type_box", "Dropbox");
        // $query->bindValue(":teacher_course_id", $teacherCourseId);

        $query->bindValue(":ass_type_quiz", "Quiz");
        $query->bindValue(":set_quiz", "yes");
        $query->execute();
        
        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                // 2nd
                $period_assignment = new SubjectPeriodAssignment($this->con,
                    $row, $this->userLoggedInObj);
                
                array_push($sampArray, $period_assignment);

                $output .= $period_assignment->createStudentAssignmentSection($teacherCourseId, $subjectPeriodTypeName);
            }
        }

        // For Quiz Table
        $queryQuiz = $this->con->prepare("SELECT * FROM subject_period_quiz_class
            WHERE subject_period_id=:subject_period_id");
        
        $queryQuiz->bindValue(":subject_period_id", $subject_period_id);
        $queryQuiz->execute();
        
        if($queryQuiz->rowCount() > 0){
            while($quiz = $queryQuiz->fetch(PDO::FETCH_ASSOC)){

                $subjectPeriodQuiz = new SubjectPeriodQuiz($this->con, $quiz['subject_period_quiz_id'],
                    $this->userLoggedInObj);

                array_push($sampArray, $subjectPeriodQuiz);
                // Disable for a moment.
                // $output .= $subjectPeriodQuiz->CreateStudentQuizSection($teacherCourseId, $subjectPeriodTypeName,
                //     $quiz['subject_period_quiz_class_id']);
            }
        }

       
        $output .= "
            </table>
        ";
        return $output;
    }
    
    private function GenerateSubjectAssignmentTeacher($teacherCourseId){

        $subject_period_id = $this->GetSubjectPeriodId();
        $subjectPeriodTypeName = $this->GetSubjectPeriodTypeName();

        // $query = $this->con->prepare("SELECT * FROM subject_period_assignment
        //     WHERE subject_period_id=:subject_period_id
        //     AND teacher_course_id=:teacher_course_id");
        
        // $query->bindValue(":teacher_course_id", $teacherCourseId);
        // $query->bindValue(":subject_period_id", $subject_period_id);
        // $query->execute();


         $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            -- FIRST CONDITION
            WHERE (subject_period_id=:subject_period_id 
            AND ass_type=:ass_type_box)
            -- AND teacher_course_id=:teacher_course_id
            -- SECOND CONDITION
            OR subject_period_id=:subject_period_id 
            AND ass_type=:ass_type_quiz
            -- AND teacher_course_id=:teacher_course_id

            -- OR ass_type=:ass_type_quiz
            AND set_quiz=:set_quiz
            ORDER BY dateCreation ASC");

        
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":ass_type_box", "Dropbox");
        // $query->bindValue(":teacher_course_id", $teacherCourseId);

        $query->bindValue(":ass_type_quiz", "Quiz");
        $query->bindValue(":set_quiz", "yes");
        $query->execute();
        
        $output = "
            <table class='table table-sm table-hover table-borderless'>
                <thead>
                    <tr>
                        <th>
                            <a style='color:white; text-decoration: none;'
                            href='assignment.php?subject_period_id=$subject_period_id'>
                                Section
                            </a>
                        </th>
                        <th class='text-center'>Submitted</th>
                        <th class='text-center'>Due</th>
                        <th class='text-center'>Teacher</th>
                        <th class='text-center'>Actions</th>
                    </tr>
                </thead>
            ";

         // For teacher handout view
        $queryHandout = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
            WHERE subject_period_id=:subject_period_id ");

        $queryHandout->bindValue(":subject_period_id", $subject_period_id);
        $queryHandout->execute();

        if($queryHandout->rowCount() > 0){

            while($row = $queryHandout->fetch(PDO::FETCH_ASSOC)){
                $handout = new SubjectPeriodAssignmentHandout($this->con,
                    $row, $this->userLoggedInObj);
                
                $output .= $handout->CreateTeacherAssignmentHandout($subjectPeriodTypeName,
                    $teacherCourseId);
            }
        }
        
        if($query->rowCount() >0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                // 2
                $period_assignment = new SubjectPeriodAssignment($this->con,
                    $row, $this->userLoggedInObj);
                
                $output .= $period_assignment->createTeacherAssignmentSection($teacherCourseId, $subjectPeriodTypeName);
            }
        }

        

        // $queryQuiz = $this->con->prepare("SELECT * FROM subject_period_quiz_class
        //     WHERE subject_period_id=:subject_period_id");
        
        // $queryQuiz->bindValue(":subject_period_id", $subject_period_id);
        // $queryQuiz->execute();

        // if($queryQuiz->rowCount() > 0){
        //     while($quiz = $queryQuiz->fetch(PDO::FETCH_ASSOC)){

        //         $subjectPeriodQuiz = new SubjectPeriodQuiz($this->con, $quiz['subject_period_quiz_id'],
        //             $this->userLoggedInObj);
                
        //         $output .= $subjectPeriodQuiz->CreateTeacherQuizSection($teacherCourseId, $subjectPeriodTypeName);
        //     }
        // }

        $output .= "
            </table>
        ";
        return $output;
    }

    private function createStudentCategory(){

        $query = $this->con->prepare("SELECT * FROM student");
        $query->execute();

            $html = "
                <div class='form-group'>
                    <select class='form-control' name='student_id'>";

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $html .= "
                    <option value='".$row['student_id']."'>".$row['firstname']." ".$row['lastname']."</option>
                ";
            }
            $html .= "</select>
                    </div>";
            return $html;
    }

    public function EditSectionForm($subject_period_id){

        $query = $this->con->prepare("SELECT * FROM subject_period
            WHERE subject_period_id=:subject_period_id");


        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->execute();

        $query = $query->fetch(PDO::FETCH_ASSOC);

        $createEditSectionDropdown = $this->createEditSectionDropdown($query['section_num']);
        
        $onclick = "changeSectionValue(this, $subject_period_id)";

        return "
            <div class='form-group row'>
                $createEditSectionDropdown
                <button onclick='$onclick' type='submit' class='btn btn-sm btn-success'>Save</button>
            </div>
        ";
    }

    private function createEditSectionDropdown($section_num){

        $query = $this->con->prepare("SELECT * FROM subject");
        $query->execute();

        $i = 1;

        $html = "<div class='form-group'>
                <select class='form-control' id='section_value'>";

        while($i <= 10){
            $html .= "
                <option value=$i>$i</option>
            ";
            $i++;
        }
        $html .= "</select>
                </div>";
        return $html;
 
    }


    public function createForm($teacher_course_id){
        
        $teacherCourse = new TeacherCourse($this->con, $teacher_course_id,
            $this->userLoggedInObj);
        
        $subject_id = $teacherCourse->GetCourseSubjectId();

        $periodDropdown = $this->createPeriodDropdown($teacher_course_id, $subject_id);

        return "
            <form action='add_subject_period.php' method='POST' 
                enctype='multipart/form-data'>

                    <div class='form-group'>

                        <input class='mb-3 form-control' type='text' 
                            placeholder='Subject Title' name='title'>
                        
                        <textarea class='mb-3 form-control summernote' name='description' placeholder='Description'></textarea>

                        <input class='form-control mb-3' name='subject_period_upload' placeholder='File' type='file'>


                        <label for=''style='margin-bottom: 10px;'>Period Term</label>
                        $periodDropdown

                        <input name='subject_id' type='hidden' value='$subject_id'>
                        <input type='hidden' value='' name='teacher_course_id' value='$teacher_course_id'>

                    </div>
                    <button type='submit' class='btn btn-primary' name='add_subject_period_term'>Save</button>
                </form>
            ";
    }
    public function AddSubjectPeriod($title, $description, $term,
        $subject_id, $teacher_course_id){

        $image = $_FILES['subject_period_upload'] ?? null;
        $imagePath='';

        if (!is_dir('assets')) {
            mkdir('assets');
        }
        
        if (!is_dir('assets/images')) {
            mkdir('assets/images');
        }

        if (!is_dir('assets/images/subject_period')) {
            mkdir('assets/images/subject_period');
        }
        
        if ($image && $image['tmp_name']) {
            $imagePath = 'assets/images/subject_period' . '/' . $image['name'];
            // mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        $query = $this->con->prepare("INSERT INTO subject_period(term,title,
                description, subject_id, teacher_course_id, thumbnail)
            VALUES(:term,:title,:description, :subject_id, :teacher_course_id, :thumbnail)");
        
        $title = $title . " ($term)";

        $query->bindValue(":term", $term);
        $query->bindValue(":title", $title);
        $query->bindValue(":description", $description);
        $query->bindValue(":subject_id", $subject_id);
        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->bindValue(":thumbnail", $imagePath);

       
        return $query->execute();
    }

    private function createPeriodDropdown($teacher_course_id, $subject_id){


        $checkTermQuery = $this->con->prepare("SELECT term FROM subject_period
            WHERE teacher_course_id= :teacher_course_id
            AND subject_id=:subject_id");
        
        $checkTermQuery->bindValue(":teacher_course_id", $teacher_course_id);
        $checkTermQuery->bindValue(":subject_id", $subject_id);

        $checkTermQuery->execute();

        $allTerm = array("Prelim", "Midterm", "Pre-Final", "Finals");
        $currentTerm = [];
        $output = "";

        // print_r($result);

        $resultx = [];
        if($checkTermQuery->rowCount() > 0){

            $renderedTerm = $checkTermQuery->fetchAll(PDO::FETCH_ASSOC);

            foreach ($renderedTerm as $key => $value) {
                            # code...
                $output =  $value['term'];
                array_push($currentTerm, $output);
            }

            $resultx = array_diff($allTerm, $currentTerm);
        }

        $html2 = "<div class='form-group'>
                <select class='form-control' name='term'>"; 

        $i = 0;
        $resultx= array_values($resultx);
        
        while($i < count($resultx)){
            $html2 .= "
                <option value='" . $resultx[$i] . "'>" . $resultx[$i] . "</option>
            ";
            $i++;
        }

        $html2 .= "</select>
        </div>";

        return $html2;
    }


    public function editSubjectPeriodForm($subject_period_id){
        

        $query = $this->con->prepare("SELECT * FROM subject_period
            WHERE subject_period_id=:subject_period_id");

        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->execute();
        $query = $query->fetch(PDO::FETCH_ASSOC);

        
        $title = $query['title'];
        $teacher_course_id = $query['teacher_course_id'];
        $term = $query['term'];
        $subject_id = $query['subject_id'];
        $thumbnail = $query['thumbnail'];
        $description = $query['description'];

        $periodDropdown = $this->editPeriodDropdown($teacher_course_id,
            $subject_id, $term);
        $image = "";

        if($thumbnail){
            $image = "
                <img class='mb-3' style='height: 270px; width:500px;' src='$thumbnail' alt='Image' >
            ";
        }

        return "
            <form action='edit_subject_period.php?subject_period_id=$subject_period_id' method='POST' 
                enctype='multipart/form-data'>

                    <div class='form-group'>
                        $image
                        <input class='form-control mb-3' name='subject_period_upload' placeholder='File' type='file' value='$thumbnail'>


                        <input class='mb-3 form-control' type='text' 
                            placeholder='Subject Title' name='title' value='$title'>
                        
                        <textarea class='mb-3 form-control summernote' name='description' placeholder='Description'>$description</textarea>

                        <label for=''style='margin-bottom: 10px;'>Period Term</label>
                        $periodDropdown

                        <input type='hidden' name='teacher_course_id' value='$teacher_course_id' >
                        <input type='hidden'  name='assignment_upload_value' value='$thumbnail' >

                    </div>
                    <button type='submit' class='btn btn-primary' name='edit_subject_period_term'>Save</button>
                </form>
            ";
    }

    public function editSubjectPeriod($title, $description, $term,
        $teacher_course_id, $subject_period_id, $image_value){

        $query = $this->con->prepare("SELECT * FROM subject_period
            WHERE subject_period_id=:subject_period_id");

        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->execute();

        $query = $query->fetch(PDO::FETCH_ASSOC);
        
        // echo $query['thumbnail'];

        $image = $_FILES['subject_period_upload'] ?? null;

        print_r($image);

        // if($image['name'] == ""){
        //     echo "empty";
        // }else{
        //     echo "is in there";
        // }

        $imagePath='';

        if (!is_dir('assets')) {
            mkdir('assets');
        }
        
        if (!is_dir('assets/images')) {
            mkdir('assets/images');
        }

        if (!is_dir('assets/images/subject_period')) {
            mkdir('assets/images/subject_period');
        }

        if ($image['name'] != "") {

            // print_r($image);
            if (file_exists($query['thumbnail'])) {
                unlink($query['thumbnail']);
            }

            $imagePath = 'assets/images/subject_period' . '/' . $image['name'];
            // mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
            //
        }
        
        // if($imagePath == 'assets/images/subject_period/' ){
        if($image['name'] == ""){
            // If user didnt edit the image, let the image stay.
            $imagePath = $image_value;
        }

        // echo $image;
        // echo "<br>";
        // echo $imagePath;

        $query = $this->con->prepare("UPDATE subject_period
            SET title=:title, description=:description,
                term=:term, thumbnail=:thumbnail

            WHERE subject_period_id=:subject_period_id
            AND teacher_course_id=:teacher_course_id");

        $query->bindValue(":title", $title);
        $query->bindValue(":description", $description);
        $query->bindValue(":term", $term);

        // if($imagePath != "" && $image != null){
        //     $query->bindValue(":thumbnail", $imagePath);
        // }

        $query->bindValue(":thumbnail", $imagePath);

        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":teacher_course_id", $teacher_course_id);

        if($query->execute()){
            header("Location: assignment.php?teacher_course_id=$teacher_course_id");

        }  
        
    }

    private function editPeriodDropdown($teacher_course_id, $subject_id,
        $term){


        $checkTermQuery = $this->con->prepare("SELECT term FROM subject_period
            WHERE teacher_course_id= :teacher_course_id
            AND subject_id=:subject_id");
        
        $checkTermQuery->bindValue(":teacher_course_id", $teacher_course_id);
        $checkTermQuery->bindValue(":subject_id", $subject_id);

        $checkTermQuery->execute();

        $allTerm = array("Prelim", "Midterm", "Pre-Final", "Finals");
        $currentTerm = [];
        $output = "";

        // print_r($result);

        $resultx = [];
        if($checkTermQuery->rowCount() > 0){

            $renderedTerm = $checkTermQuery->fetchAll(PDO::FETCH_ASSOC);

            foreach ($renderedTerm as $key => $value) {
                            # code...
                $output =  $value['term'];
                array_push($currentTerm, $output);
            }

            $resultx = array_diff($allTerm, $currentTerm);
        }

        $html2 = "<div class='form-group'>
                <select class='form-control' name='term'>"; 

        $i = 0;
        
        array_push($resultx, $term);
        $resultx= array_values($resultx);
        
        while($i < count($resultx)){
            $html2 .= "
                <option value='" . $resultx[$i] . "'>" . $resultx[$i] . "</option>
            ";
            $i++;
        }

        $html2 .= "</select>
        </div>";

        return $html2;
    }
}
?>