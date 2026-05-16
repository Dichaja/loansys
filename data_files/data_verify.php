<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

//check_sess(); //check user loggin
if(!$_SESSION['sess_user']){
  $log_usr = $_GET['temp_usr'];
}else{
  check_sess();
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    
   <title><?php echo sys_tab_hdr() ?></title>
   <?php include('link_docs.php') ?>
<script type="text/javascript">

$(function(){

   let usr_sess = $('#temp_usr').val();
   let modal = $('#myModal').html();

   if(usr_sess){
     $('#myModal').css({'display':'block'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')

    setTimeout(function(){
         $('#myModal').html(modal);
         $('.modal-content').toggleClass('modal-small-size');
         get_login();
      },1000);
   }
})

function get_login(){

   $.ajax({
      type: 'POST',
      url: '../data_files/data_src.php',
      data:{
        'get_login':1
      },
      success:function(data){
         $('#display').html(data);
         $('.close').css('display','none');
      }
   })
}

$(document).on('click','#submit',function(e){

   e.preventDefault()
    $.ajax({
        type: 'POST',
        url: '../data_files/page_settings.php',
        data:$('#login_form').serialize(),
        beforeSend:function(){
          $('#myModal_2').css({'display':'block','z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');
        },
        success:function(d){
            if(d=='success'){
              $('#myModal, #myModal_2').css('display','none');
              $('#temp_usr').val('')
            }else{
              $('#myModal_2').css('display','none');
              $('#login_response').html('Wrong User Name or Password. Please Try Again!!!');
            }
        }
    })
})

</script>
</head>

<body>
   <input type="hidden" name="temp_usr" id="temp_usr" value="<?php echo $log_usr ?>" />

<?php 
if($_GET['edit_payment']){

  $id = $_GET['edit_payment'];
  $pay = $_GET['pay_edit'];
  $pay_date = $_GET['pay_date'];
  $loan = $_GET['loan'];

  $upd = mysqli_query($connect,"UPDATE loan_payments SET amount_paid='$pay', pay_date='$pay_date', modify_date='".date('Y-m-d H:i')."' WHERE id='$id' ");
  if(mysqli_affected_rows($connect)){
    $status='success';
     $sql = mysqli_query($connect,"SELECT * FROM loan_payments WHERE id='$id'");
      $r = mysqli_fetch_array($sql);
      $bal = loan_status($connect,$r['loan'],'');
       if($bal > 0)
         $loan_status='01';
       if($bal<=0)
         $loan_status='00';

         mysqli_query($connect,"UPDATE loan_entries SET status='$loan_status' WHERE id='".$r['loan']."' ");

   }
  ?>
<script type="text/javascript">
  window.open('../data_rp/loan_payments.php?id=<?php echo $id ?>&action_msg=<?php echo $status.' '.$bal ?>&loan=<?php echo $loan ?>','_self');
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
              <div style="margin:100px auto;width:50%;">
                <div class="form_header">Payment Adjustment Approvals</div>
                 <?php
                   if($_GET['pay_id']){

                      $id = $_GET['pay_id'];
                      $pay = $_GET['pay_edit'];
                      $date = $_GET['pay_date'];
                      $loan = $_GET['loan'];

                      $approve_tag = '<div style="margin: 15px 0;width:100%;"><a href="data_verify.php?pay_edit='.$pay.'&pay_date='.$date.'&edit_payment='.$id.'&loan='.$loan.'"><span style="border-radius:5px;padding:7px;color:#fff;font-weight:bold;background-color: #fd7e14;">Submit</span></a></div>';
                      ?>
                      <div><h2>Entry Request</h2></div>
                      <div>Pay Date <?php echo date('d/m/Y',strtotime($_GET['pay_date'])) ?></div>
                      <div>Amount <?php echo number_format($_GET['pay_edit']) ?></div>
                     <?php
                        echo $approve_tag;
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