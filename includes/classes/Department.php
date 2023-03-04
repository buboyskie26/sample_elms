<?php

    class Department{

    private $con, $userLoggedIn;

    public function __construct($con, $userLoggedIn)
    {
        $this->con = $con;
        $this->userLoggedIn = $userLoggedIn;
    }

    public function createForm(){

        return "
                <form action='department.php' method='POST'>
                    <div class='form-group'>
                        <input class='form-control' type='text' 
                            placeholder='Department Name' name='department_name'>
                        
                        <input class='form-control' type='text' 
                            placeholder='Person Incharge Name' name='dean'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_department'>Save</button>
                </form>
            ";
    }
    public function insertDepartment($department_name, $dean){
            
        // Check if the subject already entered.

        $query = $this->con->prepare("INSERT INTO department(department_name, dean)
            VALUES(:department_name, :dean)");
        
        $query->bindValue(":department_name", $department_name);
        $query->bindValue(":dean", $dean);

        return $query->execute();
    }
}

?>