<?php

    class Course{

    private $con, $userLoggedIn;

    public function __construct($con, $userLoggedIn)
    {
        $this->con = $con;
        $this->userLoggedIn = $userLoggedIn;

        
    }



    public function TeacherCourse(){
        return $this->userLoggedIn->GetName();
    }
    public function createForm(){

        return "
                <form action='course.php' method='POST'>
                    <div class='form-group'>
                        <input class='form-control' type='text' placeholder='Course Name' name='course_name'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_course'>Save</button>
                </form>
            ";
    }
    public function insertCourse($course_name){
            
        // Check if the subject already entered.

        $query = $this->con->prepare("INSERT INTO course(course_name)
            VALUES(:subject_code)");
        
        $query->bindValue(":subject_code", $course_name);

        return $query->execute();
    }


}

?>