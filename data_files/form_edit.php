<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once('../xsert/connect.php');
require_once('../data_files/sys_function.php');

if($_POST['edit_loan_payment']){
  
  $id = $_POST['edit_loan_payment'];
   $sql = mysqli_query($connect,"SELECT p.amount_paid, p.pay_date, l.status FROM loan_payments p, loan_entries l WHERE l.id = p.loan AND p.id = '$id' ");
     $r = mysqli_fetch_array($sql);
 ?>
<div style="width:80%;margin:15px auto;">
        <div class="form_header">Edit Loan Payment</div>
        <form method="post" name="form" id="loan_pay_edit" method="post">
            <input type="hidden" name="loan_id" value="<?php echo $id ?>" />
            <input type="hidden" name="data_manage" value="edit" />
            <div class="form-group">
                <div class="label">Loan Amount</div>
                 <input type="text" id="pay" name="edit_pay" class="text-input" autocomplete="off" value="<?php echo number_format($r['amount_paid']) ?>" />
           </div>
           <div class="form-group">
                <div class="label">Pay Date</div>
                 <input type="text" id="datetimepicker" name="pay_date" class="text-input" autocomplete="off" value="<?php echo $r['pay_date'] ?>" />
           </div>
           <?php
             if($r['status']=='00'){
              ?>
              <div class="form-group">
                <div class="label">Reason For Adjustment</div>
                 <textarea name="edit_reason" class="text-input" id="edit_reason" style="height:100px;"></textarea>
              </div>
            <?php
             }
           ?>
           <div class="form-group">
               <button type="submit" name="btnSubmit" class="button-input" id="submit">Submit</button>
          </div>
      </form>
  </div>
<?php
}

if($_POST['confirm_del']){
    
    $id = $_POST['confirm_del'];
  ?>
<div style="width:80%;margin:15px auto;">
        <div class="form_header">Delete Loan Payment</div>
        <form method="post" name="form" id="del_pay" method="post">
            <input type="hidden" name="loan_id" value="<?php echo $id ?>" />
            <input type="hidden" name="data_manage" value="delete" />
              <div class="form-group">
                <div class="label">Reason For Deleting</div>
                 <textarea name="reason" class="text-input" id="edit_reason" style="height:100px;"></textarea>
              </div>
           <div class="form-group">
               <button type="submit" name="btnSubmit" class="button-input" id="delete">Submit</button>
          </div>
      </form>
  </div>
<?php
}

if($_POST['edit_branch']){
  
  $id = $_POST['edit_branch'];
  $sql = mysqli_query($connect,"SELECT * FROM branches WHERE id = '$id' ");
  $r = mysqli_fetch_array($sql);

 ?>
<div style="width:80%;margin:15px auto;">
        <div class="form_header">Edit Branch</div>
                <form method="post" name="form" id="branch_edit" method="post">
                	<input type="hidden" name="edit_branch" value="<?php echo $id ?>" />
                      <div class="form-group">
                              <div class="label">Branch Name</div>
                              <input type="text" name="branch_name" class="text-input" autocomplete="off" value="<?php echo $r['branch_name'] ?>" />
                     </div>
                     <div class="form-group">
                              <div class="label">Address</div>
                              <textarea name="address" class="text-input" style="height:90px;"><?php echo $r['address'] ?></textarea>
                     </div>
                     <div class="form-group">
                              <div class="label">Contacts</div>
                              <input type="text" name="contacts" class="text-input" autocomplete="off" value="<?php echo $r['contact_phone'] ?>" />
                     </div>
                     <div class="form-group">
                              <div class="label">Email</div>
                              <input type="text" name="email" class="text-input" autocomplete="off" value="<?php echo $r['contact_email'] ?>" />
                     </div>
                     <div class="form-group">
                          <button type="submit" name="btnSubmit" class="button-input" id="submit">Submit</button>
                        </div>
                  </form>
            </div>
<?php
}

