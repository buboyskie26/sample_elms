<?php

    class Student{

    private $con, $userLoggedIn, $sqlData;

    public function __construct($con, $userLoggedIn)
    {
        $this->con = $con;
        $this->userLoggedIn = $userLoggedIn;

        $query = $this->con->prepare("SELECT * FROM student
            WHERE username=:username");

            $query->bindValue(":username", $userLoggedIn);
            $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        
        // If we are not relying on the username
        // then use the student_id to query
        // PROBLEM, we dont know the sqlData if it was 

        if($this->sqlData == null){

            $student_id = $userLoggedIn;

            $query = $this->con->prepare("SELECT * FROM student
            WHERE student_id=:student_id");

            $query->bindValue(":student_id", $student_id);
            $query->execute();
            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
        // else{
        //     $this->sqlData = null;
        // }

    }

    public function GetUsername() {
        return isset($this->sqlData['username']) ? $this->sqlData["username"] : ""; 
    }
    public function GetFirstName() {
        return isset($this->sqlData['firstname']) ? $this->sqlData["firstname"] : ""; 
    }

    public function GetLastName() {
        return isset($this->sqlData['lastname']) ? $this->sqlData["lastname"] : ""; 
    }
    public function GetName() {
        return isset($this->sqlData["firstname"]) ? $this->sqlData["firstname"] . " " . $this->sqlData["lastname"] : "";
    }
    public function GetProfilePic() {
        return isset($this->sqlData['profilePic']) ? $this->sqlData["profilePic"] : ""; 
    }
    public function GetId() : int {
        return isset($this->sqlData['student_id']) ? $this->sqlData["student_id"] : 0;
    
    }

    public function createForm(){

        $createCourseCategory = $this->createCourseCategory();
        return "
                <form action='student.php' method='POST'>
                    <div class='form-group'>
                    
                        $createCourseCategory
                        <input class='form-control' type='text' 
                            placeholder='ID Number' name='username'>
                        <input class='form-control' type='text' 
                            placeholder='First Name' name='firstname'>
                        <input class='form-control' type='text' 
                            placeholder='Last Name' name='lastname'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_student'>Save</button>
                </form>
            ";
    }
    public function insertStudent($course_id,$username,$firstname,$lastname){
            
        // Check if the subject already entered.

        $query = $this->con->prepare("INSERT INTO student(course_id, username,firstname,lastname)
            VALUES(:course_id, :username,:firstname,:lastname)");
        
        $query->bindValue(":course_id", $course_id);
        $query->bindValue(":username", $username);
        $query->bindValue(":firstname", $firstname);
        $query->bindValue(":lastname", $lastname);

        return $query->execute();
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
}

?>