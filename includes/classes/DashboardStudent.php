<?php

    class DashboardStudent{

    private $con, $studentLoggedInObj;

    public function __construct($con, $studentLoggedInObj)
    {
        $this->con = $con;
        $this->studentLoggedInObj = $studentLoggedInObj;
    }

    public function create(){

        $generateGridItems = $this->GenerateGridItems();

        if($generateGridItems != ""){
            return "
                <div class='videoGridHeader'>
                    <div class='left'>
                        My Class / School Year: 2013-2014
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
    private function GenerateGridItems(){

        $student_id = $this->studentLoggedInObj->GetId();
        
        $query = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE student_id=:student_id
            ORDER BY teacher_course_student_id DESC");

        $query->bindValue(":student_id", $student_id);
        $query->execute();

        $outputHtml = "";

        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
            
            // $teacher = new Teacher($this->con, $row['teacher_id']);
            $teacherCourse = new TeacherCourse($this->con,
                $row['teacher_course_id'], $row['teacher_id']);
                
            $item = new DashboardStudentItem($this->con, $row, $teacherCourse);
            
            $outputHtml .= $item->create();
        }
            return $outputHtml;
        }else{
            return "";
        }
        
    }

    public function insertTeacherCourse($course_id, $teacher_id, $subject_id,
            $school_year){

        $query = $this->con->prepare("INSERT INTO teacher_course(course_id,teacher_id, subject_id, school_year)
            VALUES(:course_id, :teacher_id, :subject_id, :school_year)");
        
        $query->bindValue(":course_id", $course_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->bindValue(":subject_id", $subject_id);
        // $query->bindValue(":thumbnail", $thumbnail);
        $query->bindValue(":school_year", $school_year);

        return $query->execute();
    }
    public function createForm(){
        
        $createCourseCategory = $this->createCourseCategory();
        $createSubjectCategory = $this->createSubjectCategory();

        return "
            <form action='dashboard_teacher.php' method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        $createCourseCategory
                        $createSubjectCategory
                        <input class='form-control' type='text' 
                            placeholder='School Year' name='school_year'>

                         
                        
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_teacher_course'>Save</button>
                </form>
        ";
    }
    private function createCourseCategory(){

        $query = $this->con->prepare("SELECT * FROM course");
        $query->execute();

            $html = "<div class='form-group'>
                    <select class='form-control' name='course_id'>";

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

            $html = "<div class='form-group'>
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

}   

?>