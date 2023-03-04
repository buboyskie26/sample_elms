<?php

    class MyClassmate{

    private $con, $teacherCourse, $studentLoggedIn;

    public function __construct($con, $teacherCourse, $studentLoggedIn)
    {
        $this->con = $con;
        $this->teacherCourse = $teacherCourse;
        $this->studentLoggedIn = $studentLoggedIn;
    }

    public function create(){
        
        $myClassmateObj = $this->GenerateMyClassmateItems();
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();

        $assignment_link = "student_assignment.php?teacher_course_id=$teacher_course_id";

        if($myClassmateObj != ""){
            return "
                <div class='videoGridHeader'>
                    <div class='left'>
                        My Class / AppDev101 / School Year: 2022-2023
                    </div>
                    <div class='right'>
                        <a href='$assignment_link'>
                            <button style='font-weight:bold;' 
                            class='btn btn-primary btn-sm'>My Assignment</button>
                        </a>

                    </div>
                </div>

                <div class='videoGrid'>
                    $myClassmateObj
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
    private function GenerateMyClassmateItems(){

        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();


        $query = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE teacher_course_id=:teacher_course_id");
        
        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->execute();

        $output = "";

        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $student = new Student($this->con, $row['student_id']);

            $items = new MyClassmateItem($this->con, $student, $this->studentLoggedIn);
            
            $output .= $items->create();
        }

        return $output;
    }
    

}


?>