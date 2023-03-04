<?php
    
    require_once('includes/config.php');


    if(isset($_POST['save_student']))
    {
        $course_name = $_POST['course_name'];

        if($course_name == null)
        {
            $res = [
                'status' => 422,
                'message' => 'All fields are mandatory'
            ];

            echo json_encode($res);
            return;
        }

        $query = $con->prepare("INSERT INTO course(course_name)
            VALUES(:course_name)");

        $query->bindValue(":course_name", $course_name);

        if($query->execute())
        {
            $res = [
                'status' => 200,
                'message' => 'Course Created Successfully'
            ];
            echo json_encode($res);
            return;
        }
        else
        {
            $res = [
                'status' => 500,
                'message' => 'Course Not Created'
            ];
            echo json_encode($res);
            return;
        }
        // echo $courseName;
    }

    // Vieweing for Update
    if(isset($_GET['course_id'])){

        $course_id = $_GET['course_id'];

        $query = $con->prepare("SELECT * FROM course 
            WHERE course_id=:course_id
            LIMIT 1");

        $query->bindValue(":course_id", $course_id);
        $query->execute();

        if($query->rowCount() > 0){
            
            $students = $query->fetch(PDO::FETCH_ASSOC);
            // array_push($studentArray, $row);
            
            $res = [
                'status' => 200,
                'message' => 'Course Fetch Successfully by id',
                'data' => $students
            ];

            echo json_encode($res);
            return;
        }
        else{
            $res = [
                'status' => 404,
                'message' => 'Course Id Not Found'
            ];

            echo json_encode($res);
            return;
        }
    }

    if(isset($_POST['update_student'])){

        $course_id = $_POST['course_id'];
        $course_name = $_POST['course_name'];

        if($course_name == NULL)
        {
            $res = [
                'status' => 422,
                'message' => 'All fields are mandatory'
            ];
            echo json_encode($res);
            return;
        }

        $query = $con->prepare("UPDATE course
            SET course_name=:course_name
            WHERE course_id=:course_id");

        $query->bindValue(":course_name", $course_name);
        $query->bindValue(":course_id", $course_id);

        if($query->execute()){

            $res = [
                'status' => 200,
                'message' => 'Course Updated Successfully',
            ];

            echo json_encode($res);
            return;
        }
        else{
            $res = [
                'status' => 500,
                'message' => 'Course Not Updated',
            ];

            echo json_encode($res);
            return;
        }
    }

    if(isset($_POST['delete_student'])){
        $course_id = $_POST['course_id'];

        $query = $con->prepare("DELETE FROM course
            WHERE course_id=:course_id");
        
        $query->bindValue(":course_id", $course_id);

        if($query->execute()){

            $res = [
                'status' => 200,
                'message' => 'Successfully deleted'
            ];

            echo json_encode($res);
            return;
        }
        else{
            $res = [
                'status' => 500,
                'message' => 'Course Not Deleted',
            ];
            echo json_encode($res);
            return;
        }
         
    }
?>