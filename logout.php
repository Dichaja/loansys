<?php
session_start();
    
if(isset($_SESSION['user_type']) && isset($_SESSION['sess_user'])) {
	
   $old_user = $_SESSION['sess_user'];
   $old_type = $_SESSION['user_type'];
 
 //destroys value
    unset($_SESSION['sess_user']);
	unset($_SESSION['user_type']);
	
    if(!empty($old_user) && !empty($old_type))
	{
		session_destroy();
	?>
    <script type="text/javascript">
    	location.replace('index.php')
    </script>
	<?php
	}
	
	
} else {
 ?>
    <script type="text/javascript">
    	location.replace('index.php')
    </script>
 <?php
}
?>