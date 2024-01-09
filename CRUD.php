<?php
include ('dbconnect.php');
$sql = "SELECT * FROM studentDetails WHERE isValid != 1";
$query = sqlsrv_query($con, $sql);

// Insert Query
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
  
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $password = $_POST['password'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $department = $_POST['department'];

        $sql = "INSERT into studentDetails  (firstName, lastName, contact, email, department, password, isValid) 
                values ('$firstName', '$lastName', '$contact', '$email', '$department','$password', 0)";
        $run = sqlsrv_query($con, $sql);

        if ($run) {    
            header('Location: CRUD.php');
        } else {
            echo "Error: " . print_r(sqlsrv_errors(), true);
        }
    }
}
// Uodate Query
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
      // action self defined : Reference in HTML content
    if ($_POST['action'] == 'update') {
        $studentID = $_POST['id']; 
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $department = $_POST['department'];

        $updateSql = "UPDATE studentDetails SET firstName = '$firstName', lastName = '$lastName', email = '$email', 
                     contact = '$contact', department = '$department' WHERE studentID = $studentID";

        $updateQuery = sqlsrv_query($con, $updateSql);
        if ($updateQuery === false) {
            echo "Update failed: " . print_r(sqlsrv_errors(), true);
        } else {   
            header('Location: CRUD.php');
        }
    }
}
// Soft Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'soft_delete') {
        $studentID = $_POST['id'];
      
        $deleteSql = "UPDATE studentDetails SET isValid = 1 WHERE studentID = $studentID";

        $deleteQuery = sqlsrv_query($con, $deleteSql);

        if ($deleteQuery === false) {
            echo "Error: " . print_r(sqlsrv_errors(), true);
        } else {
            header('Location: CRUD.php');
        }
    }
}

//hard delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] == 'delete') {
      $studentID = $_POST['id'];

$deleteSql = "DELETE studentDetails WHERE studentID = $studentID";
$deleteQuery = sqlsrv_query($con, $deleteSql);

if ($deleteQuery === false) {
    echo "Error: " . print_r(sqlsrv_errors(), true);
} else {
  header('Location: CRUD.php');
}
  }
}

//filter Query
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$filtersql = "SELECT * FROM studentDetails WHERE isValid!= 1";
if (!empty($filter)) {
    $filtersql .= " AND firstName LIKE '%$filter%'";
}
$query = sqlsrv_query($con, $filtersql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="modalstyle.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <style>
    .footer-btn {
      display: flex;
      flex-direction: row;
      gap: 15px;
      justify-content: center;
      align-items: center;
      width: 100%;
    }

    .footer-btn button {
      width: 40%;
    }
    #heading button a{
      color: white;
      text-decoration: none;

    }
   #registrationForm {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    #registrationForm input{
        padding: 1%;
    }
    #registrationForm label{
        font-size: 17px;
        font-weight: 400;
    }
    body{
    }

  </style>

  <title>Table Display</title>
</head>

<body>

<div class="container center " id="heading">
  <h3>Table Data Display</h3>

  <h4>New Student</h4>
  <button class="btn btn-primary" id="addNewButton">Add New</button>
  <button class="btn btn-primary" id="addNewButtonAjax">Add New Ajax</button>
</div>

<div class="filter-option">
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
  <label for="filter">Filter by Name:</label>
  <input type="text" name="filter" id="filter" placeholder="Enter name">
  <button type="submit">Filter</button>
</form>
<form id="ajaxFilter">
  <label for="filter">Filter by Name:</label>
  <input type="text" name="filter" id="filter" placeholder="Enter name">
  <button type="button" id="apply_filter">Filter</button>
</form>

  </div>

  <table class="table">
    <thead> 
        <!-- column name -->
      <tr>
        <th scope="col">StudentID</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Contact</th>
        <th scope="col">Email</th>
        <th scope="col">Department</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      while ($row = SQLSRV_FETCH_ARRAY($query, SQLSRV_FETCH_ASSOC)) {
        $studentID = $row['studentID'];
      ?>
      <?php  $id=$row['studentID']; ?>
        <tr>
          <td><?php echo $row['studentID'] ?></td>
          <td class="name"><?php echo $row['firstName'] ?></td>
          <td class="lastname"><?php echo $row['lastName'] ?></td>
          <td class="contact"><?php echo $row['contact'] ?></td>
          <td class="email"><?php echo $row['email'] ?></td>
          <td class="department"><?php echo $row['department'] ?></td>
          <td>
            <button type="button" class="btn btn-primary edit" id="<?php echo $row['studentID']; ?>">Edit</button>
            <button type="button" class="btn btn-danger delete" data-bs-toggle="modal" data-bs-target="#deletemodal" data-record-id="<?php echo $row['studentID']; ?>">
              Delete
            </button>
            <button type="button" class="btn btn-success soft_delete" data-bs-toggle="modal" data-bs-target="#deletemodal" data-record-id="<?php echo $row['studentID']; ?>">
              Soft Delete
            </button>
          </td>
        </tr>
      <?php
      }

      ?>


    </tbody>
  </table>

  <!-- Registration Form Modal -->
