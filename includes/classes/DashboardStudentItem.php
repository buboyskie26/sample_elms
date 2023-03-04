<?php

    class DashboardStudentItem{

    private $con, $sqlData, $teacherCourse;

    public function __construct($con, $input, $teacherCourse)
    {
        $this->con = $con;
        $this->teacherCourse = $teacherCourse;

        // print_r($teacherCourse);

        if(!is_array($input)) {
            //
            $query = $this->con->prepare("SELECT * FROM teacher_course_student
                WHERE teacher_course_student_id = :teacher_course_student_id");

            $query->bindParam(":teacher_course_student_id", $input);
            $query->execute();
            
            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $this->sqlData = $input;
        }
    }
    private function GetTeacherId(){
        return $this->sqlData['teacher_id'];
    }
    private function GetTeacherCourseId(){
        return $this->sqlData['teacher_course_id'];
    }

    private function GetStudentSubjectCode(){
        // $subjectId = $this->sqlData['subject_id'];

        // $query = $this->con->prepare("SELECT subject_code FROM subject
        //         WHERE subject_id = :subject_id");

        // $query->bindParam(":subject_id", $subjectId);
        // $query->execute();
 
        // return $query->fetchColumn();
    }
    private function GetStudentCourseName(){

        // Join teacher_course_student_id teacher_course_id
        // Join teacher_course teacher_course_id
        // Join teacher_course course_id
        // Join course course_id
        // Then get Course Name

        // $courseId = $this->sqlData['subject_id'];

        // $query = $this->con->prepare("SELECT subject_code FROM subject
        //         WHERE subject_id = :subject_id");

        // $query->bindParam(":subject_id", $courseId);
        // $query->execute();
 
        // return $query->fetchColumn();
    }
    private function GetThumbNail(){
        
        $teacherCourseId = $this->GetTeacherId();

        $query = $this->con->prepare("SELECT thumbnail FROM teacher_course
                WHERE teacher_id = :teacher_id");

        $query->bindParam(":teacher_id", $teacherCourseId);
        $query->execute();

        return $query->fetchColumn();
    }

    private function GetTeacherFirstName(){
        
        $teacherId = $this->GetTeacherId();

        $query = $this->con->prepare("SELECT firstname FROM teacher
                WHERE teacher_id = :teacher_id");

        $query->bindParam(":teacher_id", $teacherId);
        $query->execute();
 
        return $query->fetchColumn();
    }
    private function GetTeacherLastName(){
        
        $teacherId = $this->GetTeacherId();

        $query = $this->con->prepare("SELECT lastname FROM teacher
                WHERE teacher_id = :teacher_id");

        $query->bindParam(":teacher_id", $teacherId);
        $query->execute();
 
        return $query->fetchColumn();
    }
    private function GetTeacherFullName(){
        $teacherFName = $this->GetTeacherFirstName();
        $teacherLName = $this->GetTeacherLastName();

        return $teacherFName." ". $teacherLName;
    }
    public function create(){

        $thumbnail = $this->generateThumbnail();

        $teacherCourseId = $this->GetTeacherCourseId();

        $details = $this->generateDetails();

        $link = "my_classmates.php?teacher_course_id=$teacherCourseId";

        return "
            <a href='$link'>
                <div class='videoGridItem'>
                    $thumbnail
                    $details
                </div>
            </a>
        ";
    }
    private function generateThumbnail(){

        $getThumbNail = $this->GetThumbNail();

        return "
            <div class='thumbnail'>
                <img src='$getThumbNail' >
            </div>
        ";
    }

    private function generateDetails(){

        $courseName = $this->teacherCourse->GetCourseName();
        $subjectCode = $this->teacherCourse->GetCourseSubjectCode();
        
        $teacherName = $this->GetTeacherFullName();

        // echo $this->teacherCourse->GetCourseId();

        return "
            <div class='details'>
                <p>$courseName</p>
                <p>$subjectCode</p>
                <p>$teacherName</p>
            </div>
        ";
    }

}   

?>