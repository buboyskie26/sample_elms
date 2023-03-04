<?php require_once('includes/teacherHeader.php');?>



<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>PHP CRUD using jquery ajax without page reload</title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
</head>
<body>


<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>PHP Ajax CRUD without page reload using Bootstrap Modal
                        
                        <button type="button" class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#studentAddModal">
                            Add Student
                        </button>
                    </h4>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $query = $con->prepare("SELECT * FROM course");
                                $query->execute();

                                if($query->rowCount() > 0)
                                {
                                    while($course = $query->fetch(PDO::FETCH_ASSOC)){
                                        ?>
                                            <tr>
                                                <td><?= $course['course_name'] ?></td>
                                                <td>
                                                    <button type="button" value="<?=$course['course_id'];?>" class="viewStudentBtn btn btn-info btn-sm">View</button>
                                                    <button type="button" value="<?=$course['course_id'];?>"
                                                        class="editStudentBtn btn btn-success btn-sm"
                                                        
                                                        >Edit</button>
                                                    <button type="button" value="<?=$course['course_id'];?>" class="deleteStudentBtn btn btn-danger btn-sm">Delete</button>
                                                </td>
                                            </tr>
                                        <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>



<!-- Add Student -->
<div class="modal fade" id="studentAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="saveStudent">
                <div class="modal-body">

                    <div id="errorMessage" class="alert alert-warning d-none"></div>

                    <div class="mb-3">
                        <label for="">Course Name</label>
                        <input type="text" name="course_name" class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student -->
<div class="modal fade" id="studentEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="updateStudent">
                <div class="modal-body">

                    <div id="errorMessageUpdate" class="alert alert-warning d-none"></div>

                    <input type="hidden" name="course_id" id="course_id" >

                    <div class="mb-3">
                        <label for="">Course Name</label>
                        <input type="text" name="course_name" id="course_name" class="form-control" />
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>

    $(document).on('submit', '#saveStudent', function (e) {

        e.preventDefault();

        var formData = new FormData(this);
        formData.append("save_student", true);
        
        // Result would be the form of #saveStudent
        // console.log(this);
        $.ajax({
            type: "POST",
            url: "s_code.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                
                var res = jQuery.parseJSON(response);

                if(res.status == 422) {
                    $('#errorMessage').removeClass('d-none');
                    $('#errorMessage').text(res.message);

                }
                else if(res.status == 200){
                    
                    $('#errorMessage').addClass('d-none');
                    $('#studentAddModal').modal('hide');
                    $('#saveStudent')[0].reset();

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);

                    $('#myTable').load(location.href + " #myTable");

                }else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });
    //
    
    $(document).on('click', '.editStudentBtn', function () {

        var course_id = $(this).val();

        // Populate the data from the backend first before
        // Populate to the modal.
        $.ajax({
            type: "GET",
            url: "s_code.php?course_id=" + course_id,
            success: function (response) {

                var res = jQuery.parseJSON(response);
                // console.log(res)
                if(res.status == 404) {
                    alert(res.message);
                }else if(res.status == 200){
                    
                    // console.log(res.data);
                    // $('#course_id').val(res.data.course_id);
                    // $('#course_name').val(res.data.course_name);

                    $("#course_id").val(res.data.course_id);
                    $("#course_name").val(res.data.course_name);
                    $('#studentEditModal').modal('show');
                }
            }
        });
    });
    //
    
    $(document).on('submit', '#updateStudent', function (e) {

        e.preventDefault();

        var formData = new FormData(this);
        formData.append("update_student", true);

        var course_name = $("#course_name").val();
        var course_id = $("#course_id").val();

        $.ajax({
            type: "POST",
            url: "s_code.php",
            data: {
                'update_student': true,
                'course_name' : course_name,
                'course_id' : course_id,
            },
            // data: formData,
            // processData: false,
            // contentType: false,
            success: function (response) {

                var res = jQuery.parseJSON(response);
                if(res.status === 422){
                    $("#errorMessageUpdate").removeClass('d-none');
                    $("#errorMessageUpdate").text(res.message);

                }else if(res.status == 200){

                    $('#errorMessageUpdate').addClass('d-none');

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);
                    
                    console.log(location.href)
                    // Modal target
                    $('#studentEditModal').modal('hide');
                    // Form
                    $('#updateStudent')[0].reset();
                    // This will redirect the browser to whatever the location
                    $('#myTable').load(location.href + " #myTable");

                }else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });

    });

    $(document).on('click', '.deleteStudentBtn', function(e){

        var course_id = $(this).val();
        
         if(confirm('Are you sure you want to delete this data?')){
            $.ajax({
                type: "POST",
                url: "s_code.php",
                data: {
                    'delete_student': true,
                    'course_id': course_id
                },
                success: function (response) {
                     
                    // console.log(response)
                    var res = jQuery.parseJSON(response);

                    if(res.status == 500) {
                        alert(res.message);
                    }
                    else  if(res.status == 200){

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);

                        $("#myTable").load(location.href + " #myTable");
                    }
                }
            })
         }
    });

</script>