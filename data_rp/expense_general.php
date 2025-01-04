<?php
session_start();
require("../xsert/connect.php");

if(!isset($_SESSION['sess_usr'])){
  ?>
   <script type="text/javascript">
    location.replace("../index.php");
   </script>
  <?php
}
error_reporting(E_ALL ^ E_NOTICE);

function return_monthly_expense($c,$e,$m,$y){
  $expense=0;
   $qry = "SELECT e.amount, e.pay_date FROM expense x, expense_entries e WHERE x.id=e.expense AND ";
      if($e != ''){
         $qry .= "x.expense_acc = '$e' AND ";
        }
        if($y != ''){
         $qry .= "date_format(e.pay_date,'%Y') = '$y' AND ";
        } 
          $qry .= " 1 ";

   $sql = mysqli_query($c,$qry);
   if(mysqli_num_rows($sql)){
     while($rw=mysqli_fetch_array($sql)){
       if(date('M',strtotime($rw[1]))==$m){
         $expense += $rw[0];
       }
     }
   }else{
     $expense = 0;
   }
   return $expense;
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<title><?php //echo $title_status ?></title>
<meta charset="UTF-8" />
<!-- linking only css files -->
<link rel="stylesheet" href="../data_css/layout.css" />

<!-- linking only javascript files -->
<script type="text/javascript" src="../data_scripts/jquery-2.1.1.min.js"></script>

<script type="text/javascript">
$(function(){
	$("#essential").css("display","none");
	$("#loan").css("display","none");
  $("#search-form").css('display','none')
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
    $("#search_icon").html(close);    
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
  <div class="header_user"><span id="log" style="cursor:pointer">Hello User&nbsp;&nbsp;<img src="../img_file/logout.png" width="14" height="14" /></span></div>
  <div class="top_header">
    <span style="font-weight:bold;">Loan System</span>
  </div>
  <div  class="topMenu">
    <div style="float:left;">Prime Commerical Services</div>
    <a href="../dash.php" class="mini_link">Home</a>&nbsp;|&nbsp;<span class="mini_link" id="print_file" style="cursor:pointer">Print</span></div>
 </div>
<div class="left-wrapper">
  <div class="left-wrapper-inna">
      <a href="../dash.php" class="mini_link">Home</a>
  </div>
  <div class="left-wrapper-inna">
      <a href="../data_rp/expense_report.php" class="mini_link">Expense Overview</a>
  </div>
  <div class="left-wrapper-inna">
      <a href="../index.php" class="mini_link">Log Out</a>
  </div>
</div>
<div class="contentWrapper">
  <div class="inna_wrapper">
  <div >
    <div class="report_wrapper">
  	<span class="header"></span>
    <form name="search-form" id="top" method="post" action="">
                    <div style="width:100%;height:40px;margin-bottom:10px;padding:5px;">
                      <span class="header">Annual Expense Report - <?php echo date("Y") ?></span>
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
  	<table align="center" cellpadding="4" cellspacing="0" >
     <?php
     $month_array = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
     ?>
     <tr>
       <td class="line"></td>
       <td class="line">&nbsp;</td>
       <?php
             // returns months in a year
             foreach ($month_array as $value) {
                ?>
                <td class="line" width="10%" align="right"><b><?php echo $value ?></b></td>
                <?php
              }
             ?>
        <td class="line" align="right"><b>Total</b></td>
     </tr>
     <tr>
       <td class="line" colspan="2"><b>Expenses</b></td>
       <td colspan="<?php echo count($month_array) ?>" class="line"></td>
       <td class="line"></td>
     </tr>
     <?php
     if($_POST){
      $year = $_POST['year'];

     }else{
      $year = date('Y');
     }
     $sql=mysqli_query($connect,"SELECT * FROM expense GROUP BY category");
      if(mysqli_num_rows($sql)){
         $gen_total=0;
        while($rw=mysqli_fetch_array($sql)){
          $total=0;
          ?>
           <tr>
             <td class="line" valign="top" style="color:#145FA7;"><?php echo $rw[1] ?></td>
             <td class="line" style="color:#145FA7;"><?php echo strtolower($rw[2]) ?></td>
             <?php
             // returns months in a year
             foreach ($month_array as $value) {
                ?>
                <td class="line" align="right"><?php 
                if(return_monthly_expense($connect,$rw[1],$value,$year)==0){
                  echo '-';
                }else{
                  echo number_format(return_monthly_expense($connect,$rw[1],$value,$year));
                } ?></td>
                <?php
                $total += return_monthly_expense($connect,$rw[1],$value,$year);
              }
             ?>
             <td class="line" align="right"><b><?php echo number_format($total) ?></b>
           </tr>
          <?php
          $gen_total += $total;
        }
      }
    ?>
    <tr>
       <td class="line" colspan="2"><b>Total</b></td>
       <?php
             // returns months in a year
             foreach ($month_array as $value) {
                ?>
                <td class="line" width="10%" align="right"><b><?php echo number_format(return_monthly_expense($connect,$rw[1],$value,$year)) ?></b></td>
                <?php
              }
             ?>
        <td class="line"><b><?php echo number_format($gen_total) ?></b></td>
     </tr>
  	</table> 
    <div style="float:left;height:250px;width:100%">&nbsp;</div>
    </div>
  </div>
</div>
</div>
</div>
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
  <?php
   require("../footer.php");
  ?>
  </body>
</html>