if($_POST['edit_mem']){

	$id = $_POST['edit_mem'];

	$sql = mysqli_query($connect,"SELECT * FROM clients WHERE id='$id' ");
	 $r = mysqli_fetch_array($sql);
	?>
     <div style="margin:15px auto;">
            <div class="form_header">Edit Member</div>
                <form method="post" name="form" id="edit_mem" method="post" enctype="multipart/form-data" >
                  <input type="hidden" name="member_id" value="<?php echo $r['id']?>" />
                      <div class="form-group">
                       <div class="label">Member Id <span id="err_msg" style="color:#F00;"></span></div>
                         <input type="text" id="edit_id" name="edit_id" class="text-input" autocomplete="off" value="<?php echo $r['data_id'] ?>" data="" />
                     </div>
                     <div class="form-group">
                       <div style="display:grid; grid-template-columns: repeat(2, 1fr);gap:10px;">
                          <div>
                              <div class="label">First Name</div>
                              <input type="text" name="first_name" class="text-input" autocomplete="off" value="<?php echo $r['first_name'] ?>" />
                           </div>
                           <div>
                              <div class="label">Last Name</div>
                                <input type="text" name="last_name" class="text-input" autocomplete="off" value="<?php echo $r['last_name'] ?>" />
                            </div>
                        </div>
                       <div class="form-group">
                          <div class="label">Gender</div>
                             <select name="gender" class="text-input">
                                 <?php
                                   $gender = array('Male','Female','');
                                    foreach ($gender as $key => $value) {
                                    	if($value == $r['gender'])
                                    		echo '<option value="'.$value.'" selected="selected">'.$value.'</option>';
                                    	else if($value=='NULL')
                                    		echo '<option value='.$value.' selected="selected">'.$value.'</option>';
                                    	else
                                    		echo '<option value='.$value.'>'.$value.'</option>';
                                    }
                                 ?>
                              </select>
                       </div>
                       <div class="form-group">
                           <div class="label">Contacts</div>
                           <input type="text" name="contacts" class="text-input" value="<?php echo $r['contacts'] ?>" />
                       </div>
                       <div class="form-group">
                           <div class="label">Email</div>
                           <input type="text" name="email" class="text-input" value="<?php echo $r['email'] ?>" />
                       </div>
                       <div class="form-group">
                           <div class="label">Residence / Address</div>
                           <input type="text" name="residence" class="text-input" value="<?php echo $r['residance'] ?>" />
                       </div>
                       <div class="form-group">
                          <div class="label">Business Name</div>
                          <input type="text" name="occupy" class="text-input" value="<?php echo $r['business_name'] ?>" />
                        </div>
                        <div class="form-group">
                           <div class="label">City</div>
                           <input type="text" name="city" class="text-input" value="<?php echo $r['city'] ?>" />
                       </div>
                          <div class="form-group">
                           <div class="label">Branch</div>
                             <select name="branch_details" class="text-input">
                               <option value="00010" selected="">Select</option>
                               <?php
                                  $sql = mysqli_query($connect,"SELECT * FROM branches");
                                   if(mysqli_num_rows($sql)){
                                     while($rw = mysqli_fetch_array($sql)){
                                      if($rw[1]){
                                      	if($r['branch_id']==$rw[0])
                                          echo '<option value="'.$rw['id'].'" selected="selected">'.$rw['branch_name'].'</option>';
                                        else
                                          echo '<option value="'.$rw['id'].'">'.$rw['branch_name'].'</option>';
                                      }
                                    }
                                 }
                               ?>
                             </select>
                       </div>
                       <div class="form-group">
                        <div class="label">Photo</div>
                         <input type="hidden" name="photo_dir" value="<?php echo $r['photo_dir'] ?>" />
                          <div class="file-wrapper">
                            <div class="upload-btn-wrapper">
                              <button class="btn upload-file font-weight-500">
                                <span class="upload-btn">
                                    <i class="fas fa-cloud-upload-alt d-block font-50 pb-2"></i>
                                      Click Here to Browse folders
                                  </span>
                                 <span class="upload-select-button" id="blankFile">
                                       Supports JPG, GIF and PNG
                                  </span>
                                  <span class="success">
                                       <i class="far fa-check-circle text-success"></i>
                                   </span>
                               </button>
                           <input type="file" name="img_file" id="img_file" value="" />
                           </div>
                         </div>
                        </div>
                        <div class="form-group">
                          <button type="submit" name="btnSubmit" class="button-input" id="submit">Submit</button>
                        </div>
                  </form>
            </div>
<?php
}