<div class="modal" id="registrationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add New Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="registration_form">
                <div class="modal-body" id="registrationForm">

            <label>First Name</label>
            <input type="text" name="firstName" placeholder="Your Name">
            <label>Last Name</label>
            <input type="text" name="lastName" placeholder="Enter Username"  autocomplete=off>
            <label>Department</label>
            <input type="text" name="department" placeholder="Department" >
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Password"  autocomplete=off>
            <label>Contact Number</label>
            <input type="tel" name="contact" placeholder="Write Your Contact Number" >
            <label>Email</label>
            <input type="email" name="email" placeholder="Your Email" >
            
            <input type="hidden"  name="action" value="add">

                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Registration Form Modal Ajax  -->
<div class="modal" id="ajaxModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add New Student</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <form id="ajax_form">
                <div class="modal-body" id="registrationForm">

            <label>First Name</label>
            <input type="text" name="firstName" placeholder="Your Name">
            <label>Last Name</label>
            <input type="text" name="lastName" placeholder="Enter Username"  autocomplete=off>
            <label>Department</label>
            <input type="text" name="department" placeholder="Department" >
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Password"  autocomplete=off>
            <label>Contact Number</label>
            <input type="tel" name="contact" placeholder="Write Your Contact Number" >
            <label>Email</label>
            <input type="email" name="email" placeholder="Your Email" >
            
          
            <input type="hidden"  name="action" value="add">

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"  id="ajax_submit" data-bs-dismiss="modal">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Are you sure you want to delete this data?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer" id="modal-footer">
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                    <input type="hidden" id="dataid" name="id" value="<?php echo $id;?>">
                    <input type="hidden"  name="action" value="delete">
                    <div class="footer-btn">
                        <button type="submit" class="btn btn-danger" id="deleteButton">Yes</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- soft delete Modal -->
<div class="modal fade" id="softDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Are you sure you want to soft delete this data?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer" id="modal-footer">
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                    <input type="hidden" id="dataid" name="id" value="">
                    <input type="hidden"  name="action" value="soft_delete">
                    <div class="footer-btn">
                        <button type="submit" class="btn btn-danger" id="softDeleteButton">Yes</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

  <!--  Update Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Edit information</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal body -->
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="ajax_form_update">
          <div class="modal-body" id="modalForm">

          <label>First Name</label>
            <input type="text" name="firstName" placeholder="Your Name" class="inputfirstName">
            <label>Last Name</label>
            <input type="text" name="lastName" placeholder="Enter Username" class="inputlastName">
            <label>Department</label>
            <input type="text" name="department" placeholder="Department" class="inputdepartment">
            <label>Contact Number</label>
            <input type="tel" name="contact" placeholder="Write Your Contact Number" class="inputcontact">
            <label>Email</label>
            <input type="email" name="email" placeholder="Your Email" class="inputemail">
          
            <input type="hidden" id="dataid" name="id" value="<?php echo $id;?>">
            <input type="hidden"  name="action" value="update">
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Submit</button>
            <button type="submit" class="btn btn-danger" data-bs-dismiss="modal" id="ajaxupdate">Ajax Submit</button>
            
          </div>
        </form>

      </div>
    </div>
  </div>
</body>

</html>
<script>
    
  //J query 
  $(document).on("click", ".edit", function() {

    var studentID = $(this).attr('studentID');
    console.log(studentID);
   var ID= $('#dataid').val(studentID);
  
    $('#myModal').modal('show');
    
    //  first name
    var infirstName = $(this).closest('tr').find('.firstName').text();
    $('.inputfirstName').val(infirstName);
    //last name
    var inusername = $(this).closest('tr').find('.lastName').text();
    $('.inputlastName').val(inusername);
    // contact number
    var incontactnumber = $(this).closest('tr').find('.contact').text();
    $('.inputcontact').val(incontactnumber);

    // email
    var inemail = $(this).closest('tr').find('.email').text();
    $('.inputemail').val(inemail);
    //department
  var indepartment = $(this).closest('tr').find('.department').text();
    $('.inputdepartment').val(indepartment);

  });


  $(document).on("click", ".delete", function() {
    var recordId = $(this).data('record-id');
    $('#deleteModal #dataid').val(recordId);
    $('#deleteModal').modal('show');
});

$(document).on("click", ".soft_delete", function() {
    var recordId = $(this).data('record-id');
    $('#softDeleteModal #dataid').val(recordId);
    $('#softDeleteModal').modal('show');
});
  
$(document).ready(function(){
 $("#addNewButton").click(function(){
   $('#registrationModal').modal('show');
 });


 $("#addNewButtonAjax").click(function(){
   $('#ajaxModal').modal('show');
 });

 $(document).on("click","#ajax_submit",function(){
  $.ajax({
  url:'output.php',
  method:'POST',
  data:$("#ajax_form").serialize(),
  success: function(data){
    console.log(data);
    location.reload();
  },
  error:function(data){
    console.log(error);
  }
});
});
//Ajax Update 

 $(document).on("click","#ajaxupdate",function(){
  $.ajax({
  url:'Edit.php',
  method:'POST',
  data:$("#ajax_form_update").serialize(),
  success: function(data){
    console.log(data);
    location.reload();
  },
  error:function(data){
    console.log(error);
  }
});
});

$("#apply_filter").click(function(){
 var filtervalue=$("#filter").val();
 $.ajax({
   url:'filter.php',
   method:'GET',
   data:{
    filter:filtervalue
   },
   success:function(data){
    $("tbody").html(data);
   },
   error:function(error){
    console.log(error);
   }
 });
});

});


</script>