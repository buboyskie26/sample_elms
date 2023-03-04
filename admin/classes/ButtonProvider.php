<?php 

    class ButtonProvider{

        public static function createAdminProfileNavigationButton($con, $adminLoggedInObj){

            $user = new User($con, $adminLoggedInObj);
            
            $link = "admin/profile.php";

            if(User::IsAdminAuthenticated()){
                    return "
                    <a href='$link'>
                       LOGGED IN
                    </a>";
            }else{
                return "
                    <a href='signIn.php'>
                        <span class='signInLink'>SIGN IN</span>
                    </a>
                ";
            }
        }

        public static function teacherProfileNav($con, $teacherLoggedIn){

            $user = new Teacher($con, $teacherLoggedIn);
            
            $link = "teacher_profile.php";

            $teacherName = $user->GetName();

            if(User::IsTeacherAuthenticated()){
                    return "
                    <a style='color: #212529;font-weight: 500;' href='$link'>
                        $teacherName
                    </a>";
            }else{
                return "
                    <a href='teacherLogin.php'>
                        <span class='signInLink'>SIGN IN</span>
                    </a>
                ";
            }
        }

        public static function studentProfileNav($con, $studentLoggedIn){

            $user = new Student($con, $studentLoggedIn);
            
            $link = "studentr_profile.php";

            $studentName = $user->GetName();

            if(User::IsStudentAuthenticated()){
                    return "
                    <a style='color: #212529;font-weight: 500;' href='$link'>
                        $studentName
                    </a>";
            }else{
                return "
                    <a href='studentLogin.php'>
                        <span class='signInLink'>SIGN IN</span>
                    </a>
                ";
            }
        }
    }
    ?>