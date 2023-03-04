<?php

    class DashboardTeacherItem{

    private $con, $sqlData, $dashboardTeacher, $teacherCourse;

    public function __construct($con, $input, $teacherCourse)
    {
        $this->con = $con;
        $this->teacherCourse = $teacherCourse;

        if(!is_array($input)) {
            //
            $query = $this->con->prepare("SELECT * FROM teacher_course 
                WHERE teacher_course_id = :teacher_course_id");

            $query->bindParam(":teacher_course_id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
        else {
            $this->sqlData = $input;
        }

    }
    private function getCourseId(){
        return $this->sqlData['course_id'];
    }
    private function GetThumbNail(){
        return $this->sqlData['thumbnail'];
    }

    private function GetTeacherCourseId(){
        return $this->sqlData['teacher_course_id'];
    }
    public function create(){

        // echo $latestSchoolYear;

        $thumbnail = $this->generateThumbnail();
        $details = $this->generateDetails();
        $teacherCourseId = $this->GetTeacherCourseId();
        

        $link = "my_student.php?teacher_course_id=$teacherCourseId";
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

        // $course_id = $this->teacherCourse->GetCourseId();
        $course_name = $this->teacherCourse->GetCourseName();

        // $courseName = "BSCS-501";
        $subjectCode = $this->teacherCourse->GetCourseSubjectCode();

        return "
            <div class='details'>
                <p>$course_name</p>
                <p>$subjectCode</p>
            </div>
        ";
    }

}   

?>