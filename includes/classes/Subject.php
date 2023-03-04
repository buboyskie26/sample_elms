<?php

    class Subject{

    private $con, $userLoggedIn;

    public function __construct($con, $userLoggedIn)
    {
        $this->con = $con;
        $this->userLoggedIn = $userLoggedIn;
    }

    public function createForm(){

        return "
                <form action='subject.php' method='POST'>
                    <div class='form-group'>
                        <input class='form-control' type='text' placeholder='Subject Code' name='subject_code'>
                    </div>

                    <div class='form-group'>
                        <input class='form-control' type='text' placeholder='Subject Title' name='subject_title'>
                    </div>

                    <div class='form-group'>
                        <input class='form-control' type='number' placeholder='Number Of Units' name='units' >
                    </div>

                    <div class='form-group'>
                        <select class='form-control' name='semester'>
                            <option value='1st'>1st</option>
                            <option value='2nd'>2nd</option>
                        </select>
                    </div>

                    <div class='form-group'>
                            <textarea class='form-control' placeholder='Description' name='subject_description' rows='3'></textarea>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_subject'>Save</button>
                </form>
            ";
    }
    public function insertSubject($subject_code, $subject_title,
        $units, $semester, $subject_description){
            
        // Check if the subject already entered.

        $query = $this->con->prepare("INSERT INTO subject(subject_code, subject_title,
            description, unit, semester)
            VALUES(:subject_code, :subject_title,
                :description, :unit, :semester)");
        
        $query->bindValue(":subject_code", $subject_code);
        $query->bindValue(":subject_title", $subject_title);
        $query->bindValue(":description", $subject_description);
        $query->bindValue(":unit", $units);
        $query->bindValue(":semester", $semester);

        return $query->execute();
    }
}

?>