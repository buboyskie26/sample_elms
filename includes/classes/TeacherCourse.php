<?php

    class TeacherCourse{

    private $con, $userLoggedIn, $sqlData;

    public function __construct($con, $input, $userLoggedIn)
    {
        $this->con = $con;
        $this->userLoggedIn = $userLoggedIn;
        
        if(!is_array($input)) {

            if(!is_array($userLoggedIn)){
                // If $userLoggedIn is an id (int)
                $teacher_id = $userLoggedIn;

                // print_r($teacher_id);
            }
            if(is_array($userLoggedIn)){
                // If $userLoggedIn is an object It should be Teacher Object 
                $teacher_id =  $this->userLoggedIn->GetId();
                echo $teacher_id;
            }
            // DashboardStudent Line 66 is the issue
            // Some of TeacherCourse Object passed the 3rd parameters as student
            
            $query = $this->con->prepare("SELECT * FROM teacher_course 
                WHERE teacher_course_id = :teacher_course_id 
                -- AND teacher_id=:teacher_id
                ");

            $query->bindValue(":teacher_course_id", $input);
            // $query->bindValue(":teacher_id", $teacher_id);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $this->sqlData = $input;
        }
    }
    public function GetTeacherCourseTeacherId() : int {
        return isset($this->sqlData['teacher_id']) ? $this->sqlData["teacher_id"] : 0; 
    }
    
    // public function GetTeacherCourseTeacherName() : int {
    //     return isset($this->sqlData['teacher_id']) ? $this->sqlData["teacher_id"] : 0; 
    // }
    public function GetSubjectId() : int {
        return isset($this->sqlData['subject_id']) ? $this->sqlData["subject_id"] : 0; 
    }
    public function GetTeacherCourseId() : int {
        return isset($this->sqlData['teacher_course_id']) ? $this->sqlData["teacher_course_id"] : 0; 
    }
    public function GetCourseId() : int {
        return isset($this->sqlData['course_id']) ? $this->sqlData["course_id"] : 0; 
    }
    public function GetCourseSubjectId() : int {
        return isset($this->sqlData['subject_id']) ? $this->sqlData["subject_id"] : 0; 
    }
    public function GetCourseName()  {
        $courseId = $this->GetCourseId();

        $query = $this->con->prepare("SELECT course_name FROM course
            WHERE course_id=:courseId");

        $query->bindValue(":courseId", $courseId);
        $query->execute();

        return $query->fetchColumn();
    }

    public function GetCourseSubjectCode()  {

        $subjectId = $this->GetCourseSubjectId();

        $query = $this->con->prepare("SELECT subject_code FROM subject
            WHERE subject_id=:subjectId");

        $query->bindValue(":subjectId", $subjectId);
        $query->execute();

        return $query->fetchColumn();
    }

    public function GetThumbnail() {
        return isset($this->sqlData['thumbnail']) ? $this->sqlData["thumbnail"] : ""; 
    }

    public function GetSchoolYear() : string {

        $school_year_id = isset($this->sqlData['school_year_id']) ? $this->sqlData["school_year_id"] : 0;

        $query = $this->con->prepare("SELECT school_year_term FROM school_year
            WHERE school_year_id=:school_year_id");

        $query->bindValue(":school_year_id", $school_year_id);
        $query->execute();

        return $query->fetchColumn();
    }

}

?>