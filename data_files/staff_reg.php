<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess(); //check user loggin

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    <?php include('link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">

$(document).on('change','#job_title',function(){

  var val = $(this).val();
   if(val=='new'){
     $('#title_row').html('<input type="text" name="new_job" class="text-input" placeholder="Add New" />')
   } 
})
</script>
</head>

<body>

<?php

if($_POST['staff_no']){

  $id=date("j").rand(100000,999999);
  $new_job = $_POST['new_job'];
  $job_title = $_POST['job_title'];

  if($new_job){
     $job_title = rand(1000,9999);
      $inst = mysqli_query($connect,"INSERT INTO staff_job VALUES('$job_title','$new_job','".date('Y-m-d H:i:s')."')");
       if($inst)
          $status = 'success';
        else
          $status = 'err';
       
  }

  $sql = mysqli_query($connect,"INSERT INTO staff VALUES ('$id','".$_POST['staff_no']."','".ucfirst(strtolower($_POST['first_name']))."','".ucfirst(strtolower($_POST['last_name']))."','".$_POST['contacts']."','".$_POST['email']."','".$_POST['residence']."','$job_title','".date("Y-m-d")."','".$_POST['gender']."','01','".$_POST['nok']."','".$_POST['nok_contacts']."','".$_POST['branch_details']."') ");

      if($sql){
        $status='success';
      }else{
        $status='err';
        $err=mysqli_error($connect);
      }
  ?>
  <script type="text/javascript">
    location.replace("staff_reg.php?action_msg=<?php echo $status ?>&reason=<?php echo $err ?>");
   </script><?php
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
               <!--form header-->
                   <div class="form_header">Staff Registration Form</div>
                     <form method="post" name="form">
                           <div class="form-group" style="width: 96%;margin: auto;">
                                <div class="label">Staff No</div>
                                <input type="text" name="staff_no" class="text-input" />
                           </div>
                            <div class="form-group">
                              <div style="display:grid; grid-template-columns: repeat(2, 1fr);gap:5px;width: 96%;margin: auto;">
                                 <div>
                                    <div class="label">First Name</div>
                                     <input type="text" name="first_name" class="text-input" autocomplete="off" />
                                 </div>
                                 <div>
                                   <div class="label">Last Name</div>
                                    <input type="text" name="last_name" class="text-input" autocomplete="off" />
                                 </div>
                           </div>
                           <div class="form-group">
                                <div class="label">Gender</div>
                                 <select name="gender" class="text-input">
                                   <option selected="selected" value="">Select</option>
                                   <option value="Male">Male</option>
                                   <option value="Female">Female</option>
                                 </select>
                            </div>
                            <div class="form-group">
                                <div class="label">Contacts</div>
                                <input type="text" name="contacts" class="text-input" />
                            </div>
                            <div class="form-group">
                                 <div class="label">Email</div>
                                 <input type="text" name="email" class="text-input" />
                             </div>
                             <div class="form-group">
                                 <div class="label">Residence</div>
                                 <input type="text" name="residence" class="text-input" />
                              </div>
                              <div class="form-group">
                              <div class="label">Job Title</div>
                                <span id="title_row">
                                  <select name="job_title" class="text-input" id="job_title">
                                    <option selected="selected" value="">Select</option>
                                     <?php
                                         $sql = mysqli_query($connect,"SELECT * FROM staff_job");
                                           if(mysqli_num_rows($sql)){
                                             while ($r = mysqli_fetch_array($sql)) {
                                               echo '<option value="'.$r['id'].'">'.$r['job_title'].'</option>';
                                             }
                                           }
                                      ?>
                                      <option value="new">Add New</option>
                                  </select>
                                </span>
                              </div>
                              <div class="form-group">
                           <div class="label">Branch</div>
                             <select name="branch_details" class="text-input">
                               <option value="00010" selected="">Select</option>
                               <?php
                                  $sql = mysqli_query($connect,"SELECT * FROM branches");
                                   if(mysqli_num_rows($sql)){
                                     while($r = mysqli_fetch_array($sql)){
                                      if($r[1])
                                       echo '<option value="'.$r['id'].'">'.$r['branch_name'].'</option>';
                                     }
                                   }
                               ?>
                             </select>
                       </div>
                              <div class="form-group">
                                 <div class="label">Next of Kin</div>
                                 <input type="text" name="nok" class="text-input" />
                              </div>
                              <div class="form-group">
                                <div class="label">Next of Kin - Contacts</div>
                                <input type="text" name="nok_contacts" class="text-input" />
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