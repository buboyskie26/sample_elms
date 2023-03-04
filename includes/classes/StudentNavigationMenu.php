<?php 

    class StudentNavigationMenu{
        
        private $con, $userLoggedInObj;

        public function __construct($con, $userLoggedInObj)
        {
            $this->con = $con;
            $this->userLoggedInObj = $userLoggedInObj;
        }

        public function create(){

            $result = $this->createNavigation("dashboard_student.php",
                "assets/images/icons/home.png", "My Class");

            // $result .= $this->createNavigation("course.php",
            //     "assets/images/icons/home.png", "Class");

            //  $result .= $this->createNavigation("department.php",
            //     "assets/images/icons/home.png", "Department");

            // $result .= $this->createNavigation("student.php",
            //     "assets/images/icons/home.png", "Student");

            // $result .= $this->createNavigation("teacher.php",
            //     "assets/images/icons/home.png", "Teacher");

            if(User::IsStudentAuthenticated()){
                    $result .= $this->createNavigation("logout.php", 
                "assets/images/icons/logout.png", "Logout");
            }
            return "
                <div class='navigationItems'>
                    $result
                </div>
            ";
        }
        
        public function createNavigation($link, $profile, $text){
            return "
                <div class='navigationItem'>
                    <a href='$link'>
                        <img src='$profile'>
                        <span>$text</span>
                    </a>
                </div>
            ";
        }
    }

?>