if($_POST['edit_staff']){
 
 $id = $_POST['edit_staff'];
 $sql = mysqli_query($connect,"SELECT * FROM staff WHERE id='$id' ");
 $r = mysqli_fetch_array($sql);

 ?>
 <div style="width:85%;margin:15px auto;">
               <!--form header-->
                   <div class="form_header">Edit Staff Details</div>
                     <form method="post" name="form" id="staff">
                     	<input type="hidden" name="edit_staff" value="<?php echo $id ?>" />
                           <div class="form-group" style="margin: auto;width:98%;">
                                <div class="label">Staff No</div>
                                <input type="text" name="edit_staffNo" class="text-input" value="<?php echo $r['id'] ?>" />
                           </div>
                            <div class="form-group">
                       <div style="display:grid; grid-template-columns: repeat(2, 1fr);gap:10px;margin: auto;width:98%;">
                          <div>
                              <div class="label">First Name</div>
                              <input type="text" name="first_name" class="text-input" autocomplete="off" value="<?php echo $r['first_name'] ?>" />
                           </div>
                           <div>
                              <div class="label">Last Name</div>
                                <input type="text" name="last_name" class="text-input" autocomplete="off" value="<?php echo $r['last_name'] ?>" />
                            </div>
                        </div>
                           <div class="form-group">
                                <div class="label">Gender</div>
                                 <select name="gender" class="text-input">
                                   <?php
                                   $gender = array('Male','Female','');
                                    foreach ($gender as $key => $value) {
                                    	if($value == $r['gender'])
                                    		echo '<option value="'.$value.'" selected="selected">'.$value.'</option>';
                                    	else if($value=='NULL')
                                    		echo '<option value='.$value.' selected="selected">'.$value.'</option>';
                                    	else
                                    		echo '<option value='.$value.'>'.$value.'</option>';
                                    }
                                 ?>
                                 </select>
                            </div>
                            <div class="form-group">
                                <div class="label">Contacts</div>
                                <input type="text" name="contacts" class="text-input" value="<?php echo $r['contacts'] ?>" />
                            </div>
                            <div class="form-group">
                                 <div class="label">Email</div>
                                 <input type="text" name="email" class="text-input" value="<?php echo $r['email'] ?>" />
                             </div>
                             <div class="form-group">
                                 <div class="label">Residance</div>
                                 <input type="text" name="residance" class="text-input" value="<?php echo $r['residance'] ?>" />
                              </div>
                              <div class="form-group">
                              <div class="label">Job Title</div>
                               <span id="title_row">
                                  <select name="job_title" class="text-input" id="job_title">
                                     <?php
                                        $default_option='';
                                         $sql = mysqli_query($connect,"SELECT * FROM staff_job ORDER BY job_title ASC");
                                           if(mysqli_num_rows($sql)){
                                             while ($row = mysqli_fetch_array($sql)) {
                                              if($row['job_title']){ // returns on job title wit values
                                                if($row['id']==$r['job']){
                                                  echo '<option value="'.$row['id'].'" selected="selected">'.$row['job_title'].'</option>';
                                                } else {
                                                  $default_option = '01';
                                                  echo '<option value="'.$row['id'].'">'.$row['job_title'].'</option>';
                                                }
                                               }
                                             }
                                           }
                                           
                                        /*if($default_option)
                                          echo '<option selected="selected" value="">Select</option>';*/
                                      ?>
                                      <option value="new">Add New</option>
                                  </select>
                                </span>
                              </div>
                              <div class="form-group">
                           <div class="label">Branch</div>
                             <select name="branch_details" class="text-input">
                               <option value="00010" selected="">Select</option>
                               <?php
                                  $sql = mysqli_query($connect,"SELECT * FROM branches");
                                   if(mysqli_num_rows($sql)){
                                     while($rw = mysqli_fetch_array($sql)){
                                      if($rw[1]){
                                      	if($r['branch_id']==$rw[0])
                                          echo '<option value="'.$rw['id'].'" selected="selected">'.$rw['branch_name'].'</option>';
                                        else
                                          echo '<option value="'.$rw['id'].'">'.$rw['branch_name'].'</option>';
                                      }
                                    }
                                 }
                               ?>
                             </select>
                       </div>
                              <div class="form-group">
                                 <div class="label">Next of Kin</div>
                                 <input type="text" name="nok" class="text-input" value="<?php echo $r['nok'] ?>" />
                              </div>
                              <div class="form-group">
                                <div class="label">Next of Kin - Contacts</div>
                                <input type="text" name="nok_contacts" class="text-input" value="<?php echo $r['nok_contacts'] ?>" />
                              </div>
                              <div class="form-group">
                                <button type="submit" name="btnSubmit" class="button-input" id="submit">Submit</button>
                             </div>
                  </form>
           </div>
<?php
}

