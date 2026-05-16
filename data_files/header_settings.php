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

$(document).on('change','select[name="option"]',function(){
  
var val = $(this).val();
var split_val = val.split('_');
var id = $(this).attr('id');
var index = id.split('_');


if(split_val[1]=='edit'){

   $.ajax({
    type:'POST',
    url:'../data_files/data_src.php',
    data:{
      'edit_header':split_val[0]
    },
    success:function(d){

           var detail = d.split('>');
           $('#names').val(detail[0]);
           $('#address').val(detail[1]);
           $('#location').val(detail[5]);
           $('#email').val(detail[4]);
           $("#contact").val(detail[2]);
           $("#website").val(detail[3]);
           $("#usr_id").attr("name","update");
        }
    })
   $("#spanModal").attr('select',id);
  }

 if(split_val[1]=='preveiw'){
   
   let header = $('#header_wrap').html();
   $('#myModal_2').css({'display':'block'},{'z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');

    setTimeout(function(){
       $('#myModal_2').css('display','none');
       $('#myModal').css('display','block');
       $('.modal-content').toggleClass('modal-small-size');
       $('#display').html(header);
    },1000)}
 $("#spanModal").attr('select',id);
})

</script>
</head>

<body>
<?php

 if($_POST['submit']){

    $names = mysqli_real_escape_string($connect,$_POST['names']);
    $email = mysqli_real_escape_string($connect,$_POST['email']);
    $address = mysqli_real_escape_string($connect,$_POST['address']);
    $contact = mysqli_real_escape_string($connect,$_POST['contact']);
    $website = mysqli_real_escape_string($connect,$_POST['website']);
    $loc = mysqli_real_escape_string($connect,$_POST['location']);

    //define constant
  define("FILEREPOSITORY",'../img_file/');


  // --set image attributes for upload
  if(is_uploaded_file($_FILES['file']['tmp_name'])){

         $photo_name = $_FILES['file']['name'];
         $file_type = $_FILES['file']['type'];
         $photo_upd = $_FILES['file']['tmp_name'];
         
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


  $inst = mysqli_query($connect,"INSERT INTO header_tpl VALUES('".rand(10000,99999)."','$names','$address','$contact','$website','$email','$loc','".date('Y-m-d')."','$dir') ");

    if($inst){
      $status='success';
    }else{
      $status='err';
      $response = mysqli_error($connect);
    }
 ?>
    <script type="text/javascript">
     location.replace("header_settings.php?action_msg=<?php echo $status ?>");
   </script>
    <?php 
   
}

if($_POST['update']){

    $names = mysqli_real_escape_string($connect,$_POST['names']);
    $email = mysqli_real_escape_string($connect,$_POST['email']);
    $address = mysqli_real_escape_string($connect,$_POST['address']);
    $contact = mysqli_real_escape_string($connect,$_POST['contact']);
    $website = mysqli_real_escape_string($connect,$_POST['website']);
    $loc = mysqli_real_escape_string($connect,$_POST['location']);
    
    //define constant
  define("FILEREPOSITORY",'../img_file/');


  // --set image attributes for upload
  if(is_uploaded_file($_FILES['file']['tmp_name'])){

         $photo_name = $_FILES['file']['name'];
         $file_type = $_FILES['file']['type'];
         $photo_upd = $_FILES['file']['tmp_name'];
         
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

$photo_dir = $_POST['photo_dir'];
 if($dir)
  $photo_dir = $dir;
   
   $upd = mysqli_query($connect,"UPDATE header_tpl SET company_details='$names', address='$address', contacts='$contact', website='$website', email='$email', location='$loc', photo='$photo_dir' ");
   if($upd){
     $status='success';
   }else{
     $status='err';
   }
 }

  ?>
    <script type="text/javascript">
     location.replace("header_settings.php?action_msg=<?php echo $status ?>");
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

  <div class="form_header">Header Settings</div>
    <form name="form1" id="form1" method="post" action="header_settings.php" enctype="multipart/form-data">
        <input name="usr_id" type="hidden" value="1" id="usr_id" />  
          <div class="form-group">
            <div class="label">Company Details</div>
            <input type="text" name="names" class="text-input" autocomplete="off" id="names" />
           </div>
           <div class="form-group">
            <div class="label">Postal Address</div>
            <input type="text" name="address" id="address" class="text-input" autocomplete="off" />
           </div>
           <div class="form-group">
            <div class="label">Location</div>
            <input type="text" name="location" id="location" class="text-input" autocomplete="off"  />
           </div>
           <div class="form-group">
            <div class="label">Telephone Contact</div>
            <input type="text" name="contact" id="contact" class="text-input" autocomplete="off" />
           </div>
           <div class="form-group">
            <div class="label">Website Address</div>
            <input type="text" name="website" id="website" class="text-input" autocomplete="off" />
           </div>
           <div class="form-group">
            <div class="label">Email</div>
            <input type="text" name="email" id="email" class="text-input" autocomplete="off" />
           </div>
           <div class="form-group">
            <div class="label">Upload Logo</div>
            <input type="file" name="file" id="file" />
           </div>
             <div class="form-group">
                <button type="submit" name="submit" class="button-input" id="submit_hdr">Submit</button>
            </div>                                     
         </form>
        </div>
    <div style="width:90%;margin:15px auto;">
      <div id="header_wrap">
        <div id="header_tpl"><?php echo po_address($connect) ?></div>
      </div>
      <div class="report_header">View List</div>
       <table width="90%" cellpadding="5" cellspacing="0" align="center" class="report_display">
         <tr>
           <td>No</td>
           <td>Company Details</td>
           <td>Postal Address</td>
           <td>Location</td>
           <td>Telephone</td>
           <td>Website</td>
           <td>Email</td>
           <td></td>
         </tr>     
      <?php
      $q='';
      $q = "SELECT * FROM  header_tpl"; 
     $sql = mysqli_query($connect,$q);
     $no = 0;
     if(mysqli_num_rows($sql)){
      while($row=mysqli_fetch_array($sql)){
          ?>
          <tr id="status_<?php echo $x+=1 ?>">
           <td><?php echo $no+=1 ?></td>
           <td><?php echo $row[1] ?></td>
           <td><?php echo $row[2] ?></td>
           <td><?php echo $row[6] ?></td>
           <td><?php echo $row[3] ?></td>
           <td><?php echo $row[4] ?></td>
           <td><?php echo $row[5] ?></td>
           <td>
           <select name="option" style="width:80px;" id="action_1" class="text-input">
              <option value="" selected="selected">Action</option>
              <option value="<?php echo $row[0].'_edit' ?>">Edit</option>
              <option value="<?php echo $row[0].'_preveiw' ?>">Preview</option>
          </select>
        </td>
       </tr>
          <?php
      }
     }
     ?>      
      <tr>
         <td colspan="9"><div style="height:40px;">&nbsp;</div></td>
      </tr>
      </table>
    </div>
      </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>