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
    <?php include('../data_files/link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">

$(document).on('change','select[name="sel_action"]',function(){

   var chg_val = $(this).val();
   var split = chg_val.split('_');

    if(split[0]=='edit'){
      $.ajax({
         type: 'POST',
         url: '../data_files/form_edit.php',
         data:{
           'edit_staff': split[1]
         },beforeSend:function(){
           $('#myModal_2').css({'display':'block'},{'z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
        success:function(d){
           $('#myModal').css('display','block');
           $('#myModal_2').css('display','none');
            $('.modal-content').toggleClass('modal-small-size');
            $('#display').html(d);
        }
      })
    }

    if(split[0]=='delete'){
      if(confirm('Do You Wish To Delete...?')){
         $.ajax({
           type:'POST',
           url: '../data_files/data_src.php',
           data:{
             'check_staff': split[1]
           },
           success: function(d){
             if(d=='yes_del'){
               del('staff',split[1]);
             }else{
               if(confirm("Already In Use. Do Wish to Continue...!!!")){
                 if(user=='data_clerk')
                   alert('Denied. Please Contact Admin...!')
                  else
                    del('staff',split[1]);
              }else{
                 return false;
              }
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
           url: '../data_files/data_src.php',
           data:{
             'del_id': index,
             'del_tab':tab
           },
           beforeSend:function(){
             $('#myModal_2').css({'display':'block'},{'z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
            },
           success: function(d){
             location.replace('staff_list.php?action_msg='+d);
           }
  })
}

$(document).on('click','#submit',function(e){
   
  e.preventDefault();
  let content, load_content;

    $.ajax({
       type: 'POST',
       url: '../data_files/post_data.php',
       data: $('#staff').serialize(),
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

if($_POST['search']){
  $staff = $_POST['staff_names'];
  $job = $_POST['job'];
  $staff_id = $_POST['staff_id'];
}

$start = 0;
$limit = 40;

$qry = "SELECT s.id, s.first_name, s.last_name, s.contacts, s.gender, s.email, s.residance, b.branch_name, j.job_title, s.reg_date FROM staff s, branches b, staff_job j WHERE s.branch_id = b.id AND  j.id = s.job AND ";
      if($staff_id)
        $qry .= " s.id = '$staff_id' AND ";
       else if($staff)
            $qry .= " CONCAT(s.first_name,' ',s.last_name) LIKE  '%$staff%' OR CONCAT(s.last_name,' ', s.first_name) LIKE '%$staff%' AND ";
         if($job)
             $qry .= " s.residence = '".$address."' AND ";
           if($branch)
              $qry .= " b.id = '$branch' AND ";
          $qry_tot = $qry." 1 ";
        $qry .= " 1 ORDER BY s.first_name, s.last_name ASC LIMIT $start, $limit ";

  $sql_tot = mysqli_query($connect,$qry_tot); //return total staff 
?>  
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <div style="width:95%;margin: auto;">
  <div class="report_wrap">
    <?php if($_GET){ include('action_msg.php'); } ?>
        <div id="header_wrap">
          <div id="header_tpl"><?php echo po_address($connect) ?></div>
        </div>               
        <div class="report_header" style="align-items: center;">
                <span>Staff Report <?php echo $search ?></span>
                <div style="text-align: right;">
          <!--Search Wrapper -->
           <form name="form1" method="post" action="staff_list.php" id="form1">
            <input type="hidden" name="post_search" value="1" />
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
               <div style="height:40px;border:solid 1px #CCC;background-color:#fff;width:300px;" id="drop_wrapper">
                  <input type="text" class="search_text" name="name_search" placeholder="Search Staff" id="name_search" autocomplete="off" data-src="staff" />
                      <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px" />
                       <input type="hidden" name="staff_id" value="" id="data_id" />
                      <div id="drop-box" class="drop_down drop_large_size"></div>
                   </div> 
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
     </div>
  </div>
  <div class="report_wrap">
  <div style="font-size:12px;font-weight: normal;display: grid; grid-template-columns: 1fr 1fr;">
    <span>Entries : <?php echo mysqli_num_rows($sql_tot) ?></span>
    <span style="display: grid; grid-template-columns: 1fr 1fr;">
              <div style="width:100%;text-align:right;font-size:14px;">
                <span style="display:inline-block;text-align: right;font-size: 12px;font-weight: normal;">
                  <span id="get_report" class="print_layout">Generate PDF</span>
                  <span class="print_layout"><a href="../export/export_clients.php" style="text-decoration: none; color:#000; ">Export Data</a></span>
                  </span>
                  <span id="print_rpt">
                    <span>Print</span>
                    <span><img src="../img_file/print-icon.svg" width="20" height="20"></span>
                  </span>
                </span>
              </div>
              <div style="width:100%;text-align:right;font-size:14px;display:none;">
                     <span style="padding:5px;">Page <?php echo $page ?> <b>of</b> <?php echo $lastpage ?></span>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$next&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">Next</a>"; ?></span>
                     <?php 
                        if($page!=1) {
                            $prev = $page - 1; ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$prev&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">Back</a>"; ?></span>
                     <?php } ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=1&limit=$total_pages&limit=$limit&month=$month&year=$year&gender=$gender&branch=$branch&address=$address\">View All</a>"; ?></span>
                  </div>
        </span>
    </div>
</div>
  <form name="form_list" id="form_list" method="post" action="client_list.php">
     <table align="center" cellpadding="5" cellspacing="0" class="report_display" width="100%">
      <tr>
        <td>No</td>
        <td>Staff Names</td>
        <td>Job Title</td>
        <td>Gender</td>
        <td>Contacts</td>
        <td>Email</td>
        <td>Residance</td>
        <td>Registration Date</td>
        <td>Branch</td>
        <td></td>
      </tr>
      <?php
         $sql = mysqli_query($connect, $qry);
          if(mysqli_num_rows($sql)){
            while ($r = mysqli_fetch_array($sql)){
              $count += 1;
              if($r['first_name'] OR $r['last_name']){
              ?>
               <tr>
                  <td><?php echo $count ?></td>
                  <td><?php echo $r['first_name'].' '.$r['last_name'] ?></td>
                  <td><?php echo $r['job_title'] ?></td>
                  <td><?php echo $r['gender'] ?></td>
                  <td><?php echo $r['contacts'] ?></td>
                  <td><?php echo $r['email'] ?></td>
                  <td><?php echo $r['residance'] ?></td>
                   <td><?php echo date('d/m/y', strtotime($r['reg_date'])) ?></td>
                  <td><?php echo $r['branch_name'] ?></td>
                  <td align="right">
                    <select name="sel_action" id="action_<?php echo $count ?>" class="text-input" style="width:80px;">
                       <option value="">Action</option>
                       <option value="edit_<?php echo $r[0] ?>">Edit</option>
                       <option value="delete_<?php echo $r[0] ?>">Delete</option>
                    </select></td>
                </tr>
              <?php
            }
          }
        }else{
            ?>
          <tr>
            <td colspan="7"><div style="height:90px">No Result(s) Found</div></td>
           </tr> 
          <?php
         }
      ?>
     </table>
    </form>
   </div>
  </div>
    <?php echo footer_sec(); //footer section ?>
  </div>
  </body>
</html>