<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");

if(!isset($_SESSION['sess_usr'])){
  ?>
   <script type="text/javascript">
    location.replace("../index.php");
   </script>
  <?php
}

?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
  	 <title>Prime Commercial Services :: loan System</title>
  	 <meta charset="UTF-8" />
<!-- linking only css files -->
<link rel="stylesheet" href="../data_css/layout.css" />

<!-- linking only javascript files -->
<script type="text/javascript" src="../data_scripts/jquery-2.1.1.min.js"></script>

<script type="text/javascript">
//to check all checkboxes
$(document).on('click','#all',function(){
	$('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
});

$(function(){
  //$("#user_reports").css("display","none");
});

$(document).on('change','#staff',function(){
  var staffNo = $(this).val();
  $.ajax({
    type:'POST',
    url:'../bin/src_data.php',
    data:{
      'staff_no': staffNo
    },
    success:function(data){
      if(data=='n/a'){
      $('#user').attr('placeholder','Enter User Name');
      }else{
        $('#user').val(data);
      }
    }
  });
});

$(document).on('keyup',"#passconf",function(){
  var pwd = $('#pwd').val();
  var passconf = $(this).val();
  if(pwd!=passconf){
    $('#confirm2').html('Password Mis-Match');
  }else if(pwd==passconf){    
    $('#confirm2').html('<img src="../img_file/icon-success.png" width="20px" height="20px" />');
  }
});

$(document).on('keyup',"#pwd",function(){
  var pwd = $('#pwd').val();
  var char_val = pwd.length;
  if(char_val<=7){
    $('#pass').html('Atleast 8 Characters');
    $('#pass').css('color','#F00');
  }else{    
    $('#pass').html('<img src="../img_file/icon-success.png" width="20px" height="20px" />');
  }
})
</script>
</head>
  <body>
<div class="mainWrapper">
  <div class="top">
  <div class="header_user"><span id="log" style="cursor:pointer">Hello User&nbsp;&nbsp;<img src="../img_file/logout.png" width="14" height="14" /></span></div>
  <div class="top_header">
    <span style="font-weight:bold;">Loan System</span>
  </div>
  <div  class="topMenu">
    <div style="float:left;">Prime Commerical Services</div>
  </div>
 </div>
 <div class="left-wrapper">
  <div class="left-wrapper-inna">
      <a href="../dash.php" class="mini_link">Home</a>
  </div>
  <div class="left-wrapper-inna">
      <a href="../index.php" class="mini_link">Log Out</a>
  </div>
</div>
<?php
       if($_POST['submit'])
        {
          $staff = $_POST['staff'];
          $user = $_POST['user'];
          $pass1 = $_POST['password'];
          $pass2 = $_POST['passconf'];
          $type = $_POST['type'];
          $branch = $_POST['branch'];
          $id = date("j").rand('10000','99999');
          $date_reg = date("Y-m-d H:i:s");
          $date_set = date("Y-m-d");
          $prev = $_POST['prev'];
          
          if($pass1 == $pass2)
          {

            $insert = mysqli_query($connect,"INSERT INTO users values('$id','$staff','$user',MD5('$pass1'),'$type','$date_reg','1','$date_set')");
            if($insert)
            {
              $status = 1; 
            }else{
              $status = 0;
              $err_msg=mysqli_error($connect);
            }
          }
          else
          {
             $mis_match=1;
             $staff_user = $staff;
             $initial_pwd = $pass1;
             $wrg_pwd = $pass1;
          }
  }

if($status=='1'){
      ?>
      <script type="text/javascript">
        location.replace("set_user.php?action_msg=success");
      </script>
      <?php
    }
    
    if($status=='0'){
      ?>
      <script type="text/javascript">
        location.replace("set_user.php?action_msg=err");
      </script>
      <?php
     }

    if($mis_match){
      ?>
      <script type="text/javascript">
        location.replace("set_user.php?action_msg=mis_match&user=<?php echo $user ?>&staff=<?php echo $staff ?>&pwd=<?php echo $initial_pwd ?>");
      </script>
    <?php
    }
  ?>
<div class="contentWrapper">
  <div class="inna_wrapper">
  <div class="home_wrapper" style="height:90% !important">
    <?php
           if($_GET['action_msg']){
             $action_status = $_GET['action_msg'];
             if($action_status=='mis_match'){
               ?>
                <script type="text/javascript">
                  $(document).ready(function(){
                     $('#staff').val("<?php echo $_GET['staff'] ?>");
                     $('#user').val("<?php echo $_GET['user'] ?>");
                     $('#pwd').val("<?php echo $_GET['pwd'] ?>");
                     $('#passconf').css('border','solid #F00 1px').focus();
                     $('#confirm2').html('Password Mis-Match').css('color','#F00');
                   });
                </script>
               <?php
             }
             else{
              if($action_status=='success'){
                echo '<div style="width:100%;padding:5px;border-radius:5px;background-color:#CCC;">Action Successfull....!!!</div>';
                 }else{
                ?><div style="width:100%;padding:5px;border:radius:5px;background-color:#CCC;">Un-Successfull. Something Went Wrong. Please Try Again..!!!</div><?php
                }
             }
           }
          ?>
    <div style="float:right;width:68%;padding-top:10px;" id="user_list">
      <span class="header" style="display:block; border-bottom:solid 1px #CCC;font-size:14px;">List of Users</span><br>
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="line" style="color: #145FA7;">No</td>
          <td class="line" style="color: #145FA7;">Staff No</td>
          <td class="line" style="color: #145FA7;">User</td>
          <td class="line" style="color: #145FA7;">Type</td>
          <td class="line" style="color: #145FA7;">Last Login</td>
          <td class="line" style="color: #145FA7;">Status</td>
          <td></td>
        </tr>
        <?php
        $sql = mysqli_query($connect,"SELECT * FROM users");
        if(mysqli_num_rows($sql)){
          while($rw=mysqli_fetch_array($sql)){
            ?>
            <tr>
              <td class="line"><?php echo $count+=1 ?></td>
              <td class="line"><?php echo $rw[1] ?></td>
              <td class="line"><?php echo $rw[2] ?></td>
              <td class="line"><?php echo $rw[4] ?></td>
              <td class="line"><?php echo date("d-m-Y",strtotime($rw[5])) ?></td>
              <td class="line"><?php echo $rw[6] ?></td>
              <td class="line">
                 <select name="action" class="action" id="<?php echo $rw[0] ?>" style="height:35px">
                   <option value="" selected="selected">Action</option>
                   <option value="<?php echo $rw[0] ?>_edit">Edit</option>
                   <option value="<?php echo $rw[0] ?>_terminate">Terminate</option>
                 </select>
              </td>
            </tr>
            <?php
          }
        }
        ?>
      </table>
    </div>
    <div id="resize" style="width:30%;" >
    <form name="form1" id="form1" method="post">
           <div class="form-group">
              <span class="header" style="display:block; border-bottom:solid 1px #CCC;font-size:14px;">Set Up User Account</span>
            </div>
            <div class="form-group">
               <div class="label">Staff No </div><br>
               <input type="text" name="staff" id="staff" class="text-input" style="width:60%"/>
            </div>
            <div class="form-group">
              <div class="label">User Name </div><br>
              <input name="user" id="user" type="text" class="text-input" style="width:60%"/>
            </div>
            <div class="form-group">
              <div class="label">Password </div><br>
              <input name="password" id="pwd" type="password" value="" class="text-input" style="width:60%" />
              <div id="pass" style="font-family:'Segoe UI';width:35%;float:right;"></div>
            </div>
            <div class="form-group">
              <div class="label">Confirm Password </div><br>
              <input name="passconf" id="passconf" type="password" value="" class="text-input" style="width:60%" /><div id="confirm2" style="float:right;width:35%;font-family:'Segoe UI'; color:#009900;"></div>
            </div>
            <div class="form-group">
              <div class="label">User Account </div><br>             
                 <select name="type" id="type" class="text-input" style="width:60%">
                    <option value="" selected="selected">--Select--</option>                    
                    <option value="staff">Staff User</option>
                    <option value="office">Office Administor</option>
                    <option value="director">Director</option>
                    <option value="software">Software Admin</option>
                 </select>
            </div>             
            <div class="form-group">
               <input name="submit" type="submit" value="Submit" class="button-input" />
            </div>
    </form>
  </div>      
</div>
</div>
</div>
</div>
</div>
  <?php
   require("../footer.php");
  ?>
  </body>
</html>