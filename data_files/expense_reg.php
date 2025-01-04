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
$(document).on('change','.action',function(){
  var sel_val = $(this).val();
  var id = $(this).attr('id');
  var split = sel_val.split('_');

 if(split[1]=='Edit'){

  $('#myModal').css('display','block');
     $.ajax({
        type:'POST',
        url:'src_data.php',
        data:{
          'edit_expense_item':split[0]
        },beforeSend:function(){
              $('#display').html('<div style="margin:auto;margin-top:100px;margin-bottom:100px;width:10%;"><img src="../img_data/loading.gif" /></div>');
            },success:function(d){
              $('#display').html(d)
         }
     })
 }

 if(split[1]=='Delete'){
  var usr = $('#user').html();
    if(confirm("Do You Want to Delete...?")){
      $.ajax({
         type:'POST',
         url:'src_file.php',
         data:{
          'check_expense':split[0]
         },success:function(d){
           if(d==1){
            if(usr!='admin'){
              alert('Already in Use. Please Contact Administrator...!!!');
            }else{
             if(confirm("In Use. Do You Wish to Continue..?")){
               del_expense(split[0],d);
             }else{
               return false;
             }
            }
           }else{
             del_expense(split[0],d);
           }
         }
      });
    }else{
      return false;
    }
 }

$(".close").attr('select',id); 
  
});

$(document).on('click','.close',function(){
    var select_id = $(this).attr('select');
    var select_data = $("#"+select_id).html();
    var split=select_id.split("_");//returns id index
    $("#myModal").css("display","none");
    $("#"+select_id).html(select_data);
});

$(document).on('change','#category',function(){
  
  var chg_val = $(this).val();
  if(chg_val=='add')
     $('#chg_element').html('<input type="text" value="" name="add_cat" class="text-input" placeholder="Add New" />');
})

function del_expense(id,index){
  $.ajax({
     type:'POST',
     url:'src_file.php',
     data:{
      'del_expense':id,
      'index':index
     },
     success:function(d){
       location.replace("add_expense.php?action_msg="+d);
     }
  })
}
</script>
</head>

