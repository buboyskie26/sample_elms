<?php

    
    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodQuiz.php'); 
    require_once('includes/classes/SubjectPeriod.php'); 

    if(isset($_GET['teacher_id']) && isset($_GET['subject_period_id'])){

        $_SESSION['subject_period_id_session'] = $_GET['subject_period_id'];
        $_SESSION['teacher_id_session'] = $_GET['teacher_id'];

        $subject_period_id = $_GET['subject_period_id'];

        $teacher_id = $_GET['teacher_id'];

        $subjectPeriod = new SubjectPeriod($con, $subject_period_id,
            $teacherUserLoggedInObj, "teacher");

        $subjectPeriodQuizId = $subjectPeriod->GetSubjectPeriodQuizId($teacher_id);


        $periodQuiz = new SubjectPeriodQuiz($con, $subjectPeriodQuizId,  $teacherUserLoggedInObj);

        $createQuiz = $periodQuiz->create($subject_period_id, $teacher_id);
        
        echo "
            <div class='subject_period_quiz_form'>
                $createQuiz
            </div>

        ";
        
        if(isset($_POST['create_subject_period_quiz'])){
            $wasSuccess = $periodQuiz->insertPeriodQuiz(
                $_POST['quiz_title'],
                $_POST['quiz_description'],
                $_POST['due_date'],
                $_POST['subject_period_id'],
                $_POST['teacher_id']
            );
            if($wasSuccess){
                // header("Location: ");
                echo "nice";
            }
        }
        // require_once('form_handlers/set_quiz.php'); 
        require_once('form_handlers/add_set_quiz.php'); 
        require_once('form_handlers/edit_set_quiz.php'); 
    }
