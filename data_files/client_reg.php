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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.3.1/css/all.min.css">
    <?php include('link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">

</script>

</head>
<body>
<?php



if($_POST['first_name']){

//define constant
define("FILEREPOSITORY",'profile/');

      $id= date("j").rand(100000,999999);
      $apply = $_POST['apply'];

// --set image attributes for upload
  if(is_uploaded_file($_FILES['img_file']['tmp_name'])){

         $photo_name = $_FILES['img_file']['name'];
         $file_type = $_FILES['img_file']['type'];
         $photo_upd = $_FILES['img_file']['tmp_name'];
         
         //get the extension of the file
         $base = basename($photo_name);
         $extension = substr($base, strlen($base)-4, strlen($base));
         $allowed_extension = array(".jpg",".png",".jpeg",".PNG");

  if(in_array($extension,$allowed_extension)){
             if(!is_dir(FILEREPOSITORY.date("Y-m-d"))){
                  mkdir(FILEREPOSITORY.date("Y-m-d"));
                }

             $dir = date("Y-m-d").'/'.$id.'_'.strtotime(date('Y-m-d H:i:s')).$extension; //returns directory for uploading image
             move_uploaded_file($photo_upd,FILEREPOSITORY.date("Y-m-d").'/'.$id.'_'.strtotime(date('Y-m-d H:i:s')).$extension); //uploads file to respective directory
  }else{
        $response = 'Un-Supported Image File Format. <a href="" id="status_id">Try Again.!</a>';
    }
  //--//  
}

  if($response){

      $status = $response;
  }else{

    //insert query
    $inst_query = "INSERT INTO clients VALUES ('$id','".ucfirst(strtolower($_POST['first_name']))."','".ucfirst(strtolower($_POST['last_name']))."','".$_POST['contacts']."','".$_POST['email']."','".$_POST['residence']."','".$_POST['occupy']."','".$_POST['gender']."','".$_POST['city']."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."','$dir','".$_POST['branch_details']."','01','".$_POST['member_id']."') ";

    $inst = mysqli_query($connect, $inst_query);
        
      if($inst)
          $status = 'success';
      else{
        $status = 'err';
        $reason = mysqli_error($connect);
      }
  }

 ?>
 <script type="text/javascript">
        location.replace("client_reg.php?action_msg=<?php echo $status ?>&reason=<?php echo $reason ?>");
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
              <div class="form_header">New Member</div>
                <form method="post" name="form" id="client_reg" method="post" enctype="multipart/form-data" >
                      <div class="form-group">
                              <div class="label">Member Id</div>
                              <input type="text" name="member_id" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                       <div style="display:grid; grid-template-columns: repeat(2, 1fr);gap:10px;">
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
                           <div class="label">Residence / Address</div>
                           <input type="text" name="residence" class="text-input" />
                       </div>
                       <div class="form-group">
                          <div class="label">Business Name</div>
                          <input type="text" name="occupy" class="text-input" />
                        </div>
                        <div class="form-group">
                           <div class="label">City</div>
                           <input type="text" name="city" class="text-input" />
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
                        <div class="label">Photo</div>
                          <div class="file-wrapper">
                            <div class="upload-btn-wrapper">
                              <button class="btn upload-file font-weight-500">
                                <span class="upload-btn">
                                    <i class="fas fa-cloud-upload-alt d-block font-50 pb-2"></i>
                                      Click Here to Browse folders
                                  </span>
                                 <span class="upload-select-button" id="blankFile">
                                       Supports JPG, GIF and PNG
                                  </span>
                                  <span class="success">
                                       <i class="far fa-check-circle text-success"></i>
                                   </span>
                               </button>
                           <input type="file" name="img_file" id="img_file" value="" />
                           </div>
                         </div>
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