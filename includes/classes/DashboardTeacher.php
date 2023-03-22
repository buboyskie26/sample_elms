<?php

    // require_once __DIR__ . '/form-helper/MyStudentAssignmentHelper.php';

    class DashboardTeacher{

    private $con, $teacherLoggedInObj;

    public function __construct($con, $teacherLoggedInObj)
    {
        $this->con = $con;
        $this->teacherLoggedInObj = $teacherLoggedInObj;
    }

    public function create($latestSchoolYear){


        $generateGridItems = $this->GenerateGridItems($latestSchoolYear);

        if($generateGridItems != ""){
            return "
                <div class='videoGridHeader'>
                    <div class='left'>
                        My Class School Year: 2013-2014 (Sample)
                    </div>
                    <div class='right'>
                        <div class='right-first'>
                            <a href='dashboard_teacher_create.php'>
                            <button class='btn btn-success'>Add Class</button>
                            </a>
                        </div>

                        <div>
                            <a href='teacher_student_message.php'>
                                <button class='btn btn-success'>Message Student</button>
                            </a>
                        </div>

                    </div>
                </div>

                <div class='videoGrid'>
                    $generateGridItems
                </div>
            ";
            
        }else{
            return "
                <div class='videoGridHeader'>
                    <div class='right'>
                        <a href='dashboard_teacher_create.php'>
                            <button class='btn btn-success'>Add Class</button>
                        </a>
                    </div>
                </div>
            ";
        }
        

    }
    public function showSchoolYearForm(){

        $showingSchoolYear = $this->showingSchoolYearSelection();

        

        $output = "
            <div class='form-group' >
                <form action='dashboard_teacher.php' method='POST'>
                    $showingSchoolYear

                    <button type='submit' name='search_school_year' class='btn btn-sm btn-primary'>Search</button>
                </form>
            </div>
        ";

        return $output;
    }
    private function showingSchoolYearSelection(){

        $lastQuery = $this->GetLatestSchoolYearAndPeriod();
        $lastQuery = $lastQuery->fetchColumn(1);

        $query = $this->con->prepare("SELECT * FROM school_year");
        $query->execute();

        $html = "<div class='form-group mb-3'>
                <select class='form-control' name='school_year_id'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $html .= "
                <option selected='$lastQuery' value='".$row['school_year_id']."'>".$row['school_year_term']." ".$row['period']."</option>
            ";
        }
        $html .= "</select>
                </div>";

        return $html;
    }
    public function GetLatestSchoolYearAndPeriod(){

        $statuses = "Active";
        $query2 = $this->con->prepare("SELECT school_year_id, school_year_term, statuses FROM school_year 
            WHERE statuses=:statuses
            ORDER BY school_year_id DESC
            LIMIT 1");
        $query2->bindValue(":statuses", $statuses);
        $query2->execute();
        return $query2;
        // return $query2->fetchColumn();
    }

    public function GetLatestSchoolYearAndPeriodStatus(){
        $statuses = "Active";

        $query2 = $this->con->prepare("SELECT statuses FROM school_year
            WHERE statuses=:statuses 
            LIMIT 1");

        $query2->bindValue(":statuses", $statuses);
        $query2->execute();
        return $query2->fetchColumn();
        // return $query2->fetchColumn();
    }
    private function GenerateGridItems($latestSchoolYear){

        $teacher_id = $this->teacherLoggedInObj->GetId();
        $statuses = "Active";

        $query = $this->con->prepare("SELECT * FROM teacher_course
            WHERE teacher_id=:teacher_id 
            ORDER BY teacher_course_id DESC");

        // $query->bindValue(":statuses", $statuses);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->execute();

        $array = array();

        // echo $latestSchoolYear;
        

        $outputHtml = "";

        if($query->rowCount() > 0){

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

            if($row['school_year_id'] === $latestSchoolYear)
                array_push($array, $row['school_year_id']);

            $teacherCourse = new TeacherCourse($this->con, $row, $this->teacherLoggedInObj);
            $item = new DashboardTeacherItem($this->con, $row, $teacherCourse);

            // If teacher have the same school year_id
            // to the latest school year, that school year section
            // must be show up
            if(sizeof($array) > 0)
                $outputHtml .= $item->create();
            else{
                // $outputHtml = "nothing";
            }
            // echo sizeof($array);

        }
            return $outputHtml;
        }else{

            return "";
        }
        
    }

    
    public function createForm(){
        
        $createCourseCategory = $this->createCourseCategory();
        $createSubjectCategory = $this->createSubjectCategory();
        $selectSchoolYear = $this->selectSchoolYear();

// <input class='form-control mb-3' type='text' 
//                             placeholder='School Year' name='school_year'>

        return "
            <form action='dashboard_teacher.php' method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        $createCourseCategory
                        $createSubjectCategory
                        <input type='text' name='school_year' class='form-control mb-3'>
                        $selectSchoolYear

                        <input type='file' name='thumbnail' class='form-control mb-3'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_teacher_course'>Save</button>
            </form>
        ";
    }

    public function insertTeacherCourse($course_id, $teacher_id, $subject_id,
        $school_year_id, $thumbnail){


        $image = $_FILES['thumbnail'] ?? null;
        $imagePath='';

        if (!is_dir('assets')) {
            mkdir('assets');
        }
        
        if (!is_dir('assets/images')) {
            mkdir('assets/images');
        }
        if (!is_dir('assets/images/teacher_course_thumbnail')) {
            mkdir('assets/images/teacher_course_thumbnail');
        }

        if ($image && $image['tmp_name']) {
            $imagePath = 'assets/images/teacher_course_thumbnail' . '/' . $image['name'];
            // mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        $query = $this->con->prepare("INSERT INTO teacher_course(course_id,teacher_id, subject_id,
            school_year_id, thumbnail)
            VALUES(:course_id, :teacher_id, :subject_id, :school_year_id, :thumbnail)");
        
        $query->bindValue(":course_id", $course_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->bindValue(":subject_id", $subject_id);
        // $query->bindValue(":school_year", $school_year);
        $query->bindValue(":school_year_id", $school_year_id);
        $query->bindValue(":thumbnail", $imagePath);

        return $query->execute();
    }

    private function selectSchoolYear(){

       
        $query = $this->con->prepare("SELECT * FROM school_year");
        $query->execute();

        $html = "<div class='form-group mb-3'>
                <select class='form-control' name='school_year_id'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $html .= "
                <option  value='".$row['school_year_id']."'>".$row['school_year_term']." ".$row['period']."</option>
            ";
        }
        $html .= "</select>
                </div>";

        return $html;

    }
    private function createCourseCategory(){

        $query = $this->con->prepare("SELECT * FROM course");
        $query->execute();

            $html = "<div class='form-group'>
                    <select class='form-control mb-3' name='course_id'>";

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

            $html = "<div class='form-group mb-3'>
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
  

    public function ShowingSelectedSchoolYear($school_year_id, $latestSchoolYear){

        echo $school_year_id;
        $query = $this->con->prepare("SELECT * FROM teacher_course
            WHERE school_year_id=:school_year_id");
        
        $query->bindValue(":school_year_id", $school_year_id);
        
        $query->execute();
        
        // $latest = $this->GetLatestSchoolYearAndPeriod();
        // $isNow = $latest == $school_year_id;

        // echo $latest;
        // echo $school_year_id;
        if($query->rowCount() > 0){
            header("Location: dashboard_teacher.php?search_school_year=$school_year_id");
            // return $this->create($latestSchoolYear);
        }else{
            // echo "No data was found in this year.";
            // exit();
        }
        
    }

    public function createTeacherDashboardSearch($search_school_year){

        $searchDashBoard = $this->GenerateSearchDashboard($search_school_year);

        if($searchDashBoard != ""){
            return "
                <div class='videoGridHeader'>
                    <div class='left'>
                        My Class School Year: 2013-2014 (Sample)
                    </div>

                    <div class='right'>
                        <a href='dashboard_teacher_create.php'>
                            <button class='btn btn-success'>Add Class</button>
                        </a>
                        <a href='teacher_student_message.php'>
                            <button class='btn btn-success'>Message To Student</button>
                        </a>
                    </div>
                </div>

                <div class='videoGrid'>
                    $searchDashBoard
                </div>
            ";
            
        }
    }
    private function GenerateSearchDashboard($search_school_year){

        $teacher_id = $this->teacherLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM teacher_course
            WHERE teacher_id=:teacher_id AND school_year_id=:school_year_id
            ORDER BY teacher_course_id DESC");

        // $query->bindValue(":statuses", $statuses);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->bindValue(":school_year_id", $search_school_year);
        $query->execute();

        $array = array();

        // echo $latestSchoolYear;
        

        $outputHtml = "";

        if($query->rowCount() > 0){

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $teacherCourse = new TeacherCourse($this->con, $row, $this->teacherLoggedInObj);
            $item = new DashboardTeacherItem($this->con, $row, $teacherCourse);
            
            $outputHtml .= $item->create();
        }
            return $outputHtml;
        }else{
            return "";
        }
    }

    public function TeacherListAssignmentToBeChecked(){

        $school_year = $this->con->prepare("SELECT school_year_id FROM school_year
            WHERE statuses= 'Active'
            LIMIT 1
            ");
        
        $teacher_id = $this->teacherLoggedInObj->GetId();

        $school_year->execute();

        $active_school_year_id = $school_year->fetchColumn();

       


        $totalAssignmentDueToCheck =  $this->NumberOfTotalAssignmentTobeChecked($teacher_id);

        $output = "
            <div class=''>
                <h1>Todo</h1>
                    <div class='row'>
                        <h5>$totalAssignmentDueToCheck Assignments due</h5>

                    <div class='col-md-12'>
                        <ul class='list-group'>
        ";

         // All of your given assignments aligned with your teacher course.
        $teacher_id = $this->teacherLoggedInObj->GetId();

        // The school year depends on the admin which school year should we
        // dependent. get the setted school year and place it to WHERE
        $statement = $this->con->prepare("SELECT subject_id, teacher_course_id FROM teacher_course
            WHERE teacher_id=:teacher_id
            AND school_year_id=:school_year_id
            ");
        
        $statement->bindValue(":teacher_id", $teacher_id);
        $statement->bindValue(":school_year_id", $active_school_year_id);

        $statement->execute();



        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            
            $subject_id = $row['subject_id'];
            $teacher_course_id = $row['teacher_course_id'];


            $output .= $this->GenerateTodoBody($subject_id, $teacher_course_id);
            
        }

        $output .= "
                    </ul>
                </div>
            </div>
        </div>
        ";
        
       
        return $output;
    }
    private function GenerateTodoBody($subject_id,
        $teacher_course_id){

        // echo $subject_id;
        
        $output = "";

        $totalAssignmentToBeChecked = 0;

        $totalAssignmentToBeChecked = $this->NumberOfPassedAssignmentOnTeacherCourse($teacher_course_id);

        $statement = $this->con->prepare("SELECT subject_title FROM subject
            WHERE subject_id=:subject_id");
        
        $statement->bindValue(":subject_id", $subject_id);
        $statement->execute();

        while($row = $statement->fetch(PDO::FETCH_ASSOC)){

            $subject_title = $row['subject_title'];

            $output .= "
                <a href='teacher_todo_assignment.php?teacher_course_id=$teacher_course_id'>
                    <li class='list-group-item'>$subject_title  ($totalAssignmentToBeChecked)</li> 
                </a>
            ";
        }
        
        return $output;
    }

    private function NumberOfPassedAssignmentOnTeacherCourse($teacher_course_id){
        
        $sql = $this->con->prepare("SELECT * FROM subject_period_assignment t1
            INNER JOIN student_period_assignment t2 
            ON t1.subject_period_assignment_id = t2.subject_period_assignment_id
            WHERE t1.teacher_course_id=:teacher_course_id
            AND t2.grade= 0
            ORDER BY t2.passed_date");

        $sql->bindValue(":teacher_course_id", $teacher_course_id);
        $sql->execute();

        return $sql->rowCount();
    }
    private function NumberOfTotalAssignmentTobeChecked($teacher_id){

        $sql = $this->con->prepare("SELECT * FROM subject_period_assignment t1
            INNER JOIN student_period_assignment t2 
            ON t1.subject_period_assignment_id = t2.subject_period_assignment_id

            WHERE t1.teacher_id=:teacher_id
            AND t2.grade= 0
            ORDER BY t2.passed_date");
        
        $sql->bindValue(":teacher_id", $teacher_id);
        $sql->execute();

        return $sql->rowCount();
    }
}   

?>