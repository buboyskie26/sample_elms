<?php 
    require_once('includes/teacherHeader.php');

    $subject_period_id_session = $_SESSION['subject_period_id_session'];
    $teacher_id_session = $_SESSION['teacher_id_session'];
?>


<!-- Edit Student -->
<div class="modal fade" id="subjectPeriodQuizEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editPeriodQuizForm">
                <div class="modal-body">

                    <div id="errorMessage" class="alert alert-warning d-none"></div>

                    <div class="mb-3">
                        <label for="">Quiz Title</label>
                        <input type="text" name="quiz_title_edit" id="quiz_title_edit" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="">Quiz Description</label>
                        <textarea type="text" name="quiz_description_edit" id="quiz_description_edit" class="form-control summernote"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="">Set Due</label>
                        <input type="text" name="online_exam_datetime_edit" id="online_exam_datetime_edit" class="form-control"/>
                    </div>     
                    
                </div>
                <div class="modal-footer">

                    <input type="hidden" name="subject_period_id" 
                        id="subject_period_id" value="<?php echo $subject_period_id_session?>" />
	        		<input type="hidden" name="teacher_id" 
                        id="teacher_id" value="<?php echo $teacher_id_session?>" />
                    
                    <input type="hidden" name="subject_period_quiz_id" 
                        id="subject_period_quiz_id" />

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Edit Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

