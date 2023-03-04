<?php

class AdminUser{

    private $con, $sqlData;

    public function __construct($con, $input)
    {
        $this->con = $con;
        $this->sqlData = $input;

        // echo "hey";
        // print_r($input);
        if(!is_array($input)){
            $query = $this->con->prepare("SELECT * FROM users
            WHERE username=:username");

            $query->bindValue(":username", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
    }
    public function GetId() {
        return isset($this->sqlData['user_id']) ? $this->sqlData["user_id"] : 0; 
    }
    public function GetUsername() {
        return isset($this->sqlData['username']) ? $this->sqlData["username"] : ""; 
    }
    public function GetFirstName() {
        return isset($this->sqlData['firstName']) ? $this->sqlData["firstName"] : ""; 
    }
    public function GetName() {
        return $this->sqlData["firstName"] . " " . $this->sqlData["lastName"];
    }
    public function GetLastName() {
        return isset($this->sqlData['lastName']) ? $this->sqlData["lastName"] : ""; 
    }

    public static function IsAuthenticated(){
        return isset($_SESSION['adminLoggedIn']);
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
    public function insertStudent($course_id, $username, $firstname, $lastname){
            
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