if(isset($_POST['add_officer'])){
 
?>
 <div style="width:85%;margin:15px auto;">
               <!--form header-->
                   <div class="form_header">Add Loan Office</div>
                     <form method="post" name="form" id="staff">
                        <input type="hidden" name="edit_staff" value="<?php echo $id ?>" />
                           <div class="form-group" style="margin: auto;width:98%;">
                                <div class="label">Staff No</div>
                                <input type="text" name="staff_no" class="text-input" value="<?php echo $r['id'] ?>" />
                           </div>
                            <div class="form-group">
                       <div class="grid-2" style="margin: auto;width:98%;">
                          <div>
                              <div class="label">First Name</div>
                              <input type="text" name="first_name" class="text-input" autocomplete="off" value="<?php echo $r['first_name'] ?>" />
                           </div>
                           <div>
                              <div class="label">Last Name</div>
                                <input type="text" name="last_name" class="text-input" autocomplete="off" value="<?php echo $r['last_name'] ?>" />
                            </div>
                        </div>
                           <div class="form-group">
                                <div class="label">Gender</div>
                                 <select name="gender" class="text-input">
                                    <option value='' selected="selected">Select</option>
                                   <?php
                                   $gender = array('Male','Female');
                                    foreach ($gender as $key => $value) {
                                      echo '<option value='.$value.'>'.$value.'</option>';
                                    }
                                 ?>
                                 </select>
                            </div>
                            <div class="form-group">
                                <div class="label">Contacts</div>
                                <input type="text" name="contacts" class="text-input" value="" />
                            </div>
                            <div class="form-group">
                                 <div class="label">Email</div>
                                 <input type="text" name="email" class="text-input" value="" />
                             </div>
                             <div class="form-group">
                                 <div class="label">Residance</div>
                                 <input type="text" name="residance" class="text-input" value="" />
                              </div>
                              <div class="form-group">
                              <div class="label">Job Title</div>
                               <span id="title_row">
                                  <select name="job_title" class="text-input" id="job_title">
                                     <?php
                                        $default_option='';
                                         $sql = mysqli_query($connect,"SELECT * FROM staff_job WHERE id='0001' ");
                                           if(mysqli_num_rows($sql)){
                                             while ($row = mysqli_fetch_array($sql)) {
                                                  echo '<option value="'.$row['id'].'" selected="selected">'.$row['job_title'].'</option>';
                                                }
                                           }
                                      ?>
                                  </select>
                                </span>
                              </div>
                              <div class="form-group">
                           <div class="label">Branch</div>
                             <select name="branch_details" class="text-input">
                               <option value="00010" selected="">Select</option>
                               <?php
                                  $sql = mysqli_query($connect,"SELECT * FROM branches");
                                   if(mysqli_num_rows($sql)){
                                     while($rw = mysqli_fetch_array($sql)){
                                      if($rw[1]){
                                        if($r['branch_id']==$rw[0])
                                          echo '<option value="'.$rw['id'].'" selected="selected">'.$rw['branch_name'].'</option>';
                                        else
                                          echo '<option value="'.$rw['id'].'">'.$rw['branch_name'].'</option>';
                                      }
                                    }
                                 }
                               ?>
                             </select>
                       </div>
                              <div class="form-group">
                                 <div class="label">Next of Kin</div>
                                 <input type="text" name="nok" class="text-input" value="<?php echo $r['nok'] ?>" />
                              </div>
                              <div class="form-group">
                                <div class="label">Next of Kin - Contacts</div>
                                <input type="text" name="nok_contacts" class="text-input" value="<?php echo $r['nok_contacts'] ?>" />
                              </div>
                              <div class="form-group">
                                <button type="submit" name="btnSubmit" class="button-input" id="submit">Submit</button>
                             </div>
                  </form>
           </div>
<?php
}

