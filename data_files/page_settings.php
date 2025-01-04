<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once("../xsert/connect.php");
date_default_timezone_set('Africa/Nairobi');

/*
Builds up page header and structual layout
*/

if($_POST['user']){

 $usr = $_POST['user'];
 $pwd = $_POST['pass'];

$sql  = mysqli_query($connect,"SELECT * FROM user_log WHERE usr_name='$usr' AND pass_wrd = md5('$pwd') ");

 if(mysqli_num_rows($sql)){
    while($r = mysqli_fetch_array($sql)){
  
   //if(hash('crc32',$r[0])==$_GET['user_id']){

     $_SESSION['sess_user'] = $r[2];
     $_SESSION['session_id'] = $r[0];
     $_SESSION['user_type'] = $r[4];
     $_SESSION['user_branch'] = $r[8];

      //update last login in status
        mysqli_query($connect,"UPDATE user_log SET log_date='".date('Y-m-d H:i')."' WHERE id='".$_SESSION['session_id']."' ");
        if($_SESSION['user_type']=='admin' OR $_SESSION['user_type']=='director')
           $_SESSION['general_user'] = $_SESSION['user_type'];
      //}
    }
    echo 'success';
  }else{
    echo 'err'.mysqli_error($connect);
  }
}

function check_sess(){

  if(!$_SESSION['user_type'] AND !$_SESSION['sess_usr']){
     ?>
     <script type="text/javascript">
      location.replace('../index.php?sess_status=01');
     </script>
  <?php
  }
}

function sys_tab_hdr(){
  return 'Loans Management Evaluation System';
}


function tp_hdr(){ // returns main header wrapper + logo
?>
<input type="hidden" name="user_type" id="user_type" value="<?php echo $_SESSION['user_type'] ?>" >
<!-- The Modal -->
<div id="myModal_2" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close" id="spanModal" select="">&times;</span>
      <span id="display_2"></span>
  </div>
</div>

<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close" id="spanModal" select="">&times;</span> 
      <span id="display"></span>
  </div>
</div>

<div id="loading_wrap">
  <div style="width:50%;margin:auto;text-align:center;" id="loading">
      <span><img src="../img_file/loading.gif" width="25px" heigth="25px"/></span>
      <span style="display: inline-block;width:100%;">Please Wait...</span>
  </div>
</div>

<div id="success_wrap">
  <div style="width:50%;margin:auto;text-align:center;">
      <span class="success_icon"></span>
      <span style="display: inline-block;width:100%;margin-top: 10px">Successful.!</span>
  </div>
</div>

<div id="warning_wrap">
  <div style="width:50%;margin:auto;text-align:center;">
      <span><img src="../img_file/warning-96.png" width="25px" heigth="25px"/></span>
      <span style="display: inline-block;width:100%;margin-top: 10px;color:#F00;" id="warning_msg">Denied!. Member Still Has Running Loan.</span>
  </div>
</div>

<div id="error_wrap">
  <div style="width:50%;margin:auto;text-align:center;">
      <span class="error_icon"></span>
      <span style="display: inline-block;width:100%;margin-top: 10px">Something Went Wrong. Please Try Again!</span>
  </div>
</div>

<!--Page Header, Menu-->
 	<header id="masthead">
 		<section id="top_header_wrap">
 			<div class="column_gap">
 				<!-- Wrap left to contain social site icons-->
  				<div class="div_column">
  				  <div class="column_gap_left">
  					
  				  </div>
  				</div>

  				<!-- Wrap Right to contain search and login icons -->
  				 <div class="div_column">
  				  <div class="column_gap_right">
  				 	<div class="login_wrap">
  				 		<?php if ($_SESSION['sess_user']){
  				 			echo '<div class="login"><p><a href="../logout.php" style="color:#fff;"><div class="login">User, '.$_SESSION['sess_user'].'</a></p></div>';
  				 		}else{
  				 		  echo '<div class="login"><p><a href="../index.php" style="color:#fff;">Login</a></p></div>'; } ?>
  				 	</div>  				 
  				    <div class="search_block"></div>
  				  </div>
  				</div>
 			</div>
 		</section>

 		<section class="top_logo_wrap">
 			<div class="column_gap">
            	<!--Logo & Text Section-->
              <div class="div_logo_column">
            	<div class="logo_2">
            		<a class="man_logo" href="../admin/home_index.php">
            			<div class="man_logo_img"><i class="ti ti-package"></i></div>
            			<div class="man_logo_txt">Loani-Ware System</div>
            		</a>
            	</div>
               </div>
            	<!-- Menu Navigation -->
            	<div class="div_menu_column">
            	  <div class="menu_nav">
            		<button class="sm_menu_toggle"><i class="ti ti-menu"></i></button>
            		<a class="sm_menu_toggle_close" href="#"><i class="ti ti-close"></i></a>
            		<!--List Menu Items-->
            	   <div class="sm_menu" id="sm_menu">
            	  	  <nav id="menu_wrap">
            	  	  	<!--System Nav Menu-->
            	  	  </nav>
            	  </div>
            	</div>
              </div>
            </div>	
 		</section>
 	</header>
<?php
}

function side_menu_content(){
  ?>
<!--Top Background Cover-->
 	<section class="top_backgd_cover bg_cover_dim">
 		<div class="bg_overlay"></div>
 	</section>
   
   <section class="body_content">
 	   <div class="column_gap div_column_home">
 		    <div class="row">

              <!--   Side-Left  -->
              <div class="col-lg-3 man_sidebar_col">
                <div class="main-sidebar">
                  <section class="side-content"><?php include("user_menu.php");  ?></section>
                </div>
 		      </div>
<?php
}

function footer_sec(){
 ?>
</div>
    </div>
   </section>
<?php } 

function report_header($c){

  $sql = mysqli_query($c,"SELECT * FROM header_tpl");
  $r = mysqli_fetch_array($sql);
  $header_text = $r[2].'<br>'.$r[6].'<br>Tel:'.$r[3].'<br>Website '.$r[4].'<br>Email: '.$r[5];
	?>
	<div id="header"  style="float:left;width:100%;margin-bottom:10px;border-bottom:solid 5px #01994D;">
    <div style="float:left;width:35%;"><img src="../photo_upd/<?php echo $r[8] ?>" width="100" height="100"></div>
    <div style="width:60%;float:right;text-align:right;"><span style="font-size:18px;"><?php echo $r[1] ?></span><br><span style="font-size:14px">
     <?php echo $header_text ?><br>Date Generated: <?php echo date('jS F Y') ?></span>
      </div>
   </div>
<?php
}
?>