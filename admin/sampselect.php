<?php 

    require_once("../includes/config.php");

?>


<!DOCTYPE html>
<html>
<head>
  <title>Dynamic Select Option Example</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <h2>Select a Teacher and Course</h2>
  <form>
    <label for="teacher">Teacher:</label>

    <!-- <select name="teacher" id="teacher">
      <option value="">Select a Teacher</option>
      <option value="mr_smith">Mr. Smith</option>
      <option value="ms_jones">Ms. Jones</option>
    </select> -->

    <div class="form-group mb-4">
        <select class="form-control" name="teacher_id" id="teacher_id_samp">
            <?php
                $query = $con->prepare("SELECT * FROM teacher");
                $query->execute();

                echo "<option value='' selected>Select Teacher</option>";


                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['teacher_id'] . "'>" . $row['firstname'] . " " . $row['lastname'] . "</option>";
                }
            ?>
        </select>
    </div>

    <br>

    <label for="teacher_course">Teacher Course:</label>
    
    <select name="teacher_course" id="teacher_course">
      <option value="">Select a Course</option>
    </select>
  </form>

  <script>
    // When the teacher select changes
    $('#teacher_id_samp').on('change', function() {

      var teacher = $(this).val();

      if (!teacher) {
        $('#teacher_course').html('<option value="">Select a Course</option>');
        return;
      }

      $.ajax({
        url: '../ajax/schedule/get_courses.php',
        type: 'POST',
        data: {teacher: teacher},
        dataType: 'json',
        success: function(response) {

            // var res = jQuery.parseJSON(response);
            
          console.log(response)

          // Populate the course select with the retrieved courses

          var options = '<option value="">Select a Course</option>';

          $.each(response, function(index, value) {
            options += '<option value="' + value.teacher_course_id + '">' + value.teacher_course_id + '</option>';

            console.log(value.teacher_course_id)
          });
          $('#teacher_course').html(options);

        }

      });
    });
  </script>
</body>
</html>