if($_POST['edit_loan']){

	$id = $_POST['edit_loan'];
	$sql = mysqli_query($connect," SELECT l.id, l.loan_amount, l.interest, l.duration, l.period, s.id, CONCAT(s.first_name,' ',s.last_name) as 'officer_name', l.loan_officer, l.date_entry FROM loan_entries l, staff s WHERE l.loan_officer = s.id AND l.id='$id' ");
	$r = mysqli_fetch_array($sql);
 ?>
<div style="width:90%;margin: 15px auto;">
 <div class="form_header">Edit Loan Form</div>
 <input type="hidden" name="loan_id" id="loan_id" value="<?php echo $id ?>" />
  <section class="step-wizard">
     <ul class="step-wizard-list">
       <li class="step-wizard-item" id="step_1">
           <span class="progress-count">1</span>
           <span class="progress-label">Edit Loan</span>
       </li>
       <li class="step-wizard-item" id="step_2">
           <span class="progress-count">2</span>
           <span class="progress-label">Edit Security</span>
       </li>
       <li class="step-wizard-item" id="step_3">
           <span class="progress-count">3</span>
           <span class="progress-label">Edit Guarantor</span>
       </li>
     </ul>
   </section>
  <section id="form_wrap">
    <form method="post" name="form" id="form2">
      <input type="hidden" name="post_id" id="post_id" value="" />
      <div class="form-group">
        <div class="label">Loan Officer</div>
          <input type="text" name="name_search" class="text-input" id="name_search" autocomplete="off" data-src="staff" value="<?php echo $r['officer_name'] ?>" />
             <input type="hidden" name="data_id" value="<?php echo $r['loan_officer'] ?>" id="data_id">
           <div id="drop-box" class="drop_down drop_large_size" style="width:575px;"></div>
      </div>
      <div class="form-group">
        <div class="label">Loan Amount</div>
        <input type="text" name="loan_amount" class="text-input" id="loan_amount" value="<?php echo number_format($r['loan_amount']) ?>" />
      </div>
      <div class="form-group">
      <div style="display:grid; grid-template-columns: repeat(2, 1fr);gap:10px;">
        <div>
          <div class="label">Period Category</div>
           <select name="duration" class="text-input" id="duration">
            <?php
              $array_period = array('day','month','year');
               foreach ($array_period as $key => $value) {
               	   if($r['duration']==$value)
               	   	echo '<option value="'.$value.'" selected="selected">'.ucfirst($value).'</option>';
               	   else
               	   	echo '<option value="'.$value.'">'.ucfirst($value).'</option>';
               }
            ?>
           </select>
        </div>
         <div>
           <div class="label">Period</div>
           <input type="text" name="period" class="text-input" value="<?php echo $r['period'] ?>" id="period"  />
         </div>
      </div>
      <div class="form-group">
        <div class="label">Interest Rate</div>
        <input type="text" name="interest" class="text-input" value="<?php echo $r['interest'] ?>" id="interest" />
      </div>
      <div class="form-group">
        <div class="label">Issue Date</div>
        <input type="text" name="date" class="text-input" id="datetimepicker" autocomplete="off" value="<?php echo date('Y/m/d H:i:s',strtotime($r['date_entry'])) ?>" />
      </div>
      <div class="form-group" id="button">        
        <button type="submit" name="btnSubmit" class="button-input" id="edit_loan">Submit</button>
      </div>
    </form>
</div>
<?php
}

if($_POST['edit_security']){

	$loan_id = $_POST['edit_security'];
	$sql = mysqli_query($connect,"SELECT * FROM loan_security WHERE loan='$id' ");
	$r = mysqli_fetch_array($sql);
?>

<div style="width:90%;margin:15px auto;">
 <form method="post" name="form" id="form">
    <input type="hidden" name="client2" id="client_id2" />
    <input type="hidden" name="loan" id="loan_security" value="<?php echo $r['loan'] ?>" />
      <div class="form-group">
        <div class="label">Security Name</div>
        <input type="text" name="security_name" class="text-input" value="<?php echo $r['security'] ?>"/>
      </div>
      <div class="form-group">
        <div class="label">Value</div>
        <input type="text" name="value" class="text-input" id="loan_amount" value="<?php echo $r['value'] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Type</div>
        <input type="text" name="type_sec" class="text-input" value="<?php echo $r['type'] ?>"/>
      </div>
      <div class="form-group">
        <div class="label">Serial No</div>
        <input type="text" name="serial" class="text-input" value="<?php echo $r['serial_no'] ?>"/>
      </div>      
      <div class="form-group">
        <div class="label">Description</div>
        <input type="text" name="desc" class="text-input" value="<?php echo $r['desc'] ?>" />
      </div>
      <div class="form-group">
        <button type="submit" name="btn-security" class="button-input" id="security_button">Submit</button>
      </div>
      <input type="hidden"  value="entry" name="security-entry" />
    </form>
</div>
<?php
}