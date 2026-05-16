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

$(function(){
   let total = $('#total_staff').attr('data');
     $('#return_total').html(total)
})

$(document).on('change','select[name="sel_action"]',function(){

   var chg_val = $(this).val();
   var split = chg_val.split('_');


   function postData(index,dataVal,postUrl){
      $.ajax({
         type: 'POST',
         url: postUrl,
         data:{[dataVal]: index },
         beforeSend:function(){
           $('#myModal_2').css({'display':'block'},{'z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
        },
        success:function(d){
          $('#myModal').css('display','block');
          $('#myModal_2').css('display','none');
         let content='';  
          if(d=='success'){
            content = $('#success_wrap').html();
          } else if(d=='err'){
            content = $('#success_wrap').html();
          }else{
            content=d;
          }
          $('#display').html(content);
        }
      })
   }

    if(split[0]=='edit'){
      postData(split[1],'edit_staff','../data_files/form_edit.php');       
       $('.modal-content').toggleClass('modal-small-size');
    }

    if(split[0]=='disable'){
      postData(split[1],'disable_staff','../data_files/data_src.php');
        $('.modal-content').toggleClass('modal-min-size');
    }

    if(split[0]=='restore'){
      postData(split[1],'restore_staff','../data_files/data_src.php');
        $('.modal-content').toggleClass('modal-min-size');
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
             location.replace('staff_performance.php?action_msg='+d);
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

$(document).on('click','#add_officer',async function(){

   try{

     $('#myModal_2').css({'display':'block'},{'z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');

     const data = await $.ajax({
        url: '../data_files/form_edit.php',
        type: 'POST',
        data:{'add_officer':'add' }
     });

       if(data){
           $('#myModal').css('display','block');
           $('.modal-content').toggleClass('modal-small-size');
           $('#display').html(data);
        }

   }catch(error){
     console.log(error);
   }finally{
      $('#myModal_2').css('display','none');
   }

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

$qry = "SELECT s.id, s.first_name, s.last_name, s.contacts, s.gender, s.email, s.residance, b.branch_name, j.job_title, e.id as 'loan_id', s.status FROM staff s LEFT JOIN branches b ON s.branch_id = b.id LEFT JOIN staff_job j ON j.id = s.job LEFT JOIN loan_entries e ON s.id=e.loan_officer AND ";
      if($staff_id)
        $qry .= " s.id = '$staff_id' AND ";
       else if($staff)
            $qry .= " CONCAT(s.first_name,' ',s.last_name) LIKE  '%$staff%' OR CONCAT(s.last_name,' ', s.first_name) LIKE '%$staff%' AND ";
         if($job)
             $qry .= " s.residence = '".$address."' AND ";
           if($branch)
              $qry .= " b.id = '$branch' AND ";
          $qry_tot = $qry." 1 ";
        $qry .= "1 WHERE s.job='0001' GROUP BY s.id ORDER BY s.first_name, s.last_name ASC LIMIT $start, $limit ";
  $sql_tot = mysqli_query($connect,$qry); //return total staff 
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
  <div class="grid-2" style="display: grid;">
    <div></div>
    <div style="text-align: right;">
      <!--Search Wrapper -->
          <form name="form1" method="post" action="client_list.php" id="form1">
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
                <div style="height:40px;border:solid 1px #CCC;background-color:#fff">
                   <input type="text" name="staff_names" value="" placeholder="Search Staff" style="width:85%;height:90%;border:solid 1px #fff;" id="names" autocomplete="off">
                        <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px">
                          <input type="hidden" name="staff_id" value="" id="staff_id">
                      <div id="drop-box" class="drop_down drop_large_size"></div>
                     </div> 
                    <div><input type="submit" name="search" value="Search" class="button_search"></div>
                  </div>                      
                </form>
      </div>
  </div>
  <div class="report_header">
    <?php if($_GET){ include('action_msg.php'); } ?>
    <span>Loan Officer Performance 
        <span style="font-size: 13px;font-weight: normal;">Total: <span id="return_total"></span></span>
    </span>
    <span class="grid-2">
      <span id="add_officer" style="margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;font-size:12px;text-align: center;">Add Officer</span>
       <div id="print_rpt"><img src="../img_file/print-icon.svg" width="20" height="20"></div>
     </span>
  </div>
  <form name="form_list" id="form_list" method="post" action="client_list.php">
     <table align="center" cellpadding="5" cellspacing="0" class="report_display" width="100%">
      <tr>
        <td>No</td>
        <td>Staff Names</td>
        <td>Gender</td>
        <td>Contacts</td>
        <td>Branch</td>
        <td>Loan Entries</td>
        <td>Non-Defaulters</td>
        <td>Defaulters</td>
        <td>Work Rate</td>
        <td>Status</td>
        <td></td>
      </tr>
      <?php
       $count=0;
       $status='Active';
         $sql = mysqli_query($connect, $qry);
          if(mysqli_num_rows($sql)){
            while ($r = mysqli_fetch_array($sql)){

                $count += 1;
                $loan_entries = 0;
                $defaulters=0;
                $nonDefaulters=0;
                $rate=0;
                $loanStatus='';

                  $status = ($r['status']=='01') ? 'Active' : 'Disabled';

                $query = mysqli_query($connect,"SELECT * FROM loan_entries WHERE loan_officer='".$r['id']."' ");
                  if(mysqli_num_rows($query)){
                    while($row = mysqli_fetch_array($query)){
                        $loan_entries+=1;
                         if($row['status']=='00')
                           $nonDefaulters+=1;
                         if($row['status']=='02')
                           $defaulters+=1;
                    }
                  }else{
                    $loanStatus='01';
                  }

                      //Return Branch Name Initials
                        $split = explode(' ',$r['branch_name']);
                        $branch_init = '';
                         foreach($split as $key){
                          $branch_init .= substr($key,0,1);
                         } 
              ?>
               <tr>
                  <td><?php echo $count ?></td>
                  <td><?php echo $r['first_name'].' '.$r['last_name'] ?></td>
                  <td><?php echo $r['gender'] ?></td>
                  <td><?php echo $r['contacts'] ?></td>
                  <td><?php echo $branch_init ?></td>
                  <td><?php echo $loan_entries ?></td>
                  <td><?php echo $nonDefaulters ?></td>
                  <td><?php echo $defaulters ?></td>
                  <td><?php 
                         if(!$loanStatus)
                            $rate = round($nonDefaulters / $loan_entries,2)*100;

                          if($rate>=80)
                            echo  '<span style="color:#F00;">'.$rate.'%</span>';
                          else if($rate>45)
                            echo  '<span style="color:orange;">'.$rate.'%</span>';
                          else if($rate<=40)
                            echo  '<span style="color:green;">'.$rate.'%</span>';
                          
                    ?></td>
                  <td><?php echo '<span style="font-weight:bold;font-size:11px">' . $status . '</span>' ?></td>
                  <td align="right">
                    <select name="sel_action" id="action_<?php echo $count ?>" class="text-input" style="width:80px;">
                       <option value="">Action</option>
                       <option value="edit_<?php echo $r[0] ?>">Edit</option>
                       <?php
                         if($r['status']=='01'){?>
                          <option value="disable_<?php echo $r[0] ?>">Disable</option>
                         <?php } else {?>
                          <option value="restore_<?php echo $r[0] ?>">Restore</option>
                         <?php } ?>
                       <option value="delete_<?php echo $r[0] ?>">Delete</option>
                    </select></td>
                </tr>
              <?php
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
     <span id="total_staff" data="<?php echo $count ?>"></span>
    </form>
   </div>
  </div>
    <?php echo footer_sec(); //footer section ?>
  </div>
  </body>
</html>