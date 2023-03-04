<?php

    class MyStudent{

    private $con, $teacherLoggedIn, $teacherCourse;

    public function __construct($con, $teacherLoggedIn, $teacherCourse)
    {
        $this->con = $con;
        $this->teacherLoggedIn = $teacherLoggedIn;
        $this->teacherCourse = $teacherCourse;
    }

    public function create(){

        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $generateMyStudent = $this->GenerateMyStudent();
        
        $courseName = $this->teacherCourse->GetCourseName();
        $subjectCode = $this->teacherCourse->GetCourseSubjectCode();
 
        $assignment_link = "assignment.php?teacher_course_id=$teacher_course_id";
        
        return "
            <div class='myStudentHeader'>
                <div class='left'>
                    $courseName / $subjectCode / My Students 
                </div>
                <div class='right'>
                    <a href='add_student.php?teacher_course_id=$teacher_course_id'>
                        <button style='font-weight:bold;' class='btn btn-primary btn-sm'>Add Student</button>
                    </a>
                    <a href='$assignment_link'>
                        <button style='font-weight:bold;' 
                            class='btn btn-success btn-sm'>Assignment</button>
                    </a>
                    <a href='gradebook.php?teacher_course_id=$teacher_course_id'>
                        <button style='font-weight:bold;' 
                            class='btn btn-secondary btn-sm'>GradeBook</button>
                    </a>
                </div>
            </div>

            <div class='myStudentSection'>
                $generateMyStudent
            </div>
        ";
    }

    private function fetchMyStudentv2(){

        $teacher_id = $this->teacherLoggedIn->GetId();

        $arr = array();
        $arr1 = array();

        $teacherCourseStudentQuery = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE teacher_id=:teacher_id");

        $teacherCourseStudentQuery->bindValue(":teacher_id", $teacher_id);
        $teacherCourseStudentQuery->execute();

        while($row = $teacherCourseStudentQuery->fetch(PDO::FETCH_ASSOC)){
            array_push($arr, $row['student_id']);
        }
         
        if(sizeof($arr) > 0){

            $condition = "";
            $i = 0;

            while($i < sizeof($arr)){
                if($i == 0){
                    $condition .= "WHERE student_id=?";
                }else{
                    $condition .= " OR student_id=?";
                }
                $i++;
            }
            // SELECT * FROM student WHERE student_id = ? OR student_id = ? OR student_id = ? 

            $studentQuery = $this->con->prepare("SELECT firstname FROM student
                $condition");

            $j = 1;

            foreach ($arr as $sub) {
                
                $count = $sub;

                $studentQuery->bindValue($j, $count);
                $j++;
            }

            $output = "";
            $studentQuery->execute();

            while($studRow = $studentQuery->fetch(PDO::FETCH_ASSOC)){
                $output = $studRow['firstname'];

            }
            return $output;
            // return $studentQuery->fetchColumn();
            // return $studentQuery;
        }
    }
    private function GenerateMyStudent(){

        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $teacher_id = $this->teacherLoggedIn->GetId();

        $query = $this->con->prepare("SELECT * FROM teacher_course_student
            WHERE (teacher_course_id=:teacher_course_id AND teacher_id=:teacher_id) AND deleted =''
            ORDER BY student_id DESC");
        
        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->execute();

        $outputHtml = "";
     
        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $student_id = $row['student_id'];
            $que = $this->GeneratestudentFirstName($student_id);

            $buttonDelete = "deleteMyStudent(this, $student_id)";
            $btnId = "student_id_$student_id";
            $fname = $que[0];
            $lname = $que[1];
            $profilePic = $que[2];

            $outputHtml .= "
                <div class='myStudentSectionItems'>
                    <div class='thumbnail'>
                        <img src='$profilePic' >
                    </div>
                    <div class='details'>
                        <p>$fname</p>
                        <p>$lname</p>
                    </div>
                    <button onclick='$buttonDelete' id='$btnId'
                        class='mt-3 btn btn-danger btn-sm'>Remove</button>
                </div>
            ";
        }
        return $outputHtml;
    }
    private function GeneratestudentFirstName($student_id){
        $array = array();

        $query = $this->con->prepare("SELECT firstname,lastname,profilePic FROM student
            WHERE student_id=:student_id");

        $query->bindValue(":student_id", $student_id);
        $query->execute();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            array_push($array, $row['firstname']);
            array_push($array, $row['lastname']);
            array_push($array, $row['profilePic']);
        }
        return $array;

    }
    public function AddStudent($teacher_course_idd, $student_id, $teacher_id){

        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();

        $query = $this->con->prepare("INSERT INTO teacher_course_student(teacher_course_id, student_id, teacher_id)
            VALUES(:teacher_course_id, :student_id, :teacher_id)");
        
        $query->bindValue(":teacher_course_id", $teacher_course_id);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":teacher_id", $teacher_id);

        return $query->execute();
    }

    public function createForm(){
        
        $createStudentCategory = $this->createStudentCategory();
        $teacher_course_id = $this->teacherCourse->GetTeacherCourseId();
        $link = "add_student.php?teacher_course_id=$teacher_course_id";
        return "
            <form action='$link' method='POST'>
                    <div class='form-group'>
                        $createStudentCategory
                        
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_teacher_course_student'>Save</button>
                </form>
        ";
    }
    private function createStudentCategory(){

        $query = $this->con->prepare("SELECT * FROM student");
        $query->execute();

            $html = "<div class='form-group'>
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
   

}

?>