<?php

    // require_once('includes/teacherHeader.php');

    // $subject_period_id_session = $_SESSION['subject_period_id_session'];
    // $teacher_id_session = $_SESSION['teacher_id_session'];
    require_once("../includes/config.php");
    // require_once("../../includes/config.php");

    $subject_period_id_session = 1;
    $teacher_id_session = 1;
    
?>

<!-- Add Subject Period Quiz -->
<div class="modal fade" id="createScheduleModal" >

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method='post'id="addScheduleForm">
                <div class="modal-body">

                    <div id="errorMessageForSchedule" class="alert alert-warning d-none"></div>


                    <div class="form-group mb-4">
                        <select class="form-control" name="teacher_id" id="teacher_select">
                            <?php
                                $query = $con->prepare("SELECT * FROM teacher");
                                $query->execute();

                                echo "<option value='' disabled selected>Select Teacher</option>";


                                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $row['teacher_id'] . "'>" . $row['firstname'] . " " . $row['lastname'] . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="teacher_course">Teacher Course:</label>
    
                        <select class="form-control" name="teacher_course" id="teacher_course">
                            <option value="">Select a Course</option>
                        </select>

                    </div>

                    <div class="mb-3">
                        <label for="">Room number</label>
                        <input value='55' type="text" placeholder="(Room: 501)" name="room_number" id="room_number" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="">Day</label>
                        <input  value='Monday' type="text" placeholder="(Monday)" name="day" id="day" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="">Start Hour</label>
                        <input type="text" placeholder="(7:00)" name="start_hour" id="start_hour" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="">End Hour</label>
                        <input type="text" placeholder="(7:00)" name="end_hour" id="end_hour" class="form-control" />
                    </div>


                    <!-- <div class="mb-4">
                        <label for="">Start</label>
                        <input type="text" name="start_date" id="start_date" class="form-control" readonly />
                    </div>   
                    
                    <div class="mb-4">
                        <label for="">End</label>
                        <input type="text" name="end_date" id="end_date" class="form-control" readonly />
                    </div>    -->

                </div>
                <div class="modal-footer">

                    <input type="hidden" name="subject_period_id" 
                        id="subject_period_id" value="<?php echo $subject_period_id_session?>" />
	        		<input type="hidden" name="teacher_id" 
                        id="teacher_id" value="<?php echo $teacher_id_session?>" />

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // When the teacher select changes
    $('#teacher_select').on('change', function() {

      var teacher = parseInt($(this).val());

    //   console.log(teacher)

      if (!teacher) {
        $('#teacher_course').html('<option value="">Select a Course</option>');
        return;
      }

      $.ajax({
        url: '../ajax/schedule/get_teacher_course.php',
        type: 'POST',
        data: {teacher: teacher},
        dataType: 'json',
        success: function(response) {

          var options = '<option value="">Select a Course</option>';

          $.each(response, function(index, value) {
            // options += '<option value="' + value.teacher_course_id + '">' + value.teacher_course_id + '</option>';
            options += '<option value="' + value + '">' + value + '</option>';

            // console.log(value.teacher_course_id)
          });
          $('#teacher_course').html(options);

        }

      });
    });
  </script>