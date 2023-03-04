<?php

    require_once('includes/teacherHeader.php');

    $subject_period_id_session = $_SESSION['subject_period_id_session'];
    $teacher_id_session = $_SESSION['teacher_id_session'];


?>

<!-- Add Subject Period Quiz -->
<div class="modal fade" id="addSubjectPeriodQuizModal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method='post'id="addPeriodQuizForm">
                <div class="modal-body">

                    <div id="errorMessage" class="alert alert-warning d-none"></div>

                    <div class="mb-3">
                        <label for="">Quiz Title</label>
                        <input type="text" name="quiz_title" id="quiz_title" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="">Quiz Description</label>
                        <textarea type="text" name="quiz_description" id="quiz_description" class="form-control summernote"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="">Set Due</label>
                        <input type="text" name="due_date" id="online_exam_datetime" class="form-control" readonly />
                    </div>     

                    <!-- <div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Set Due <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<input type="text" name="due_date" id="online_exam_datetime" class="form-control" readonly />
	                		</div>
            			</div>
          			</div> -->
                    
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