?>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<script>
    $(document).ready(function() {
        
        $("#add_button").click(function(){

            $("#addSubjectPeriodQuizModal").modal('show');
        });

        var date = new Date();
	    date.setDate(date.getDate());

        $('#online_exam_datetime').datetimepicker({
            startDate :date,
            format: 'yyyy-mm-dd hh:ii',
            autoclose:true
        });

        $('#online_exam_datetime_edit').datetimepicker({
            startDate :date,
            format: 'yyyy-mm-dd hh:ii',
            autoclose:true
        });
    });

    $(document).on('submit', '#addPeriodQuizForm', function (e) {
        e.preventDefault();

        var quiz_title = $("#quiz_title").val();
        var quiz_description = $("#quiz_description").val();

        var formData = new FormData(this);
        formData.append("save_subjectPeriodQuiz", true);

        $.ajax({
            method: "POST",
            url:"ajax/subject_period_quiz/set_quiz.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                
                var res = jQuery.parseJSON(response);

                console.log(response)
                if(res.status === 422){
                    $('#errorMessage').removeClass('d-none');
                    $('#errorMessage').text(res.message);
                }
                else if(res.status == 200){

                    $('#errorMessage').addClass('d-none');
                    $("#addSubjectPeriodQuizModal").modal('hide');
                    $('#addPeriodQuizForm')[0].reset();

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);

                    $('#subjectPeriodQuizTable').load(location.href + " #subjectPeriodQuizTable");

                }
                else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });

    $(document).on('click', '.subjectPeriozQuizEdit', function () {

        var subject_period__quiz_id = $(this).attr('spq_id');

        $.ajax({
            method: "GET",
            url:"ajax/subject_period_quiz/get_quiz.php?subject_period__quiz_id=" + subject_period__quiz_id,
            success: function (response) {

                var res = jQuery.parseJSON(response);
                
                if(res.status === 200){

                    $("#subject_period_quiz_id").val(subject_period__quiz_id);
                        
                    $quiz_title_edit = $("#quiz_title_edit").val(res.data.quiz_title);
                    $quiz_description_edit = $("#quiz_description_edit").val(res.data.quiz_description);
                    $due_date_edit = $("#online_exam_datetime_edit").val(res.data.due_date);
                    $("#subjectPeriodQuizEditModal").modal('show');

                }
            }
        });
    })
    
    $(document).on('submit', '#editPeriodQuizForm', function (e) {

        e.preventDefault();
        // $quiz_title_edit = $("#quiz_title_edit").val();
        // $quiz_description_edit = $("#quiz_description_edit").val();
        // // $due_date_edit = $("#online_exam_datetime_edit").val();

        // console.log(quiz_title_edit)
        // console.log(quiz_description_edit)
        
        var formData = new FormData(this);
        formData.append("edit_subjectPeriodQuiz", true);

        $.ajax({
            method: "POST",
            url:"ajax/subject_period_quiz/edit_quiz.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {

                var res = jQuery.parseJSON(response);
                console.log(response)

                if(res.status === 422){
                    $('#errorMessage').removeClass('d-none');
                    $('#errorMessage').text(res.message);
                }
                else if(res.status == 200){

                    $('#errorMessage').addClass('d-none');
                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);

                    $("#subjectPeriodQuizEditModal").modal('hide');
                    $('#editPeriodQuizForm')[0].reset();

                    $('#subjectPeriodQuizTable').load(location.href + " #subjectPeriodQuizTable");

                }else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });

    });





    // $(document).ready(function() {
        
    //     $("#add_button").click(function(){

    //         $("#addSubjectPeriodQuizModal").modal('show');
    //     });

    //     var date = new Date();
	//     date.setDate(date.getDate());

    //     $('#online_exam_datetime').datetimepicker({
    //         startDate :date,
    //         format: 'yyyy-mm-dd hh:ii',
    //         autoclose:true
    //     });

    //     $('#online_exam_datetime_edit').datetimepicker({
    //         startDate :date,
    //         format: 'yyyy-mm-dd hh:ii',
    //         autoclose:true
    //     });
 
    //     // $('#add_button').click(function(){
    //     //     // reset_form();
    //     //     // $('#message_operation').html('');
    //     //     $('#formModal').modal('show');

    //     // });

    //     // var date = new Date();
	//     // date.setDate(date.getDate());

    //     // $('#online_exam_datetime').datetimepicker({
    //     //     startDate :date,
    //     //     format: 'yyyy-mm-dd hh:ii',
    //     //     autoclose:true
    //     // });

	//     // $('#exam_form').parsley();

    //     // $("#exam_form").on('submit', function(event) {
    //     //     event.preventDefault();

    //     //     if($('#exam_form').parsley().validate())
    //     //     {
    //     //         $.ajax({
    //     //             url:"ajax/set_quiz.php",
    //     //             method: "POST",
    //     //             data:$(this).serialize(),
    //     //             // dataType:"json",

    //     //             // beforeSend:function(){
    //     //             //     $('#button_action').attr('disabled', 'disabled');
    //     //             //     $('#button_action').val('Validate...');
    //     //             // },
    //     //             success:function(data)
    //     //             {
    //     //                 // Dont know why it didnt return, but in the console it returned a value
    //     //                 if(data == "success"){
    //     //                     // $('#message_operation').html('<div class="alert alert-success">'+data.success+'</div>');

    //     //                     alert('asdds')
    //     //                 }

    //     //                 // alert(data)
    //     //                 $('#formModal').modal('hide');
    //     //                 location.reload();
    //     //                 // if(data.success)
    //     //                 // {
    //     //                 //     $('#message_operation').html('<div class="alert alert-success">'+data.success+'</div>');

    //     //                 //     reset_form();

    //     //                 //     dataTable.ajax.reload();

    //     //                 //     $('#formModal').modal('hide');
    //     //                 // }

    //     //                 // $('#button_action').attr('disabled', false);

    //     //                 // $('#button_action').val($('#action').val());

    //     //                 console.log(data)
    //     //             }
	// 	// 	    });
    //     //     }
    //     // });

    // });

</script>



