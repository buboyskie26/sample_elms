<?php

class User{

    private $con, $sqlData;

    public function __construct($con, $userLoggedIn)
    {
        $this->con = $con;

        $query = $this->con->prepare("SELECT * FROM users
            WHERE username=:username");

        $query->bindValue(":username", $userLoggedIn);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function IsTeacherAuthenticated(){
        return isset($_SESSION['teacherUserLoggedIn']);
    }
    public static function IsStudentAuthenticated(){
        return isset($_SESSION['studentUserLoggedIn']);
    }
    public static function IsAdminAuthenticated(){
        return isset($_SESSION['adminLoggedIn']);
    }
}

?>