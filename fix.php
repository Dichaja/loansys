<html>
<head>
<title>Prime Loans - Fix Errors </title>
<script type="text/javascript" src="data_scripts/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
$(function(){
  $.ajax({
    type:'POST',
    url:'data_files/load_sql.php',
    data:{
      'sql':1
    },
      beforeSend: function(){
      $("#content").css({"background":"url(img_file/LoaderIcon.gif) no-repeat","height":"25px","width":"25px"});
      $('#result').css('font-weight','bold');
     },
     success:function(data){
      $("#content").css({"background":"url(img_file/icon-success.png) no-repeat"});
      $("#result").html(data).css('font-weight','normal');
     }
  });
})
</script>
</head>
<body style="background-color:#eee;">
<?php

?>
<div style="border:solid 1px #33C;border-radius:5px;width:50%;margin-left:auto;margin-right:auto;margin-top:150px;font-family:'Segoe UI';background-color:#fff;">
  <div style="width:60%;text-align:center;margin-left:auto;margin-right:auto;color:#F00;border-bottom:solid 1px #33C;"><h2>System Diagnosis</h2></div>
  <div style="height:100px;padding:10px;" >
  	<div style="width:25px;margin-left:auto;margin-right:auto;" id="content"></div>
  	<div style="display:block;margin-top:10px;text-align:center;" id="result">Fixing in Progress...</div>
 </div>
</div>
</body>
</html>