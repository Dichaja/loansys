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

$(document).on('keyup','#staff_names',function() {

  var  search = $(this).val();
  var exp = new RegExp(search, "i");
  var results='',count=0, combo;

if(search){
  $.ajax({
    type:'POST',
    url:'../data_files/name_list.php',
    data:{
      'search':search,
      'name_cat': 'staff'
    },
    beforeSend:function(){
      $("#drop-box").slideDown().html('<div style="margin:auto;max-height250px;margin-bottom:50px;margin-top:50px;width:40%;"><img src="../img_file/loading.gif" /></div>');
    },success:function(data){
      $.each(data,function(key,value){
      combo = value.first_name+' '+value.last_name;
      combo2 = value.last_name+' '+value.first_name;
       if(value.first_name.search(exp) != -1 || value.last_name.search(exp) != -1 || combo.search(exp) != -1 || combo2.search(exp) != -1){
        count+=1;
         results += '<div class="list_items" data="'+value.id+'">'+value.first_name+' '+value.last_name+'</div>';
        }
     })

     if(results)
      $("#drop-box").html(results);
     else 
      $("#drop-box").slideUp();
    }
  })
} 
else 
  $("#drop-box").slideUp();
})

$(document).on('click','.list_items',function(){

   var id = $(this).attr('data');
   var txt = $(this).html();

   $('#staff_names').val(txt).css('text-transform','capitalize');
   $('#staff_id').val(id);
   $('#drop-box').slideUp();
 })

$(document).on('change','select[name="option[]"]',function(){
  
   var val = $(this).val();
   var split = val.split('_');
   var modal = $('#myModal').html();

   if(split[1]=='delete'){
    
      if(confirm("Do You Want to Delete User...?")){
         $.ajax({
           type: 'POST',
           url: 'data_src.php',
           data:{
             'del_id': split[0],
             'del_tab' : 'user_log'
           },
          beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
          success:function(d){
               $('#myModal').html(modal);
                $('.modal-content').toggleClass('modal-min-size')
                if(d=='success')
                   content = $('#success_wrap').html();
                else
                   content = $('#error_wrap').html();           
            $('#display').html(content);
         }
      })
    }else{
      return false;
    }
  }

  if(split[1]=='restore'){
     
      $.ajax({
         type: 'POST',
         url: 'data_src.php',
         data: {
           'restore_usr': split[0]
         },
         beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
          success:function(d){
               $('#myModal').html(modal);
                $('.modal-content').toggleClass('modal-min-size')
                if(d=='success'){
                   content = $('#success_wrap').html();
                   $('#display').html(content);
                }
                else
                   $('#myModal').css('display','none')           
            
         }
      })
  }

  if(split[1]=='disable'){
     
      $.ajax({
         type: 'POST',
         url: 'data_src.php',
         data: {
           'disable_usr': split[0]
         },
         beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
          success:function(d){
               $('#myModal').html(modal);
                $('.modal-content').toggleClass('modal-min-size')
                if(d=='success'){
                   content = $('#success_wrap').html();
                   $('#display').html(content);
                }
                else
                   $('#myModal').css('display','none')           
            
         }
      })
  }

})

$(document).on('click','#add_user', function(){

    var modal = $('#myModal').html();

    $.ajax({
         type: 'POST',
         url: 'data_src.php',
         data: {
           'add_user': 1
         },
         beforeSend:function(){
          $('#myModal').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
         },
          success:function(d){
               $('#myModal').html(modal);
                $('.modal-content').toggleClass('modal-min-size')
                  $('#display').html(d);
         }
      })     
})

$(document).on('click', 'input[name="submit"]', function(e){

    e.preventDefault();
       $.ajax({
          url:'post_data.php',
          type:'POST',
          data:$('#form1').serialize(),
          beforeSend:function(){
          $('#myModal_2').css({'display':'block','z-index':'8'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
          },
          success: function(d){
            $('#myModal_2').css('display','none');
              if(d=='mis-match'){
                $('#mis-match').html('<span style="font-size:12px;color:#F00;">Password Mis-Match</span>');
                $('#pwd2').css('border','solid 1px #F00');
              }else if(d=='success'){
                 window.open('add_user.php?action_msg='+d,'_self');
            }
        }
    })
})

</script>
</head>

<body>

<!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            
             <?php if($_GET){ include('action_msg.php'); } ?>

    <div style="width:90%;margin: 30px auto;">
      <div class="report_header">
         <span>User Activity Log</span>
          <span class="grid-2">
          <span id="add_user" style="margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;font-size:12px;text-align: center;">Add User</span>
         <div id="print_rpt"><img src="../img_file/print-icon.svg" width="20" height="20"></div>
         </span>
     </div>

       <table width="100%" cellpadding="5" cellspacing="0" align="center" class="report_display">        
         <tr>
           <td>No</td>
           <td>Staff</td>
           <td>Email</td>
           <td>User Name</td>
           <td>User Type</td>
           <td>User Branch</td>
           <td>Last Login</td>
           <td>User Status</td>
           <td>Date Set</td>
           <td></td>
         </tr>     
      <?php
      $q = "SELECT u.id, s.first_name, s.last_name, s.email, u.usr_name, u.usr_type, b.branch_name, u.log_date, u.action_status, u.date_set FROM  user_log u, staff s, branches b WHERE u.staff = s.id AND u.user_branch = b.id "; 
        $sql = mysqli_query($connect,$q);
     $no = 0;

     if(mysqli_num_rows($sql)){

      while($row=mysqli_fetch_array($sql)){
          ?>
          <tr id="status_<?php echo $x+=1 ?>">
           <td><?php echo $no+=1 ?></td>
           <td><?php echo $row['first_name'].' '.$row['last_name'] ?></td>
           <td><?php echo $row['email'] ?></td>
           <td><?php echo $row['usr_name'] ?></td>
           <td><?php echo $row['usr_type'] ?></td>
           <td><?php echo $row['branch_name'] ?></td>
           <td><?php 
                if(!$row['log_date'])
                     echo '00-00-0000 00:00';
                  else
                     echo date('d-m-Y H:i',strtotime($row['log_date'])); ?></td>
           <td><span style="color:#F00;"><?php 
                 if($row[8]=='01')
                      echo 'Active';
                   if($row[8]=='02')
                         echo 'Disabled';
                   ?></span></td>
           <td><?php echo date('d-m-Y H:i:s',strtotime($row['date_set'])) ?></td>
           <td align="right">
           <select name="option[]" style="height:30px; width:90px;" id="action_<?php echo $count ?>" class="text-input">
              <option value="" selected="selected">Action</option>
              <option value="<?php echo $row[0].'_edit' ?>">Edit</option>
              <?php
                if($row[8]=='01')
                  echo '<option value="'.$row[0].'_disable">Disable</option>';
                if($row[8]=='02')
                  echo '<option value="'.$row[0].'_restore">Restore</option>';
                if($row['usr_type']!='admin')
                   echo '<option value="'.$row[0].'_delete">Delete</option>';
               ?>
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