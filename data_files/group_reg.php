<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
error_reporting(0);

check_sess(); //check user loggin

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.3.1/css/all.min.css">
    <?php include('link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>
</head>
<body>
<?php

if($_POST['group_name']){

      $id= date("j").rand(100000,999999);
      $apply = $_POST['apply'];

    //insert query
    $inst_query = "INSERT INTO groups (id, group_type, group_name, date_created, village, meeting_day, contact_number, contact_person) VALUES ('$id','".ucfirst(strtolower($_POST['group_type']))."','".ucfirst(strtolower($_POST['group_name']))."','".$_POST['date_created']."','".ucfirst(strtolower($_POST['village']))."','".ucfirst(strtolower($_POST['meeting_day']))."','".$_POST['contact_number']."','".ucfirst(strtolower($_POST['contact_person']))."') ";

    $inst = mysqli_query($connect, $inst_query);
        
      if($inst){
          $status = 'success';
      } else {
        $status = 'err';
        $reason = mysqli_error($connect);
      }

 ?>
 <script type="text/javascript">
        location.replace("group_reg.php?action_msg=<?php echo $status ?>&reason=<?php echo $reason ?>");
  </script>
 <?php
 }
?> 
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <div class="form_wrap_min">
             <?php if($_GET){ include('action_msg.php'); } ?>
              <div class="form_header">Group Registration</div>
                <form method="post" name="form" id="group_reg" method="post" >
                      <div class="form-group">
                              <div class="label">Group Type</div>
                              <input type="text" name="group_type" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Group Name</div>
                              <input type="text" name="group_name" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Date Created</div>
                              <input type="date" name="date_created" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Village</div>
                              <input type="text" name="village" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Meeting Day</div>
                              <input type="text" name="meeting_day" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Contact Number</div>
                              <input type="text" name="contact_number" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Contact Person</div>
                              <input type="text" name="contact_person" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                          <button type="submit" name="btnSubmit" class="button-input">Submit</button>
                     </div>
                  </form>
            </div>
        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>
