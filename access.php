<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("xsert/connect.php");
?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
  	 <title>Prime Commercial Services :: loan System</title>
  	 <meta charset="UTF-8" />
<!-- linking only css files -->
<link rel="stylesheet" href="data_css/layout.css" />

<!-- linking only javascript files -->
<script type="text/javascript" src="data_scripts/jquery-2.1.1.min.js"></script>

<script type="text/javascript">
//to check all checkboxes
$(document).on('click','#all',function(){
	$('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
});

$(function(){
  //$("#user_reports").css("display","none");
});

$(document).on('click','#reports',function(){

  var link  = $(this).html();
  var id = $(this).attr('id');
   
  if(link=='View Reports'){
     $('#reports').html('Access Forms');
     $("#user_access").fadeOut();
     $("#user_reports").css("display","block");
  }

  if(link=='Access Forms'){ 
    $('#reports').html('View Reports');
     $("#user_reports").fadeOut();
     $("#user_access").css("display","block");
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
  
</div>
<div class="contentWrapper">
  <div class="inna_wrapper">
  <div class="home_wrapper" style="height:90%">    
  </div>
      <div style="float:left;display:block;margin-top:10px;height:400px;width:99%;">
          <div style="margin-left:auto;margin-right:auto;border:solid 1px #000;margin-top:100px;width:60%;">
            <div style="float:left;width:10%;border:solid 1px #000;"><img src="img_file/close.png" height="25px" /></div>
            <div style="float:right;width:88%;border:solid 1px #000;"><span style="font-size:20px;color:#F00;">User Session Expired...!!!</span></div>
          </div>
          <div style="display:block;border:solid 1px #000;margin-top:50px;margin-left:auto;margin-right:auto;width:60%"><a href="index.php">Login</a></div>
      </div>      
</div>
</div>
</div>
</div>
</div>
  <?php
   require("footer.php");
  ?>
  </body>
</html>