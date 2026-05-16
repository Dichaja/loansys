<?php
require("../xsert/connect.php");
error_reporting(E_ALL ^ E_NOTICE);

if($_POST['edit_client']){

	$id = $_POST['edit_client'];
	$sql = mysqli_query($connect,"SELECT * FROM clients WHERE id='$id'");
	$rw = mysqli_fetch_array($sql);
  
	?>
 <div class="form-wrapper">
 <span class="header">Client Registration Form</span>
  	<form method="post" name="edit-form">
  		<input type="hidden" value="<?php echo $id ?>" name="client_edit" />
      <div class="form-group">
      	<div class="label">Client Names</div>
      	<input type="text" name="names" class="text-input" value="<?php echo $rw[1] ?>" />
      </div>
      <div class="form-group">
      	<div class="label">Date of Birth</div>
      	<input type="text" name="age" class="text-input" placeholder="dd/mm/yyyy" value="<?php echo $rw[7] ?>" id="datetimepicker" />
      </div>
      <div class="form-group">
      	<div class="label">Gender</div>
      	<select name="gender" class="select-input">
      		<?php 
               $array = array('Male','Female');
               foreach ($array as $key) {
               	 if($_POST){
               	 	if($key==$rw[8]){
               	 		echo '<option selected="selected" value="'.$key.'">'.$key.'</option>';
               	 	}else{
                       echo '<option value="'.$key.'">'.$key.'</option>';
               	 	}
               	 }
               }
      		?>
      	</select>
      </div>
      <div class="form-group">
      	<div class="label">Contacts</div>
      	<input type="text" name="contacts" class="text-input"  value="<?php echo $rw[2] ?>" />
      </div>
      <div class="form-group">
      	<div class="label">Email</div>
      	<input type="text" name="email" class="text-input" value="<?php echo $rw[3] ?>" />
      </div>
      <div class="form-group">
      	<div class="label">Residence</div>
      	<input type="text" name="residence" class="text-input" value="<?php echo $rw[4] ?>" />
      </div>
      <div class="form-group">
      	<div class="label">Occupation</div>
      	<input type="text" name="occupy" class="text-input" value="<?php echo $rw[5] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Application Fee</div>
        <input type="text" name="apply" class="text-input" value="<?php echo $rw[10] ?>"/>
      </div>
      <div class="form-group">
      	<button type="submit" name="update" class="button-input">Update</button>
      </div>
     </form>
 </div>
<?php
}

if($_POST['edit_loan']){
  $id = $_POST['edit_loan'];
  $sql = mysqli_query($connect,"SELECT * FROM loan_entries WHERE id='$id' ");
  $rw = mysqli_fetch_array($sql);
  ?>
   <div id="loan_form">
<form method="post" name="form" id="form2">
  <input type="hidden" name="edit_loan" value="<?php echo $id ?>" />
  <span class="header">Loan Lending Form</span>
    <input type="hidden" name="client" id="client_id" />
      <div class="form-group">
        <div class="label">Loan Amount</div>
        <input type="text" name="loan_amount" class="text-input" id="loan_amount" value="<?php echo number_format($rw[1]) ?>" />
      </div>
      <div class="form-group">
        <div class="label">Interest Rate</div>
        <input type="text" name="interest" class="text-input" value="<?php echo $rw[2] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Period</div>
        <input type="text" name="period" class="text-input" value="<?php echo $rw[4] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Specify</div>
        <select name="duration" class="select-input">
          <?php
            $array = array('day','month','year');
            foreach($array as $val){
              if($rw[3]==$val){
                echo '<option value="'.$val.'" selected="selected">'.ucfirst($val).'</option>';
              }else{
                echo '<option value="'.$val.'">'.ucfirst($val).'</option>';
              }
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <div class="label">Date</div>
        <input type="text" name="date" class="text-input" id="datetimepicker" autocomplete="off" value="<?php echo $rw[5] ?>" />
      </div>
      <div class="form-group" id="button">        
        <button type="submit" name="btnSubmit" class="button-input" id="submit_loan">Submit</button>
      </div>
    </form>
</div>
  <?php
}
?>