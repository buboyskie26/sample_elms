<?php

    class MyClassmateItem{

    private $con,$studentObj, $teacherLoggedInObj;

    public function __construct($con, $studentObj, $teacherLoggedInObj)
    {
        $this->con = $con;
        $this->studentObj = $studentObj;
        $this->teacherLoggedInObj = $teacherLoggedInObj;
    }

    public function create(){
       
        $teacherCourseId = 0;

        $profilePic = $this->studentObj->GetProfilePic();
        $studentFirstName = $this->studentObj->GetFirstName();
        $studentLastName = $this->studentObj->GetLastName();
        
        return "
           
                <div class='videoGridItem'>
                    <div class='thumbnail'>
                        <img src='$profilePic'>
                    </div>
                    <div class='details'>
                        <p>$studentFirstName</p>
                        <p>$studentLastName</p>
                    </div>
                </div>
        ";
    }
}


?>