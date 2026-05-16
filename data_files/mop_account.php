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
    
   <title><?php echo sys_tab_hdr() ?></title>
   <?php include('link_docs.php') ?>
<script type="text/javascript">

</script>
</head>

<body>
<?php


   if(isset($_POST['mop'])){
     
 $inst = mysqli_query($connect," INSERT INTO mop_accounts VALUES('".rand(10000,99999)."','".$_POST['name']."','".$_POST['branch']."','".$_POST['accNo']."','".$_POST['mop']."','".date('Y-m-d')."') ");
       if($inst){
         ?>
         <script type="text/javascript">
           location.replace('mop_account.php')
         </script>
        <?php
       }
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
    <div class="form_header">Set Mode of Payment Account</div>
  <form name="form1" id="form1" method="post" >
    <table width="100%" cellspacing="5" cellpadding="5" align="center" class="form-display">
      <tr>
        <td>Account Category </td>
        <td>
          <select name="mop" class="text-input" id="mop">
            <option selected="selected" value="">Select</option>
             <?php
               $select_mop = mysqli_query($connect,"SELECT * FROM mop");
               if(mysqli_num_rows($select_mop)){              
                    while($row_mop = mysqli_fetch_array($select_mop)) {
                      echo '<option value="'.$row_mop[0].'">'.$row_mop[1].'</option>';
                   }        
                }else{
                  echo '<option value="">Not Found</option>'; 
                }
               ?>
              </select></td>
      </tr>
       <tr>
        <td>Branch / Mobile Operator </td>
        <td><input type="text" name="branch" id="branch" class="text-input" /></td>
      </tr>
      <tr>
        <td>Account Name </td>
        <td><input type="text" name="name" id="name" class="text-input" /></td>
      </tr>
      <tr>
        <td>Account No </td>
        <td><input type="text" name="accNo" id="accNo" class="text-input" /></td>
      </tr>
     
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="submit" id="submit" value="Submit" class="button-input" /></td>
      </tr>
    </table>
  </form>
  </div>

<div style="width:80%;margin:18px auto">
  <div style="font-weight:bold;font-size:14px;">Account Details</div>
  <table width="100%" align="center" cellspacing="0" cellpadding="5" class="report_display">
            <tr>
             <td>No</td>
             <td>Account</td>
             <td>Name</td>
             <td>Branch</td>
             <td>Account No</td>
             <td>Cash Balance</td>
             <td></td>
            </tr>
 <?php
  $get_bank = mysqli_query($connect,"SELECT a.id, a.acc_name, a.acc_branch, a.acc_no, m.name FROM mop_accounts a, mop m WHERE m.id=a.mop");   
    if(mysqli_num_rows($get_bank))
    {
      $array = array('125,000,000','259,000','115,000');
      $index = 0;
      while($rw = mysqli_fetch_array($get_bank))
        {
          $count+=1;

          echo '<tr>
                   <td class="table_content">'.$count.'</td>
                   <td class="table_content">'.$rw[4].'</td>
                   <td class="table_content">'.$rw[1].'</td>
                   <td class="table_content">'.$rw[2].'</td>
                   <td class="table_content">'.$rw[3].'</td>
                   <td class="table_content">'.$array[$index].'</td>
                   <td class="table_content">
                      <select name="action" class="text-input" style="width:80px;height:30px;" id="'.$count.'">
                                <option selected="selected" value="">Action</option>
                                <option value="'.$rw[0].'_edit">Edit</option>
                                <option value="'.$rw[0].'_delete">Delete</option>
                            </select></td>
                 </tr>';
         $index+=1;
       } 
      
    } else {
       echo '<tr>
               <td colspan="7" class="table_content">No Result(s) Found</td>
             </tr>';  
    }
?>
   <tr>
       <td colspan="6"><div style="height:90px">&nbsp;</div></td>
   </tr>
 </table>
</div>
</div>

        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>