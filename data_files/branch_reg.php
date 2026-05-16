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

$(document).on('change','select[name="sel_action"]',function(){

   var chg_val = $(this).val();
   var split = chg_val.split('_');

    if(split[0]=='edit'){
      $.ajax({
         type: 'POST',
         url: 'form_edit.php',
         data:{
           'edit_branch': split[1]
         },beforeSend:function(){
           $('#myModal_2').css({'display':'block'},{'z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
        success:function(d){
           $('#myModal').css('display','block');
            $('.modal-content').toggleClass('modal-small-size');
            $('#display').html(d);
        }
      })
    }

    if(split[0]=='delete'){
      if(confirm('Do You Wish To Delete...?')){
         $.ajax({
           type:'POST',
           url: 'data_src.php',
           data:{
             'branch_check': split[1]
           },
           success: function(d){
             if(d=='yes_del'){
               del('branches',split[1]);
             }else{
               alert('Denied. Already in Use...!!!');
             }
           }
         })
      }else{
        return false;
      }
    }
})

function del(tab, index){

      $.ajax({
           type:'POST',
           url: 'data_src.php',
           data:{
             'del_id': index,
             'del_tab':tab
           },
           beforeSend:function(){
             $('#myModal_2').css({'display':'block'},{'z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
            },
           success: function(d){
             location.replace('branch_reg?action_msg='+d);
           }
  })
}

$(document).on('click','#submit',function(e){
   
  e.preventDefault();
  let content, load_content;

    $.ajax({
       type: 'POST',
       url: 'post_data.php',
       data: $('#branch_edit').serialize(),
       beforeSend:function(){
         load_content = $('#loading_wrap').html();
         $('.modal-content').removeClass('modal-small-size').toggleClass('modal-min-size');
         $('#display').html(load_content)
       },
       success:function(d){
         if(d)
          content = $('#success_wrap').html();          
            $('#display').html(content);
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

if($_POST['branch_name']){
  
  $branch = $_POST['branch_name'];
  $addr = $_POST['address'];
  $email = $_POST['email'];
  $contacts = $_POST['contacts'];

  $inst = mysqli_query($connect,"INSERT INTO branches VALUES('".rand(1000,9999)."','$branch','$email','$conacts','$addr','".date('Y-m-d')."')");
    if($inst){
      $status = 'success';   
    }else{
      $status = 'err';
    }
?>
<script type="text/javascript">
  location.replace('branch_reg.php?action_msg=<?php echo $status ?>');
</script>
<?php
}

      ?>
      <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <div class="form_wrap_min">
             <?php if($_GET){ include('action_msg.php'); } ?>
              <div class="form_header">Add Branch</div>
                <form method="post" name="form" id="branch_reg" method="post">
                      <div class="form-group">
                              <div class="label">Branch Name</div>
                              <input type="text" name="branch_name" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Address</div>
                              <input type="text" name="address" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Contacts</div>
                              <input type="text" name="contacts" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                              <div class="label">Email</div>
                              <input type="text" name="email" class="text-input" autocomplete="off" />
                     </div>
                     <div class="form-group">
                       <button type="submit" name="btnSubmit" class="button-input">Submit</button>
                     </div>
                  </form>
            </div>

            <div style="margin:50px auto;width:80%;">
               <div class="report_header"><span>Branch Details</span><span id="print_rpt"><img src="../img_file/print-icon.svg" width="20" height="20" /></span></div>
               <table width="100%" cellspacing="0" cellpadding="5" class="report_display">
                <tr>
                   <td>No</td>
                   <td>Branch</td>
                   <td>Email</td>
                   <td>Contact</td>
                   <td>Address</td>
                   <td></td>
                </tr>
            <?php
              $sql = mysqli_query($connect,"SELECT * FROM branches");
              $count = 0;
               if(mysqli_num_rows($sql)){
                  while($r = mysqli_fetch_array($sql)){
                    
                    if($r['branch_name']){
                      $count += 1;
                    ?>
                    <tr>
                       <td><?php echo $count ?></td>
                       <td><?php echo $r['branch_name'] ?></td>
                       <td><?php echo $r['contact_email'] ?></td>
                       <td><?php echo $r['contact_phone'] ?></td>
                       <td><?php echo $r['address'] ?></td>
                       <td>
                         <select name="sel_action" id="action_<?php echo $count?>" class="text-input" style="height: 35px;width:80px;">
                           <option value="" selected="selected">Action</option>
                           <option value="edit_<?php echo $r['id'] ?>">Edit</option>
                           <option value="delete_<?php echo $r['id'] ?>">Delete</option>
                         </select>
                       </td>
                    </tr>
                  <?php
                }
             }
          }
        ?>
            </div>
        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>