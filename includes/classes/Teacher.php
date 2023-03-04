<?php

    class Teacher{
    
    private $con, $userLoggedIn, $sqlData;

    public function __construct($con, $userLoggedIn)
    {
        $this->con = $con;
        $this->userLoggedIn = $userLoggedIn;
        
        $query = $this->con->prepare("SELECT * FROM teacher
            WHERE username=:username");
            $query->bindValue(":username", $userLoggedIn);
            $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);

        // if(!is_numeric($userLoggedIn)){
        //     $query = $this->con->prepare("SELECT * FROM teacher
        //         WHERE username=:username");

        //     $query->bindValue(":username", $userLoggedIn);
        //     $query->execute();
        // }

        // else if(is_numeric($userLoggedIn)){
            
        //     $teacher_id = (int)$userLoggedIn;
        //     $query = $this->con->prepare("SELECT * FROM teacher
        //         WHERE teacher_id=:teacher_id");

        //     $query->bindValue(":teacher_id", $teacher_id);
        //     $query->execute();
        // }
        

        // $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public function QueryWithTeacherId($teacher_id){
        
        $query = $this->con->prepare("SELECT * FROM teacher
            WHERE teacher_id=:teacher_id");

        $query->bindValue(":teacher_id", $teacher_id);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public function GetUsername() {
        return isset($this->sqlData['username']) ? $this->sqlData["username"] : ""; 
    }

    public function GetName() {
        return isset($this->sqlData["firstname"]) ? $this->sqlData["firstname"]. " " . $this->sqlData["lastname"] : "";
    }

    public function GetId() : int {
        return isset($this->sqlData['teacher_id']) ? $this->sqlData["teacher_id"] : 0;
    }
     public function GetProfilePic() : string {
        return isset($this->sqlData['profilePic']) ? $this->sqlData["profilePic"] : "";
    }
    public function createForm(){

        $createDepartmentCategory = $this->createDepartmentCategory();
        return "
                <form action='teacher.php' method='POST'>
                    <div class='form-group'>
                    
                        $createDepartmentCategory
                        <input class='form-control' type='text' 
                            placeholder='ID Number' name='username'>
                        <input class='form-control' type='text' 
                            placeholder='First Name' name='firstname'>
                        <input class='form-control' type='text' 
                            placeholder='Last Name' name='lastname'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_teacher'>Save</button>
                </form>
            ";
    }
    public function insertTeacher($department_id,$username,$firstname,$lastname){
            
        // Check if the subject already entered.

        $query = $this->con->prepare("INSERT INTO teacher(department_id, username,firstname,lastname)
            VALUES(:department_id, :username,:firstname,:lastname)");
        
        $query->bindValue(":department_id", $department_id);
        $query->bindValue(":username", $username);
        $query->bindValue(":firstname", $firstname);
        $query->bindValue(":lastname", $lastname);

        return $query->execute();
    }
    private function createDepartmentCategory(){

        $query = $this->con->prepare("SELECT * FROM department");
        $query->execute();

            $html = "<div class='form-group'>
                    <select class='form-control' name='department_id'>";

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $html .= "
                    <option value='".$row['department_id']."'>".$row['department_name']."</option>
                ";
            }
            $html .= "</select>
                    </div>";
            return $html;
    }

    public function createTable(){

        $table = "
        <div style='text-align: end; margin-right: 46px;'>
            <a href='teacher_create.php'>
                <button class='btn btn-sm btn-primary'>Add Teacher</button>
            </a>
        </div>
            <table class='table table-hover'>
                <thead >
                    <tr class='text-center'>
                        <th>Name</th>
                        <th>Department</th>
                        <th></th>
                    </tr>
                </thead>
        ";

        $query = $this->con->prepare("SELECT * FROM teacher
            WHERE teacher_status=:teacher_status");

        $query->bindValue(":teacher_status", "active");
        $query->execute();

        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $table .= $this->GenerateTableBody($row);
            }
        }
        $table .= "
            </table>
        ";
        return $table;
    }
    private function GenerateTableBody($row){
        $fullName = $row['firstname'] . " ". $row['lastname'];
        $department_id = $row['department_id'];
        $teacher_id = $row['teacher_id'];

        $department = new Department($this->con, $department_id);

        $departmentName = $department->GetDepartmentName();
        return "
            <tbody>
                <tr class='text-center'>
                    <td>$fullName</td>
                    <td>$departmentName</td>
                    <td>
                        <a href='add_teacher_course.php?teacher_id=$teacher_id'>
                            <button class='btn btn-success'>
                                Add TC
                            </button>
                        </a>
                    </td>
                </tr>
            </tbody>
        ";
    }

    public function createTeacherCourse($teacher_id){

        $createCourseCategory = $this->createCourseCategory();
        $createSubjectCategory = $this->createSubjectCategory();
        $selectSchoolYear = $this->selectSchoolYear();

        return "
            <form action='add_teacher_course.php?teacher_id=' method='POST' enctype='multipart/form-data'>
                    <div class='form-group'>
                        $createCourseCategory
                        $createSubjectCategory
                        $selectSchoolYear

                        <input type='file' name='thumbnail' class='form-control mb-3'>
                        <input type='hidden' name='teacher_id' value='$teacher_id'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_teacher_course_admin'>Save</button>
            </form>
        ";
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

}

?>