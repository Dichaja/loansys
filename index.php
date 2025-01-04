<?php
session_start();

error_reporting(E_ALL ^ E_NOTICE);

require_once("xsert/connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <title>Loani-Ware System</title>
   <meta charset="utf-8">

<link rel="stylesheet" href="data_css/login_theme.css" media="screen" type="text/css" />
<script type="text/javascript" src="data_scripts/jquery-2.1.1.min.js"></script>
<script type="text/javascript">


$(document).on('click', '#button', async function(event) {

event.preventDefault();
var usr = $('#login-name').val();

if (usr) {
  try {
    // Show the loading spinner
    $('#myModal').
    css({'display':'block','z-index':'6'}).
    html('<div class="modal-spin-wrap"><div class="modal-text"></div><div class="modal-img-spin"><img src="img_file/loading.gif" /></div></div>');

    const response = await $.ajax({
      type: 'POST',
      url: 'data_files/page_settings.php',
      data:$('#post_login').serialize()
    });

    if (response === 'success') {
      window.location.href = "data_files/dash.php";
    } else {
      $('#post_response').html('<div style="width:100%;padding:5px;color:#F00;font-family:Segoe UI">Wrong User or Password. Please Try Again.</div>')
      $('#typ_pwd').val('').focus();
    }
  } catch (error) {
    console.error('Error:', error);
  } finally {
    // Hide the loading spinner
    $('#myModal').css('display', 'none');
  }
} else {
  $('#login-name').css('border', 'solid 1px #F00').focus();
}
})

</script>
<style type="text/css">

.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 3;
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: scroll; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    padding-bottom:30px;
    margin-bottom:10px;
    border-radius:10px;
}

.modal-spin-wrap{
    margin:100px auto;width:50%;text-align:center;
}

.modal-text{
    width:100%;color:#fff;font-size:1.4em;
}

.modal-img-spin{
    width:5%;margin:auto;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}
</style>
</head>
<body>

<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close" id="spanModal" select="">&times;</span>
      <span id="display"></span>
  </div>
</div>
 <form name="form1" method="post" action="" id="post_login">
    <div class="login">
      <div class="login-screen">
                <div class="app-title">
          <h1><img src="img_file/logo.PNG" width="220" height="140"><br>
          Login</h1>
        </div>
        <div class="login-form">
          <div class="control-group">
            <input name="user" type="text" class="login-field" id="login-name" value="" />
            <label class="login-field-icon fui-user" for="login-name"></label>
          </div>
          <div class="control-group">
            <input name="pass" type="password" class="login-field" id="login-pass" value="" />
            <label class="login-field-icon fui-lock" for="login-pass"></label>
          </div>
          <input type="submit" name="button" id="button" value="LOGIN" class="styled-button-5">
          <span id="post_response"></span>
          <?php
            if(isset($_GET['sess_status'])){
                echo ' <div style="width:100%;padding:5px;color:#F00;font-family:Segoe UI">User Session Expired. Please Login.</div>';
            }
          ?>
        </div>
      </div>
    </div>
  </form>
</body>
</html>