<body>

 <?php
    if($_POST['submit']){

      $expense = mysqli_real_escape_string($connect,$_POST['expense']);
      $desc = mysqli_real_escape_string($connect,$_POST['desc']);
      $date = $_POST['date'];
      $category = $_POST['category'];
      $add_cat = $_POST['add_cat'];

    if($add_cat){

       $new_id = rand(1000,9999);
       $inst = mysqli_query($connect,"INSERT INTO expense_cat VALUES('$new_id','$add_cat','".date('Y-m-d')."')");
       if($inst){
         $category = $new_id;
       }
    }

    if($expense){
       if(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM expense_items WHERE item='$expense' "))){
           $status = 'exist';
         }else{
              $inst = mysqli_query($connect,"INSERT INTO expense_items VALUES('".rand(1000,9999)."','$expense','$desc','$date','$category') ");
            if($inst){
                $status='success';
             }else{
                $status='err';
                $response = mysqli_error($connect);
           }
        }
    }
      ?>
       <script type="text/javascript">
         location.replace("expense_reg.php?action_msg=<?php echo $status ?>&response=<?php echo $response ?>");
       </script>
      <?php
    }

  if($_POST['update']){

    $id = $_POST['expense_id'];
    $expense = mysqli_real_escape_string($connect,$_POST['expense']);
    $desc = mysqli_real_escape_string($connect,$_POST['desc']);
    $date = $_POST['date']; 
    $category = $_POST['category'];
    $add_cat = $_POST['add_cat'];

    if($add_cat){

       $new_id = rand(1000,9999);
       $inst = mysqli_query($connect,"INSERT INTO expense_cat VALUES('$new_id','$add_cat','".date('Y-m-d')."')");
       if($inst){
         $category = $new_id;
       }
    }

      $upd = mysqli_query($connect,"UPDATE expense_items SET item='$expense', item_desc='$desc', reg_date='$date', category='$category' WHERE id='$id' ");
      if($upd){
        $status='success';
      }else{
        $status='err';
        $response=mysqli_error($connect);
      }
    ?>
       <script type="text/javascript">
         location.replace("expense_reg.php?action_msg=<?php echo $status ?>&response=<?php echo $response ?>");
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
  
            <!-- form header -->
  <div class="form_wrap_min">
    <!--form header-->
    <?php if($_GET){ include('action_msg.php'); } ?>
      <div class="form_header">Add Expense Item</div>
        <form name="form1" id="form1" method="post" action="" autocomplete="off"> 
         <input type="hidden" name="expense_id" id="expense_id" value="" />
           <div class="form-group">
                <div class="label">Expense Category</div>
                <div id="chg_element">
                  <select name="category" class="text-input" id="category">
                    <option selected="selected">Select</option>
                    <?php
                      $sql = mysqli_query($connect,"SELECT * FROM expense_cat ORDER BY category ASC");
                      while($r = mysqli_fetch_array($sql)){
                        if($r[1]){
                          echo '<option value="'.$r[0].'">'.$r[1].'</option>';
                        }
                      }
                    ?>
                    <option value="add">Add New</option>
                  </select>
                </div>
              </div>
            <div class="form-group">
                <div class="label">Expense Name</div>
                  <input type="text" class="text-input" name="expense" id="expense"  /> 
            </div>
            <div class="form-group">
                <div class="label">Description</div>
                  <textarea class="text-input" name="desc" id="desc" style="height:100px"></textarea>
            </div>
            <div class="form-group">
                <div class="label">Date</div>
                <input type="text" class="text-input" name="date" id="picker" value="<?php echo date('Y/m/d H:i') ?>" required="required" />
            </div>             
            <div class="form-group">
              <input type="submit" name="submit"  value="Submit" class="button-input" id="submit" />
            </div>        
       </form>
     </div>

  <div style="margin:50px auto;width:80%;">
     <div class="report_header"><span>List of Expenses</span><span id="print_rpt"><img src="../img_file/print-icon.svg" width="20" height="20" /></span></div>
    <table width="100%" cellspacing="0" cellpadding="5" class="report_display">
      <tr>
         <td class="bottom_line">No</td>
         <td class="bottom_line">Category</td>
         <td class="bottom_line">
                 <div class="grid-4">
                   <div>Expense</div>
                   <div>Description</div>
                   <div>Date</div>
                   <div></div>
                 </div>
              </td>
      </tr>
    <?php

      $q = " SELECT * FROM expense_cat ORDER BY category ASC ";
        $query = mysqli_query($connect,$q);
         while($rw = mysqli_fetch_array($query)){
          $count += 1;
          if($rw[1]){
           ?>
           <tr>
              <td class="bottom_line" valign="top"><?php echo $count ?></td>
              <td class="bottom_line" valign="top"><?php echo $rw[1] ?></td>
              <td class="bottom_line">
               <?php
                $q = " SELECT * FROM expense_items i WHERE category='$rw[0]' AND ";
                   $q .= " 1 ORDER BY i.item, i.reg_date ASC ";
                $sql = mysqli_query($connect,$q);

                   if(mysqli_num_rows($sql)){
                       while($r=mysqli_fetch_array($sql)){ ?>
                 <div class="grid-4">
                   <div><?php echo $r[1] ?></div>
                   <div><?php echo $r[2] ?></div>
                   <div><?php echo date('d-m-Y',strtotime($r[3])) ?></div>
                   <div>
                    <select name="option" style="height:30px; width:90px;" id="action_<?php echo $count ?>" class="text-input">
                      <option value="" selected="selected">Action</option>
                      <option value="<?php echo $r[0] ?>_Edit">Edit</option>
                      <option value="<?php echo $r[0] ?>_Delete">Delete</option>
                  </select></div>
                 </div>
                <?php 
                  }
                }
              ?>
              </td>
           </tr>
           <?php
           }
         }
      ?>
          <tr>
            <td colspan="4"><div style="height:100px;">&nbsp;</div></td>
          </tr>
      </table>
    </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>