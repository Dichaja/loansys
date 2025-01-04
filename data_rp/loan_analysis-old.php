<?php
require("../xsert/connect.php");
require("../data_files/sys_function.php");

error_reporting(E_ALL ^ E_NOTICE);

function return_monthly_loans($c,$month,$year){
      $qry = "SELECT * FROM loan_entries WHERE ";
        if($month!=''){
          $qry.= " monthname(date_entry) = '$month' AND ";
         }
          if($year!=''){
            $qry.= " date_format(date_entry,'%Y') = '$year' AND ";
             }
           $qry.=" 1 ";
      $sql = mysqli_query($c,$qry);
      while($rw=mysqli_fetch_array($sql)){
         $loan .= $rw[0].",";
      }
  return $loan;
}

if($_POST['year']){
  $year = $_POST['year'];
}else{
  $year = date("Y");
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<title><?php //echo $title_status ?></title>
<meta charset="UTF-8" />
<!-- linking only css files -->
<link rel="stylesheet" href="http://localhost/loan_sys/data_css/layout.css" />

<!-- linking only javascript files -->
<script type="text/javascript" src="../data_scripts/jquery-2.1.1.min.js"></script>

<script type="text/javascript">
$(function(){
	$("#essential").css("display","none");
	$("#loan").css("display","none");
  $("#search-form").css("display","none");
})
//to check all checkboxes
$(document).on('click','#all',function(){
	$('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
});

//closes dialog box
$(document).on('click','.pop',function(){
 var myModal = $('#myModal');
     myModal.css("display","block");
 var form_display = $("#loan").html();
 var data_pop = $(this).attr("data");
 var index  = data_pop.split("_");
     $("#display").html(form_display);
     $("#client_id").val(index[0]);
});

$(document).on('click','#spanModal',function(){
  $("#myModal").css("display","none");
});

$(document).on('click','#submit_loan',function(event){
	event.preventDefault();
	var essential_form = $("#essential").html();
	$.ajax({
		type:"POST",
		url:"../data_files/data_src.php",
		data:$("#form2").serialize(),
		success:function(response){
           var split = response.split("_");
          if(split[0]==1){
          	$("#action_remark").html("Action Successfull...!!!");
            $("#display").html(essential_form);
            $("#client_id2").val(split[2]);
            $("#loan_id").val(split[1]);                 
          }else{
          	alert("Un-successfull. Something Went Wrong...!!!");
          	$("#myModal").css("display","none");
          }
          $("#form2").trigger("reset");
		}
	});
});

$(document).on('click','#btnSubmit',function(event){
  event.preventDefault();
  $.ajax({
     type:'POST',
     url:'../data_files/data_src.php',
     data:$("#form").serialize(),
     success:function(data){
       if(data==1){
       	  $("#form").trigger('reset');
       	  alert("Successfull...!!!");
       	  location.replace("client_list.php");
       }else{
       	 alert("Un-Successfull. Something Went Wrong..!!!");
       }
     }
  });
});

$(document).on('click','#open_search',function(){
  var usr = $(this).attr("data-usr");
  var form_data = $('#search-form').html();
  
   $("#suggesstion-box").show().slideDown('slow').html(form_data);
    var close = '<img src="../img_file/search.png" width="18px" height="18px" id="close-search" style="cursor:pointer;" />';
    //$("#search_icon").html(close);    
});

$(document).on('click','#close-search',function(){
   $("#suggesstion-box").slideUp('slow').css("display","none");
   var open_search = '<img src="../img_file/search.png" width="18px" height="18px" id="open_search" style="cursor:pointer;" />';
    $("#search_icon").html(open_search);
  });


</script>
</head>
  <body>
  <div class="mainWrapper">
  <div class="top">
  <div class="header_user"><span id="log" style="cursor:pointer">Hello User&nbsp;&nbsp;<img src="../lw_img/logout.png" width="14" height="14" /></span></div>
  <div class="top_header">
    <span class="header_content">Primecom Services</span><br><span style="font-weight:bold;">Loan System</span>
  </div>
  <div  class="topMenu">Home&nbsp;|&nbsp;Back&nbsp;&nbsp;Print</div>
 </div>
 <div class="left-wrapper">
  <div class="left-wrapper-inna">
      <a href="../dash.php" class="mini_link">Home</a>
  </div>
  <div class="left-wrapper-inna">
      <a href="../data_rp/loan_activity.php" class="mini_link">Loan Activity</a>
  </div>
  <div class="left-wrapper-inna">
      <a href="../index.php" class="mini_link">Log Out</a>
  </div>
</div>
<div class="contentWrapper">
  <div class="inna_wrapper">
  <div >
    <div class="report_wrapper">
      <!-- returns seach form elements -->
<div id="search-form">
    <div class="form_element">
      <select name="month" id="month" class="select-input">
        <option selected="selected" value="">--Month--</option>
         <?php 
          $array_month = array('January','Febraury','March','April','June','July','August','September','October','November','December');
          foreach($array_month as $val){
            echo '<option value="'.$val.'">'.$val.'</option>';
          }
        ?>
      </select>
    </div>
     <div class="form_element">
      <input type="text" name="year" id="year" class="text-input" placeholder="Year" />
    </div>
    <div class="form_element">
      <input type="submit" name="search" value="Search" class="button-input-small" />
    </div>
</div>
  	<form name="search-form" id="search-form" method="post" action="">
                    <div style="width:100%;height:40px;margin-bottom:10px;padding:5px;">
                      <span class="header">General Loan Performance - <?php echo $year ?></span>
                        <div style="float:right;width:470px;">
                        <div style="height:40px;width:350px;border:solid 1px #CCC;float:left;background-color:#fff;">
                          <input type="text" name="year" value="" placeholder="Search Year" style="width:90%;height:90%;border:solid 1px #fff;" id="names"  autocomplete="off" />
                          <span  id="search_icon" ><img src="../img_file/search.png" width="18px" height="18px" style="cursor:pointer;" data-usr="<?php  ?>" id="open_search" /></span>
                          <div id="suggesstion-box" class="suggest"></div>
                          <input type="hidden" name="staff" value="" id="staff" />
                         </div>&nbsp;&nbsp;
                         <div style="float:right;"><input type="submit" name="search" value="Search" style="width:90px;height:40px;border:solid 1px #fff;background-color:#CCC; color:#fff;"/>
                        </div>
                      </div>
                    </div>
          </form>
  	<table align="center" cellpadding="4" cellspacing="0" width="80%">
     <?php
     $month_array = array('January','February','March','April','May','June','July','August','September','October','November','December');
     ?>
      <tr style="font-weight:bold;">
        <td class="line" style="color: #145FA7;">Period</td>
        <td class="line" style="color: #145FA7;">Disburments</td>
        <td class="line" style="color: #145FA7;">Expected Interest</td>
        <td class="line" style="color: #145FA7;" colspan="2">Actual Collection</td>
        <td class="line" style="color: #145FA7;">Balance</td>
      <tr>
        <tr>
        <td class="line" style="color: #145FA7;"></td>
        <td class="line" style="color: #145FA7;"></td>
        <td class="line" style="color: #145FA7;"></td>
        <td class="line" style="color: #145FA7;">Interest</td>
        <td class="line" style="color: #145FA7;">Princpal</td>
        <td class="line" style="color: #145FA7;"></td>
      <tr>
       <?php
        // returns months in a year
        foreach ($month_array as $value) {
          $ids=return_monthly_loans($connect,$value,$year);
          $split = explode(",", $ids);  
          $loan_principal=0; 
          $payments = 0;
          $bal=0;
          $agg_bal=0;
          $tot_int=0;
          foreach ($split as $key ) {
                   $qry = mysqli_query($connect,"SELECT amount_paid FROM  loan_payments WHERE loan='$key'");
                      $acc_principal=0;
                      $principal = 0;
                      
                      while($result=mysqli_fetch_array($qry)){
                            $payments += $result[0];                       
                         }                      
                      }
         ?>
          <tr>
             <td class="line" width="10%" style="font-weight:bold" ><?php echo $value ?></td>
             <td class="line"><?php 
              foreach ($split as $key ) {
                   $qry = mysqli_query($connect,"SELECT loan_amount, interest, period FROM  loan_entries WHERE id='$key'");
                      $acc_principal=0;
                      $principal = 0;
                      
                      while($result=mysqli_fetch_array($qry)){
                            $loan_principal += $result[0];                       
                         }                      
                      }
                    echo number_format($loan_principal);
              ?></td>
             <td class="line"><?php
              $interest=0;                         
              foreach ($split as $key ) {
                   $qry = mysqli_query($connect,"SELECT loan_amount, interest, period FROM  loan_entries WHERE id='$key'");
                      $acc_principal=0;
                      $principal = 0;
                      $loan=0;
                      while($result=mysqli_fetch_array($qry)){
                            $principal = $result[0]/$result[2];
                            $int = $result[1];
                            $loan=$result[0];
                            for($x=0;$x<$result[2];$x++){
                               $acc_principal+=$principal;
                                $interest += ($int/100)*(($loan - $acc_principal)+$principal);
                            }                            
                         }                      
                      }
                  echo number_format(round($interest,0));
             ?></td>
             <td class="line">
              <?php if($payments>0){
                  foreach ($split as $key ) {
                    $start_date="";
                   $qry = mysqli_query($connect,"SELECT loan_amount, interest, period, date_entry, duration, id, status FROM  loan_entries WHERE id='$key'");
                     
                      while($rw=mysqli_fetch_array($qry)){
                        $pay_int=0;
                        if($rw[6]=='00'){
                             $status_period = $rw[2];                              
                              for($i=0;$i<$status_period;$i++){
                                  $pay_int += ($rw[1]/100)*$rw[0]; 
                                } 
                            $tot_int += $pay_int;
                        }
                        else{
                        $status_period = $rw[2]-return_period($rw[3],$rw[4]);
                         if($rw[2]==$status_period){
                                   $bal=0;
                                }else{
                                    for($i=0;$i<return_period($rw[3],$rw[4]);$i++){
                                        if($i==0){
                                           $start_date=$rw[3];
                                           }
                                         $final = endCycle($start_date, '1', $rw[4]);
                                         
                                         $sql_date = "SELECT * FROM loan_payments WHERE loan='$key' AND ";
                                             if($rw[4]=='day'){
                                              $sql_date .= " pay_date='".date("Y-m-d",strtotime($final))."' AND ";
                                            }
                                            if($rw[4]=='month'){
                                              $sql_date .= " monthname(pay_date)='".date("F",strtotime($final))."' AND ";
                                                }
                                                $sql_date .= " 1 ";
                                            $qry = mysqli_query($connect,$sql_date);
                                            while($result=mysqli_fetch_array($qry)){
                                              $tot_int += ($rw[1]/100)*$rw[0]; 
                                              $amt_paid = $result[1];
                                            }
                                        //echo date("d-m-Y",strtotime($final)).' '.$rw[4].'<br/>';
                                      $start_date=$final;
                                     } 
                                }
                         
                      }                     
                  }
                }
             echo number_format($tot_int);
             } ?>
           </td>
             <td class="line"><?php echo number_format($payments-$tot_int) ?></td>
             <td class="line"><?php
                 foreach ($split as $key ) {
                   $qry = mysqli_query($connect,"SELECT loan_amount, interest, period, date_entry, duration, id, status FROM  loan_entries WHERE id='$key'");
      
                      while($rw=mysqli_fetch_array($qry)){
                        $acc_int2=0;
                        if($rw[6]=='00'){
                          $status_period = $rw[2];
                              
                              for($i=0;$i<$status_period;$i++){
                                  $acc_int2 += ($rw[1]/100)*$rw[0]; 
                                } 
                            $bal = (($acc_int2+$rw[0])-loan_pay($connect,$key));
 
                           }else{
                              $status_period = $rw[2]-return_period($rw[3],$rw[4]);
                               if($status_period<0){
                                    for($i=0;$i<return_period($rw[3],$rw[4]);$i++){
                                          $acc_int2 += ($rw[1]/100)*$rw[0]; 
                                } 
                                    $bal = (($acc_int2+$rw[0])-loan_pay($connect,$key));
                                }else{
                                    for($i=0;$i<$rw[2];$i++){
                                    $acc_int2 += ($rw[1]/100)*$rw[0]; 
                                 }  
                                    $bal = (($acc_int2+$rw[0])-loan_pay($connect,$key));
                                 } 
                              }
                          }
                        }
                    $agg_bal += $bal;
                  echo number_format($agg_bal);  

         ?></td>
           </tr>
          <?php
          }
        ?>
      <tr style="font-weight:bold;">
        <td class="line" style="color: #145FA7;">Total</td>
        <td class="line" style="color: #145FA7;"></td>
        <td class="line" style="color: #145FA7;"></td>
        <td class="line" style="color: #145FA7;"></td>
        <td class="line" style="color: #145FA7;"></td>
        <td class="line" style="color: #145FA7;"></td>
      <tr>
  	</table>
    <div style="float:left;height:250px;width:100%">&nbsp;</div>
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