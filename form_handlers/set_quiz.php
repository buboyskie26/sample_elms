


<?php 
    require_once('includes/teacherHeader.php');

    $subject_period_id_session = $_SESSION['subject_period_id_session'];
    $teacher_id_session = $_SESSION['teacher_id_session'];

?>

<div class="modal" id="formModal">
  	<div class="modal-dialog modal-lg">
    	<form method="post" id="exam_form">
      		<div class="modal-content">
      			<!-- Modal Header -->
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title"></h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>

        		<!-- Modal body -->
        		<div class="modal-body">
          			<!-- <div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Exam Title <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<input type="text" name="online_exam_title" id="online_exam_title" class="form-control" />
	                		</div>
            			</div>
          			</div> -->
          			
                    <div class="form-group">
                        <div class="row">
              				<label class="col-md-4 text-right">Quiz Title<span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" name="quiz_title" id="quiz_title" class="form-control" />
	                		</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
              				<label class="col-md-4 text-right">Quiz Description<span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <textarea class="form-control mb-2 summernote" name="quiz_description"
                                    id="quiz_description"></textarea>
	                		</div>
                        </div>
                    </div>

                    <div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Exam Date & Time <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<input type="text" name="due_date" id="online_exam_datetime" class="form-control" readonly />
	                		</div>
            			</div>
          			</div>


                    
          			<!-- <div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Exam Duration <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<select name="online_exam_duration" id="online_exam_duration" class="form-control">
	                				<option value="">Select</option>
	                				<option value="5">5 Minute</option>
	                				<option value="30">30 Minute</option>
	                				<option value="60">1 Hour</option>
	                				<option value="120">2 Hour</option>
	                				<option value="180">3 Hour</option>
	                			</select>
	                		</div>
            			</div>
          			</div> -->
          			
        		</div>

	        	<!-- Modal footer -->
	        	<div class="modal-footer">
	        		<!-- <input type="hidden" name="online_exam_id" id="online_exam_id" />
	        		<input type="hidden" name="page" value="    " />
	        		<input type="hidden" name="action" id="action" value="Add" /> -->

	        		<input type="hidden" name="subject_period_id" id="subject_period_id" value="<?php echo $subject_period_id_session?>" />
	        		<input type="hidden" name="teacher_id" id="teacher_id" value="<?php echo $teacher_id_session?>" />

	        		<input type="submit" name="button_action" id="button_action" class="btn btn-success btn-sm" value="Add" />
	          		<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
	        	</div>
        	</div>
    	</form>
  	</div>
</div>