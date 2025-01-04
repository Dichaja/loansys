<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once('../xsert/connect.php');
require_once('../data_files/sys_function.php');

if($_POST['sess_usr']){
 if(isset($_SESSION['sess_usr'])){
   $old_user = $_SESSION['sess_usr'];
   $type = $_SESSION['sess_type'];
 
 //destroys value
    unset($_SESSION['sess_usr']);
    //unset($_SESSION['sess_type']);
    
     if(!empty($old_user)) {
        //session_destroy();
        echo '<div style="display:block;color:#F00;text-align:center">User Session Expired...!!!</div>';
     }  
}else{
    if($_SESSION['sess_type'] && !empty($_SESSION['sess_usr'])){
     echo '<div style="display:block;color:#F00;text-align:center">User Session Expired...!!!</div>';
    }else{
      
    }
  }
}

if($_POST['edit_header']){

  $id = $_POST['edit_header'];

  $sql = mysqli_query($connect,"SELECT * FROM header_tpl ");
  if(mysqli_num_rows($sql)){
    $r = mysqli_fetch_array($sql);
    echo $r[1].'>'.$r[2].'>'.$r[3].'>'.$r[4].'>'.$r[5].'>'.$r[6];
  }
}


if(isset($_POST['loan_amount'])){

	$id = date("j").rand(10000,99999);
	$date = date("Y-m-d",strtotime($_POST['date']));
  $client = $_POST['client'];
  $amt = str_replace(',', '',$_POST['loan_amount']);
  $int = $_POST['interest'];
  $duration = $_POST['duration'];
  $period = $_POST['period'];
  $staff = $_POST['data_id'];
  $loan_fees = $_POST['loan_processing'];
  $staff_name = $_POST['name_search'];
  $fees_val = null;
  $last_fee = 0;
  $last_limit = 0;

  if($staff=='')
      $staff=rand(1000,9999);

$array = array(
    array('id' => '001', 'limit' => 200000, 'fee' => 10000),
    array('id' => '002', 'limit' => 500000, 'fee' => 15000),
    array('id' => '003', 'limit' => 2000000, 'fee' => 20000),
    array('id' => '004', 'limit' => 5000000, 'fee' => 30000),
    array('id' => '003', 'limit' => 5000001, 'fee' => 50000)
   );

foreach ($array as $item) {
    if ($amt <= $item['limit'] ) {
        $fees_val = $item['fee']; // Assign the fee value to the variable
        break; // Exit the loop after finding the first matching fee
    }
}

// If no fee is found below 150000, check for fees above 1000001
if ($fees_val === null) {
    foreach ($array as $item) {
        if ($item['limit'] >= 5000001) {
            $fees_val = $item['fee']; // Assign the fee value to the variable
            break; // Exit the loop after finding the first matching fee
        }
    }
}

$sql = mysqli_query($connect,"INSERT INTO loan_entries VALUES('$id','$client','$date','$amt','','01','".date('Y-m-d H:i:s')."','$int','$duration','$period','".$_SESSION['session_id']."',NULL,'$staff','$fees_val') ");

	if($sql){
		echo '1_'.$id.'_'.$_POST['client'];
     mysqli_query($connect,"INSERT INTO staff VALUES('$staff','$staff','$staff_name','','','','','0001','".date('Y-m-d H:i:s')."','','01','','','".$_SESSION['user_branch']."')");
	}else{
		echo '0 '.mysqli_error($connect);
	}
}

if($_POST['security-entry']){

//insert security
$sql = mysqli_query($connect,"INSERT INTO loan_security VALUES('".date("j").rand(10000,99999)."','".$_POST['security_name']."','".str_replace(",", "", $_POST['value'])."','".$_POST['type_sec']."','".$_POST['serial']."','".$_POST['desc']."','".$_POST['loan']."') ");

   if($sql){
        echo '1_'.$_POST['loan'].'_'.$_POST['client2'];
	    }else{
	      echo '0 '.mysqli_error($connect);
	   }
}

if(isset($_POST['guarantor-entry'])){
//inserts guarantor
   $sql = mysqli_query($connect,"INSERT INTO loan_guarantor VALUES('".date("j").rand(100000,999999)."','".$_POST['guarantor_name']."','".$_POST['gender']."','".$_POST['email']."','".$_POST['residence']."','".$_POST['occupy']."','".$_POST['loan2']."','".$_POST['contacts']."') "); 
   if($sql){
        echo 'success';
      }else{
        echo 'err';
     }
}

if(isset($_POST['expense_cat'])){

  $cat= $_POST['expense_cat'];
  $sql = mysqli_query($connect,"SELECT * FROM expense WHERE expense_acc='$cat'");
   echo '<option value="" selected="selected">Items</option>';
  while($rw=mysqli_fetch_array($sql)){
     echo '<option value="'.$rw[0].'">'.$rw[3].'</option>';
  }
}

if($_POST['check_client']){

  $id = $_POST['check_client'];
  $check_loan = $_POST['check_loan'];
  $status = '0';

  $qry = "SELECT c.id as 'client', l.id as 'loan', p.id as 'pay', l.status FROM clients c LEFT JOIN loan_entries l ON c.id = l.client LEFT JOIN loan_payments p ON l.id = p.loan WHERE c.id='$id' AND ";
    $qry .= " 1 ";
 
  $sql = mysqli_query($connect,$qry);
    if(mysqli_num_rows($sql)){
       while($r=mysqli_fetch_array($sql)){
               if($r['status']=='01'){
                   if($r['pay'])
                     $status = '01';
               }else if($r['pay']){
                  $status = '01';
               }
            }
    }
    echo $status;
}

if($_POST['check_staff']){

  $id = $_POST['check_staff'];
  $sql = mysqli_query($connect,"SELECT * FROM user_log WHERE staff='$id' ");

    if(mysqli_num_rows($sql)){
         echo 'success';
     }else{  
        echo 'yes_del';
     }
}

if($_POST['delete_client']){

  $id = $_POST['delete_client'];
 // $del = mysqli_query($connect,"DELETE c, e, p FROM clients c, loan_entries e, loan_payments p WHERE c.id=e.client AND e.client=p.client AND c.id='$id' ");
  $del = mysqli_query($connect,"DELETE FROM clients WHERE  id='$id' ");
  if(mysqli_affected_rows($connect)){
    echo 1;
  }else{
    echo "0 ".mysqli_error($connect);
  }
}

if($_POST['delete_loan']){

  $sql = mysqli_query($connect,"SELECT * FROM  loan_payments WHERE loan='".$_POST['delete_loan']."' ");
    if(mysqli_num_rows($sql)){
       echo 1;
     }else{
       echo 0;
   }
}

if($_POST['confirm_loan_delete']){

  $id = $_POST['confirm_loan_delete'];

  /*mysqli_query($connect,"DELETE FROM loan_adjusts WHERE loan='$id' ");
  if(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM  loan_payments WHERE loan='$id' "))){
    $del=mysqli_query($connect,"DELETE e, l FROM loan_entries e, loan_payments l WHERE e.id=l.loan AND e.id = '$id' ");
    if(mysqli_affected_rows($connect)){
      echo 1;
    }else{
      echo 0;
    }
  }else{
    $del = mysqli_query($connect,"DELETE FROM loan_entries WHERE id='$id'");
     if(mysqli_affected_rows($connect)){
      echo 1;
    }else{
      echo "0_".mysqli_error($connect);
    }
  }

 if(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM  loan_payments WHERE loan='$id' "))){
    echo 0;
 }else{*/
   $del = mysqli_query($connect,"DELETE l, g, s FROM loan_entries l, loan_guarantor g, loan_security s WHERE l.id = g.loan AND l.id = s.loan AND l.id = '$id' ");
   if(mysqli_affected_rows($connect)){
     echo 1;
   }else{
     $del = mysqli_query($connect, "DELETE FROM loan_entries WHERE id='$id' ");
       if(mysqli_affected_rows($connect)){
         echo 1;
       }else{
         echo 0;
       }
    }
  //}
}

if($_POST['del_loan_payment']){
  
  $id = $_POST['del_loan_payment'];
   $sql = mysqli_query($connect,"SELECT l.status FROM loan_payments p, loan_entries l WHERE l.id = p.loan AND p.id = '$id' ");
   $r = mysqli_fetch_array($sql);
    if($r['status']!='00')
      echo 'success';
    else
      echo 'err';
}

if($_POST['expense']){
  $id = $_POST['expense'];
  $del = mysqli_query($connect,"DELETE FROM expense_entries WHERE id='$id'");
  if(mysqli_affected_rows($connect)){
    echo 1;
  }else{
    echo 0;
  }
}

if($_POST['check_mem_id']){
   $id = $_POST['check_mem_id'];
   $sql = mysqli_query($connect,"SELECT * FROM clients WHERE data_id='$id' ");
    if(mysqli_num_rows($sql)){
      echo 'success';
    }else{
      echo 'err';
    }
}

if($_POST['search_client']){

 $keyword = $_POST['search_client'];
 $sql = mysqli_query($connect,"SELECT * FROM clients WHERE client_names LIKE '%$keyword%' ");

 ?>
<ul id="item-list">
    <?php
      if(mysqli_num_rows($sql)){
         while($rw=mysqli_fetch_array($sql)){
         ?>
           <li class="list_item" data-set="<?php echo $rw[0] ?>"><?php echo $rw[1] ?></li>
        <?php
       }
     }else{
       ?>
         <li><b>Not Result(s):&nbsp;</b><?php echo $keyword ?></li>
     <?php
   }
 ?>
 </ul>
 <?php
}

if(isset($_POST['get_loan'])){

 $loan_id = $_POST['get_loan'];
 $search = $_POST['srch_val'];

 $sql = mysqli_query($connect,"SELECT CONCAT(c.first_name,' ',c.last_name), l.loan_amount, l.date_entry, l.duration, l.period, c.id, l.interest, l.status, c.data_id, l.id as 'loan_id', l.modify_date FROM clients c, loan_entries l WHERE c.id=l.client AND l.id='$loan_id'");
 
 $rw = mysqli_fetch_array($sql);

 //return loan$loan=0;
 $loan= $rw[1];
 $int = $rw[6];

 /*if($rw['status']=='00'){
    $query = mysqli_query($connect,"SELECT pay_date FROM loan_payments WHERE loan='$loan_id' ORDER BY pay_date DESC LIMIT 0,1 ");
    $result = mysqli_fetch_array($query);
      $period_status = elaspe_period($rw[2],$result[0], $rw[3]);
      $loop = $period_status;
    } else{         
          $period_status = ($rw[4]-return_period($rw[2],$rw[3]));//returns period remain for loan
          $loop = $rw[4] - ($rw[4]-return_period($rw[2],$rw[3]));
    }  */

  if($rw['status'] != '02')
      $period_status = 1;
  else 
      $period_status = -1;

  $split = explode(',',$search);    
?>

<input type="hidden" name="sess_usr" value="<?php echo $_SESSION['sess_type'] ?>" id="usr" />
  <div class="form_header">Loan Payment(s) Review</div>
   <div>
     <span></span>
     <span id="print_rpt" style="display:inline-block;width:100%;text-align: right;"><img src="../img_file/print-icon.svg" width="20" height="20"></span>
   </div>
  
  <form method="post" name="form" id="loans" action="../data_rp/loan_activity.php">
    <input type="hidden" id="client_loan" name="loan_client" value="<?php echo $rw[5] ?>" />
    <input type="hidden" name="loan" id="loan_id" value="<?php echo $loan_id ?>" />
      <div style="display:block;margin:5px 0">
        <div style="width:200px;float:left;">Member</div> <b><?php echo $rw[0] ?></b> 
      </div>
      <div style="display:block;margin:5px 0">
        <div style="width:200px;float:left;">Member ID</div> <b><?php echo $rw['data_id'] ?></b>
      </div>
      <div style="display:block;margin:5px 0">
        <div style="width:200px;float:left;">Loan</div><b><?php echo number_format($loan) ?></b>
      </div>
      <div style="display:block;margin:5px 0">
        <div style="width:200px;float:left;">Issue Date</div> <b><?php echo date("d-m-Y",strtotime($rw[2])) ?></b>
      </div>
      <div style="display:block;margin:5px 0">
        <div style="width:200px;float:left;">Loan Period</div> <b><?php echo $rw[4]." ".$rw[3]." (s)" ?> at <?php echo $rw[6]."% " ?></b>
      </div>
      <div style="display:block;margin:5px 0">
       <div style="width:200px;float:left;">Periodic Payment (PMT)</div><b>
           <?php
             $period_cat = $rw['period'];  
             $pmt = round(($loan * ($rw[6]/100)+$loan) / $period_cat);      
             echo number_format(round(($loan * ($rw[6]/100)+$loan) / $period_cat)) 
           ?></b>
      </div>
      <div style="margin:10px 0;border-top: solid 1px #CCC;"><span><b>Loan Statement</b></span></div>
      <div style="display:block;" id="details">
        <table width="100%" align="center" cellpadding="5" cellspacing="0" class="report_display">
          <tr>
             <td>No</td>
             <td>Period</td>
             <td>Entry By</td>
             <td>Pay Date</td>
             <td align="right">Amount Paid</td>
             <!--<td align="right">Interest Balance</td>-->
             <td align="right">Accumulated Interest</td>
             <!--<td align="right">Principal Balance</td>-->
             <td align="right">Due Balance</td>
          </tr>
          <?php

            $total=0;
            $client=0;
            $loan_payments = 0;
            $acc_pmt = 0;
            $count=-1;

  

  if($rw['status']=='03')
    $period = elaspe_period($rw['date_entry'],$rw['modify_date'], $rw['duration']);
  else
    $period = status_period(date_set_back($rw[2], 1),$rw['duration'],'');

  for($i=0; $i <= $period; $i++){
     $get_date = endCycle($rw[2], $i, $rw['duration']);
     $pay_date = '';
     $pay = 0;
     $usr = '';
            
  if($rw[3]=='day') {

      $loan_principal = (($rw['loan_amount'] * ($int/100))) + $rw['loan_amount'];
      $sql = mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='$loan_id' AND pay_date='".date("Y-m-d",strtotime($get_date))."'");

        if(mysqli_num_rows($sql)){
              while($rws = mysqli_fetch_array($sql)){
                $pay = $rws['amount_paid'];
                $loan_payments+=$rws['amount_paid'];
                $total += $pay;
                $pay_date = $rws['pay_date'];
                $usr = $rws['user'];
              }
          }
 
      $acc_pmt_bal = ($acc_pmt - $loan_payments);
        $loan_due = $loan_principal-$loan_payments;
          if(($rw['period'] - $i) < 0 && ($rw['period'] - $i) >= -7)
            $acc_pmt_bal = $loan_due;
          else
            $acc_pmt += $pmt;
    $count+=1;
  ?>
               <tr>
                <td><?php echo $count ?></td>
                <td><?php echo $get_date ?></td>
                <td><?php if($usr){ echo return_usr($connect,$usr);}else{ echo '-'; } ?></td>
                <td><?php if($pay_date){ echo date("d-m-y",strtotime($pay_date)); }else{ echo '-'; } ?></td>
                <td align="right"><?php if($pay!=0){ echo number_format($pay); } else { echo "-"; } ?></td>
                <td align="right"><?php if($acc_pmt_bal<=0){ echo 0; } else { echo number_format($acc_pmt_bal);} ?></td>
                <td align="right"><?php echo number_format($loan_due) ?></td>
              </tr>
          <?php
      if($loan_due<=0)
        $i = $period;  //stops loop where there is no balance;  
    }
      if($i==0 AND $rw['status']!='03')
       $period = $period-1; // maintains period count at 30
   }
  ?>
          <tr class="pay_data" style="font-weight: bold;">
            <td colspan="4">Total</td>
            <td align="right"><?php echo number_format($total); ?></td>
            <td align="right"><?php if($acc_pmt_bal<0){ echo 0; } else { echo number_format($acc_pmt_bal);} ?></td>
            <td></td>
          </tr>
   <?php

    if(loan_status($connect,$rw['loan_id'],'')>0){ ?>
           <tr class="pay_data">
            <td colspan="2" >Total Due Balance</td>
            <td colspan="5" align="right" id="due_bal"><b><?php echo number_format(loan_status($connect,$rw['loan_id'],'')) ?></b></td>
            <td></td>
          </tr>
        <?php }

       if($period_status<0){ ?>
         <tr>
            <td colspan="2">Loan Overdue</td>
            <td colspan="5">
               <div class="grid-2">
                   <div style="text-align: left;"><input type="checkbox" name="extend_loan" value="03" id="extend" />&nbsp;Extend Loan Period</div>
                   <div><input type="text" name="action" value="" class="text-input" placeholder="Remark" /></div>
                 </div>
               </td>
         </tr>
    <?php }
       if($rw['status']=='03'){ ?>
          <tr style="font-weight: bold;" class="pay_data">
            <td colspan="2">Remark</td>
            <td colspan="5" align="right">Loan Period Extended</td>
            <td></td>
          </tr>
    <?php } ?>
        </table>
  <?php if(loan_status($connect,$rw['loan_id'],'')>0 && $rw['status']!='03'){  ?>
        <div style="width: 100%" id="extend_loan"></div>
        <div class="view_row pay_data">
          <div style="margin: 10px 0 0;padding: 0 0 5px;color: #fd7e14;"><b>Make Payment</b></div>
          <div class="grid-6-items row_block" style="color: #fd7e14;">
             <div>No</div>
             <div>Pay Date</div>
             <div>Amount Paid</div>
             <div>Mode of Pay</div>
             <div>Paid To</div>
             <div>Account No(From)</div>
          </div>
          <div class="grid-6-items row_block rows" id="rows_1">
             <div>1</div>
             <div><input type="text" name="pay_date[]" id="datetimepicker" class="text-input" autocomplete="off" value="" placeholder="dd/mm/yyyy" /></div>
             <div><input type="text" name="pay_loan[]" class="text-input" id="pay_1" /></div>
             <div>
                <select name="mop[]" id="mop_1" class="text-input">
                 <option value="" selected="selected">Select</option>
                  <?php 
                    $qry = mysqli_query($connect,"SELECT * FROM mop");
                     while($r = mysqli_fetch_array($qry)){
                       ?><option value="<?php echo $r['id'] ?>"><?php echo $r['name']; ?></option><?php
                     }
                  ?>
                </select>
              </div>
              <div>
                <select name="accTo[]" id="accTo_1" class="text-input">
                 <option value="" selected="selected">Select</option>
                </select></div>
              <div><input type="text" name="accNo[]" class="text-input" id="accNo_1" value="" /></div>
            </div>
          </div>
          <div style="width:100%;margin:10px auto;padding:5px 9px;" class="view_row">
            <button type="submit" name="btnSubmit" class="button-input" id="submit_loan">Submit</button>
          </div>
      <?php } ?>
      </div>      
   </div>
   <input type="hidden" name="interest"  value="<?php echo $rw[6] ?>" />
   <input type="hidden" name="duration"  value="<?php echo $rw[3] ?>" />
 </form>
<?php
}

if($_POST['get_client']){

  $client = $_POST['get_client'];

  ?>
  <div id="client_details" style="width:95%;margin: auto;font-size:12px;">
    <div class="form_header">Loan History</div>
    <div class="grid-2">
      <span></span>
      <span id="print_rpt" style="display: inline-block;text-align: right;width: 100%;"><img src="../img_file/print-icon.svg" width="20" height="20"></span>
    </div>
  <?php
    $sql = mysqli_query($connect,"SELECT c.first_name, c.last_name, c.email, c.residance, c.gender, c.business_name, c.city, b.branch_name, c.photo_dir, c.contacts, c.id, c.data_id FROM clients c, branches b WHERE c.branch_id = b.id AND c.id='$client' ");

    if(mysqli_num_rows($sql)){

      $rw = mysqli_fetch_array($sql); // returns client info
       $photo_dir = $rw['photo_dir'];
         if(!$rw['photo_dir'])
          $photo_dir = 'default.png';
      ?>
      <div style="font-weight: bold;width: 100%;margin: 10px 0;text-align:center;font-size:18px;"><?php echo $rw[0].' '.$rw[1].'<br><span style="font-weight:normal;">'.$rw['data_id'].'</span>'?></div>
      <div style="width:30%;max-width: 100%;margin:10px auto;text-align:center;">
        <img src="<?php echo '../data_files/profile/'.$photo_dir ?>" width="100px" height="100px" />
      </div>
      <div style="display:grid;grid-template-columns: repeat(4, 1fr);gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
        <span><b>Email</b>&nbsp;&nbsp;<?php echo $rw['email'] ?></span>
        <span><b>Contacts</b>&nbsp;&nbsp;<?php echo $rw['contacts'] ?></span>
        <span><b>Residence / Address:</b>&nbsp;&nbsp;<?php echo $rw['residance'].', '.$rw['city'] ?></span>
        <span><b>Gender</b><?php echo $rw['gender'] ?></span>
      </div>
      <div style="display:grid;grid-template-columns: repeat(4, 1fr);gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
        <span><b>Business Name</b>&nbsp;&nbsp;<?php echo $rw['business_name'] ?></span>
        <span><b>Branch</b>&nbsp;&nbsp;<?php echo $rw['branch_name'] ?></span>
        <span></span>
        <span></span>
      </div>
      <div style="display:block;margin:5px 0;">
        <span class="header" style="font-size:18px; color: #fd7e14;">Loan Payment Statement</span>
      </div>
      <?php

       $sql = mysqli_query($connect,"SELECT * FROM loan_entries WHERE client = '".$rw['id']."' ORDER BY date_entry DESC ");

           if(mysqli_num_rows($sql)){

            $row_count=0;
             while($row=mysqli_fetch_array($sql)){

              if($row[3]=='day'){
                 $interest=(round($row[2]/100,2)*$row[1]) / $row['period'];
              }else if($row[3]=='month'){                
                $interest=(round($row[2]/100,2)*$row[1]);
              }
               
              $loan = $row['loan_amount'];
              $period=$row['period'];
              $dur = $row['duration'];
              $status = $row['status'];
              $period_status = $period - return_period($row['date_entry'],$dur);
              $loan_id = $row['id'];

              if($status == '03')
                $period_status=0;

              ?>
              <div style="display: grid; grid-template-columns: 2fr 3fr; width: 100%; font-weight:bold; color: #fd7e14;">
                   <span>Issue <?php echo $row_count+=1 ?>)</span>
                   <span><?php echo date("M/d/Y", strtotime($row['date_entry']))." - ".date("M/d/Y",strtotime(endCycle($row['date_entry'], $period, $dur))) ?></span>
              </div>
              <div style="display:grid;grid-template-columns: repeat(6, 1fr);gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
                  <span>Loan</span>
                  <span><?php echo number_format($loan) ?></span>
                  <span>Interest Rate</span>
                  <span><?php echo $row['interest'] ?>%</span>
                  <span>Loan Period</span>
                  <span><?php echo $period."(".$dur.")"; ?></span>
              </div> 
               <?php
                  $sql2 = mysqli_query($connect,"SELECT g.guarantor, g.contacts, s.security, s.serial_no, s.value FROM loan_guarantor g, loan_security s WHERE s.loan=g.loan AND g.loan='$row[0]' ");
                  $row_loan = mysqli_fetch_array($sql2);
               ?>
               <div style="display:grid;grid-template-columns: 1fr 2fr 1fr 1fr 1fr 3fr;gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
                  <span>Guarantor</span>
                  <span><?php echo $row_loan[0] ?></span>
                  <span>Contacts</span>
                  <span><?php echo $row_loan[1] ?></span>
                  <span>Security</span>
                  <span><?php echo 'Name: '.$row_loan[2].", &nbsp;Serial: ".$row_loan[3].", &nbsp;Value :".number_format($row_loan[4]); ?></span>
              </div> 
              <div style="display:grid;grid-template-columns: 1fr 2fr;gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;font-weight:bold;">
                <span>Payments</span>
                <span>
                   <div style="display:grid;grid-template-columns: 1fr 2fr 2fr 2fr;gap:10px;margin: 5px 0;">
                      <span>No</span>
                      <span>Amount</span>
                      <span>Pay Date</span>
                      <span>Balance</span>
                   </div>
               </span>
             </div>           
             <?php
                  $sql3 = mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='$row[0]' ORDER BY pay_date ASC ");

                  $count=0;
                  $total=0;

                  if(mysqli_num_rows($sql3)){
                    
                    while($query=mysqli_fetch_array($sql3)){
                      ?>
                      <div style="display:grid;grid-template-columns: 1fr 2fr;gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
                          <span></span>
                          <span>
                              <div style="display:grid;grid-template-columns: 1fr 2fr 2fr 2fr;gap:10px;margin: 5px 0;">
                                 <span><?php echo $count += 1 ?></span>
                                 <span><?php echo number_format($query[1]) ?></span>
                                 <span><?php echo date("d-m-Y",strtotime($query[2]))?></span>
                                 <span></span>
                               </div>
                          </span>
                      </div>
                      <?php
                      $total += $query[1];
                    }                    
                  }else{
                    ?>
                    <div style="width:100%;height:90px;text-align: center;background-color: #ccc">No Pay Details...</div>
                  <?php
                  }
                  ?>
                 <div style="display:grid;grid-template-columns: 1fr 2fr;gap:10px;margin: 5px 0;border-bottom: solid 1px #ccc;font-weight: bold;">
                          <span>Total</span>
                          <span>
                              <div style="display:grid;grid-template-columns: 1fr 2fr 2fr 2fr;gap:10px;margin: 5px 0;">
                                 <span></span>
                                 <span><?php echo number_format($total) ?></span>
                                 <span></span>
                                 <span><?php 
                                   /*if($row[4]==($row[4]-return_period($row[5],$dur))){
                                       echo number_format($row[1]);                             
                                   }else{
                                     if(loan_balances($connect,$period,$row[2],$row[1],$row[0],$row[5],$dur,$status,"balance")<0){
                                          echo 0;
                                         }else{
                                           echo number_format(loan_balances($connect,$period,$row[2],$row[1],$row[0],$row[5],$dur,$status,"balance")) ;
                                        }
                                   }*/
                                    echo number_format(loan_status($connect,$loan_id,$status));
                                   ?></span>
                               </div>
                          </span>
                      </div>                  
              <?php
             }
           }
         }else{
               ?>
              <div style="display: block;width:100%;height:90px;text-align: center;">No Details Found...</div>
            <?php
           }
         ?>
</div>
<?php
}

if($_POST['pay_statement']){
  
  $client = $_POST['pay_statement'];
  $post_loan = $_POST['post_loan'];

  $str_qry = "SELECT c.first_name, c.last_name, c.contacts, c.residance, l.id, c.data_id FROM loan_entries l, clients c WHERE l.client = c.id AND c.id='$client' AND  ";
     if($post_loan)
        $str_qry .= " l.id = '$post_loan' AND ";
      else
         $str_qry .= " l.status!='01' AND ";
       $str_qry .= " 1 ";

  $qry = mysqli_query($connect,$str_qry);

    $rs = mysqli_fetch_array($qry);

  ?>
  <div style="width:90%;margin: 10px auto;">
    <div style="display:block;margin:5px 0;">
      <span class="header" style="font-size:18px; color: #fd7e14;text-align: center;width:100%;display: inline-block;">Loan Payment Ledger</span>
      <span style="display: inline-block;width: 100%;text-align: center;font-size: 14px">
        <?php echo $rs[0].' '.$rs[1].'<br>'.$rs['data_id'] ?>
      </span>
      <span style="display: inline-block;width: 100%;text-align: center;font-size: 12px">
        <?php
            echo $rs[2].'<br>'.$rs[3]
        ?>
      </span>
      </div>
      <?php

       $sql = mysqli_query($connect,"SELECT * FROM loan_entries WHERE id = '".$rs['id']."' ORDER BY date_entry  DESC ");

           if(mysqli_num_rows($sql)){

            $row_count=0;
             while($row=mysqli_fetch_array($sql)){

              if($row[3]=='day'){
               $interest=(round($row[2]/100,2)*$row[1])/30;
              }else if($row[3]=='month'){                
                $interest=(round($row[2]/100,2)*$row[1]);
              }
               
              $loan = $row['loan_amount'];
              $period=$row['period'];
              $dur = $row['duration'];
              $status = $row['status'];
              $period_status = $period-return_period($row['date_entry'],$dur);
              $loan_id = $row['id'];

              ?>
              <div style="display: grid; grid-template-columns: 2fr 3fr; width: 100%; font-weight:bold; color: #fd7e14;">
                   <span>Issue <?php echo $row_count+=1 ?>)</span>
                   <span><?php echo date("M/d/Y", strtotime($row['date_entry']))." - ".date("M/d/Y",strtotime(endCycle($row['date_entry'], $period, $dur))) ?></span>
              </div>
              <div style="display:grid;grid-template-columns: repeat(6, 1fr);gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
                  <span>Loan</span>
                  <span><?php echo number_format($loan) ?></span>
                  <span>Interest Rate</span>
                  <span><?php echo $row['interest'] ?>%</span>
                  <span>Loan Period</span>
                  <span><?php echo $period."(".$dur.")"; ?></span>
              </div> 
               <?php
                  $sql2 = mysqli_query($connect,"SELECT g.guarantor, g.contacts, s.security, s.serial_no, s.value FROM loan_guarantor g, loan_security s WHERE s.loan=g.loan AND g.loan='$row[0]' ");
                  $row_loan = mysqli_fetch_array($sql2);
               ?>
               <div style="display:grid;grid-template-columns: 1fr 2fr 1fr 1fr 1fr 3fr;gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
                  <span>Guarantor</span>
                  <span><?php echo $row_loan[0] ?></span>
                  <span>Contacts</span>
                  <span><?php echo $row_loan[1] ?></span>
                  <span>Security</span>
                  <span><?php echo 'Name: '.$row_loan[2].", &nbsp;Serial: ".$row_loan[3].", &nbsp;Value :".number_format($row_loan[4]); ?></span>
              </div> 
              <div style="display:grid;grid-template-columns: 1fr 2fr;gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;font-weight:bold;">
                <span>Payments</span>
                <span>
                   <div style="display:grid;grid-template-columns: 1fr 2fr 2fr 2fr;gap:10px;margin: 5px 0;">
                      <span>No</span>
                      <span>Amount</span>
                      <span>Pay Date</span>
                      <span>Balance</span>
                   </div>
               </span>
             </div>           
             <?php
                  $sql3 = mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='$row[0]' ORDER BY pay_date  ASC ");

                  $count=0;
                  $total=0;

                  if(mysqli_num_rows($sql3)){
                    
                    while($query=mysqli_fetch_array($sql3)){
                      ?>
                      <div style="display:grid;grid-template-columns: 1fr 2fr;gap:10px;border-bottom: solid 1px #ccc;margin: 5px 0;">
                          <span></span>
                          <span>
                              <div style="display:grid;grid-template-columns: 1fr 2fr 2fr 2fr;gap:10px;margin: 5px 0;">
                                 <span><?php echo $count += 1 ?></span>
                                 <span><?php echo number_format($query[1]) ?></span>
                                 <span><?php echo date("d-m-Y",strtotime($query[2]))?></span>
                                 <span></span>
                               </div>
                          </span>
                      </div>
                      <?php
                      $total += $query[1];
                    }                    
                  }else{
                    ?>
                    <div style="display: block;width:100%;height:90px;text-align: center;background-color: #ccc">No Details Found...</div>
                  <?php
                  }
                  ?>
                 <div style="display:grid;grid-template-columns: 1fr 2fr;gap:10px;margin: 5px 0;border-bottom: solid 1px #ccc;font-weight: bold;">
                          <span>Total</span>
                          <span>
                              <div style="display:grid;grid-template-columns: 1fr 2fr 2fr 2fr;gap:10px;margin: 5px 0;">
                                 <span></span>
                                 <span><?php echo number_format($total) ?></span>
                                 <span></span>
                                 <span><?php  echo number_format(loan_status($connect,$loan_id,$status)); ?></span>
                               </div>
                          </span>
                      </div>                  
              <?php
             
           }
         }else{
               ?>
              <div style="display: block;width:100%;height:90px;text-align: center;border:solid 1px #ccc; border-radius: 5px;padding: 20px 0;">No Running Loan Found...</div>
            <?php
           }
         ?>
    </div>
<?php
}

if($_POST['edit_expense']){
  $id = $_POST['edit_expense'];
   $sql = mysqli_query($connect,"SELECT e.id, e.voucher, e.pay_date, e.expense, e.amount, e.mop, s.expense_acc, e.bank, e.account FROM expense_entries e, expense s WHERE  e.expense=s.id AND e.id='$id'");
   $rw = mysqli_fetch_array($sql);

  ?>

  <form  name="form 1" method="post">
    <span class="header">Edit Expense</span>
    <div class="form-group">
        <div class="label">Voucher</div>
        <input type="text" name="voucher" class="text-input" value="<?php echo $rw[1] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Date</div>
        <input type="text" name="date" class="text-input" value="<?php echo $rw[2] ?>" id="datetimepicker" />
      </div>
      <div class="form-group">
        <div class="label">Expense</div>
        <select name="category" class="select-input" id="category">
          <?php
            $sql = mysqli_query($connect,"SELECT * FROM expense GROUP BY category");
            while($row=mysqli_fetch_array($sql)){
              if($rw[6]==$row[1]){
                $category = $row[1];
                echo '<option value="'.$row[1].'" selected="selected">'.$row[2].'</option>';
              }else{
              echo '<option value="'.$row[1].'">'.$row[2].'</option>';
              }
            }
          ?>
           </select>
      </div>
      <div class="form-group">
        <div class="label">Item</div>
        <select name="expense" id="expense" class="select-input">
           <?php
            $sql = mysqli_query($connect,"SELECT * FROM expense WHERE expense_acc='$rw[6]' ");
            while($row=mysqli_fetch_array($sql)){
              if($rw[3]==$row[0]){
                echo '<option value="'.$row[0].'" selected="selected">'.$row[3].'</option>';
              }else{
              echo '<option value="'.$row[0].'">'.$row[3].'</option>';
              }
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <div class="label">Amount</div>
        <input type="text" name="amount" class="text-input" value="<?php echo number_format($rw[4]) ?>" id="pay" />
      </div>
      <div class="form-group">
        <div class="label">Paid By</div>
        <select name="mop" class="select-input">
          <?php 
            $array = array('Cash','Bank','Mobile Money');
            foreach($array as $val){
              if($rw[5]==$val){
                 echo '<option value="'.$val.'" selected="selected">'.$val.'</option>';
              }else{
                 echo '<option value="'.$val.'">'.$val.'</option>';
              }
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <div class="label">Bank</div>
        <input type="text" name="bank" class="text-input" autocomplete="off" value="<?php echo $rw[7] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Account</div>
        <input type="text" name="account" class="text-input" autocomplete="off" value="<?php echo $rw[8] ?>" />
      </div>
      <div class="form-group">
        <button type="submit" name="btnSubmit" class="button-input">Update</button>
      </div>
      <input type="hidden" name="entry_id" value="<?php echo $id ?>" />
  </form>
  <?php
}


if($_POST['edit_expense']){

  $id = $_POST['edit_expense'];
  $sql = mysqli_query($connect,"SELECT * FROM expense WHERE expense_acc='$id'");

  $rw = mysqli_fetch_array($sql);
  ?>
   <form method="post" name="form">
    <span class="header">Edit Expense</span>
      <div class="form-group">
        <div class="label">Expense Category</div>
        <input name="category-id" type="hidden" value="<?php echo $rw[1] ?>" />
        <input type="text" name="edit-category" class="text-input" value="<?php echo $rw[2] ?>" />
      </div>
      <?php
         $qry = mysqli_query($connect,"SELECT * FROM expense WHERE expense_acc='$id' ");
         while($result=mysqli_fetch_array($qry)){
          $count += 1;
          ?>
          <input type="hidden" name="expense_id[]" value="<?php echo $result[0] ?>" />
           <div class="expense_row" id="expenseRow_<?php echo $count ?>">
              <div class="label">&nbsp;</div>
              <input type="text" name="names[]" class="text-input3" value="<?php echo $result[3] ?>" />
           </div>
          <?php
         }
      ?>

      <div class="expense_row" id="expenseRow_<?php echo $count+1 ?>">
        <input type="hidden" name="expense_id[]" value="none" />
              <div class="label">&nbsp;</div>
              <input type="text" name="names[]" class="text-input3" placeholder="Add Expense"/>
           </div>
      <div class="form-group">
        <div class="label">About Expense</div>
        <textarea name="desc_expense" class="area-input"><?php echo $rw[4] ?></textarea>
      </div>
      <div class="form-group">
        <button type="submit" name="btnSubmit" class="button-input">Submit</button>
      </div>
     </form>
  <?php
}

if($_POST['edit_staff']){
 $id = $_POST['edit_staff'];
 $sql = mysqli_query($connect,"SELECT * FROM staff WHERE id='$id' ");
 $rw = mysqli_fetch_array($sql);
?>
    <h1>Edit Staff Details</h1>
    <form method="post" name="form">
      <input type="hidden" value="<?php echo $id ?>" name="staff_id" />
      <div class="form-group">
        <div class="label">Staff No</div>
        <input type="text" name="staff_no" class="text-input" value="<?php echo $rw[1] ?>"  />
      </div>
      <div class="form-group">
        <div class="label">Staff Names</div>
        <input type="text" name="names" class="text-input" value="<?php echo $rw[2] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Gender</div>
        <select name="gender" class="select-input">
            <?php
              $array_gender=array('Male','Female','');
              foreach($array_gender as $gender){               
                  if($gender==$rw[8]){
                    echo '<option value="'.$rw[8].'" selected="selected">'.$rw[8].'</option>';
                   }
                  else{
                   echo '<option value="'.$gender.'">'.$gender.'</option>';
                  }                  
              }
            
            ?>
        </select>
      </div>
      <div class="form-group">
        <div class="label">Contacts</div>
        <input type="text" name="contacts" class="text-input" value="<?php echo $rw[3] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Email</div>
        <input type="text" name="email" class="text-input" value="<?php echo $rw[4] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Residence</div>
        <input type="text" name="residence" class="text-input" value="<?php echo $rw[5] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Other Occupation</div>
        <input type="text" name="occupy" class="text-input" value="<?php echo $rw[6] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Next of Kin</div>
        <input type="text" name="nok" class="text-input"  value="<?php echo $rw[10] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Next of Kin - Contacts</div>
        <input type="text" name="nok_contacts" class="text-input" value="<?php echo $rw[11] ?>" />
      </div>
      <div class="form-group">
        <div class="label">Staff Type</div>
        <input type="text" name="staff_type" class="text-input" value="<?php echo $rw[12] ?>" />
      </div>
      <div class="form-group">
        <button type="submit" name="btnSubmit" class="button-input">Update</button>
      </div>
     </form>
<?php
}

if($_POST['detail_staff']){
  $id=$_POST['detail_staff'];
  $sql = mysqli_query($connect,"SELECT * FROM staff WHERE id='$id' ");
  $rw = mysqli_fetch_array($sql);
  ?>
   <div style="padding:20px;">
      <div class="details">
        <span class="header">Staff Details</span>
      </div>
      <div class="details">
        <div class="details_name">1) Staff No</div>
        <div class="details_desc"><?php echo $rw[1] ?></div>
      </div>
      <div class="details">
        <div class="details_name">2) Names</div>
        <div class="details_desc"><?php echo $rw[2] ?></div>
      </div>
      <div class="details">
        <div class="details_name">3) Staff No</div>
        <div class="details_desc"><?php echo $rw[8] ?></div>
      </div>
      <div class="details">
        <div class="details_name">4) Contacts</div>
        <div class="details_desc"><?php echo $rw[3] ?></div>
      </div>
      <div class="details">
        <div class="details_name">5) Email</div>
        <div class="details_desc"><?php echo $rw[5] ?></div>
      </div>
      <div class="details">
        <div class="details_name">6) Residence</div>
        <div class="details_desc"><?php echo $rw[6] ?></div>
      </div>
      <div class="details">
        <div class="details_name">7) Occupation</div>
        <div class="details_desc"><?php echo $rw[7] ?></div>
      </div>
      <div class="details">
        <div class="details_name">8) Next of Kin</div>
        <div class="details_desc"><?php echo $rw[10] ?></div>
      </div>
      <div class="details">
        <div class="details_name">9) Next of Kin Contacts</div>
        <div class="details_desc"><?php echo $rw[11] ?></div>
      </div>
   </div>
  <?php
}

if($_POST['add_user']){
?>
<div class="form_header">Add User</div>
<form name="form1" id="form1" method="post" action="add_user.php">
        <input name="usr_id" type="hidden" value="" id="usr_id" />
            <div class="form-group">
                 <div class="label">Staff Name</div>
                <input type="textbox" name="staff_names" class="text-input" required="required" id="staff_names" autocomplete="off" required="" />
                <input type="hidden" name="staff_id" value="" id="staff_id">
                      <div id="drop-box" class="drop_down drop_large_size" style="width:572px;"></div>
             </div>
             <div class="form-group">
                <div class="label">User Name</div>
                <input type="textbox" name="usr_name" class="text-input" required="required" id="usr" autocomplete="off" required="" /></td>
             </div>
             <div class="form-group">
                <div class="label">Password</div>
                <input type="password" name="pwd" class="text-input" required="required" id="pwd" />
                <div id="pass" style="display:block;font-family:'Segoe UI';width:100%;float:right;margin-top:10px;"></div>
                </div>
             <div class="form-group">
                <div class="label">Confirm Password <span id="mis-match"></span></div>
                <input type="password" name="pwd2" class="text-input" required="required" id="pwd2" />
                <div id="confirm2" style="display:block;float:right;width:100%;font-family:'Segoe UI'; color:#009900;margin-top:10px;"></div>
              </div>
            <div class="form-group">
                <div class="label">User Type</div>
                  <select name="usr_type" class="text-input" id="usr_type">
                     <option value="" selected="selected">Select</option>
                     <option value="admin">Admin</option>
                     <option value="director">Director</option>
                     <option value="data_clerk">Data Clerk</option>
                  </select>
             </div>
             <div class="form-group">
                           <div class="label">User Branch</div>
                             <select name="branch_details" class="text-input">
                               <option value="00010" selected="">Select</option>
                               <?php
                                  $sql = mysqli_query($connect,"SELECT * FROM branches");
                                   if(mysqli_num_rows($sql)){
                                     while($r = mysqli_fetch_array($sql)){
                                      if($r[1])
                                       echo '<option value="'.$r['id'].'">'.$r['branch_name'].'</option>';
                                     }
                                   }
                               ?>
                             </select>
                       </div>             
            <div class="form-group">
                 <input type="submit" name="submit" value="Submit" class="button-input" />
            </div>
       </form>
<?php
}

if($_POST['confirm_expense']){
  $id  = $_POST['confirm_expense'];
  $sql = mysqli_query($connect,"SELECT * FROM expense e,  expense_entries s WHERE e.id = s.expense AND e.expense_acc='$id' ");
  if(mysqli_num_rows($sql)){
    echo 1;
  }else{
    echo 0;
  }
}

if($_POST['delete_expense_entry']){
  $id = $_POST['delete_expense_entry'];
  $del = mysqli_query($connect,"DELETE e, s FROM expense e, expense_entries s WHERE e.id = s.expense AND e.expense_acc='$id' ");
  if(mysqli_affected_rows($connect)){
    echo 1;
  }else{
    $del = mysqli_query($connect,"DELETE FROM expense WHERE expense_acc='$id' ");
    if(mysqli_affected_rows($connect)){
      echo 1;
    }else{
      echo "0 ".mysqli_error($connect);
    }
  }
}

if($_POST['delete_staff']){
  $id = $_POST['delete_staff'];
  $delete = mysqli_query($connect,"DELETE FROM staff WHERE id='$id' ");

  if($delete){
    echo 1;
  }else{
    echo 0;
  }
}

if($_POST['get_expense']){

  $keys = $_POST['get_expense'];
  $index = $_POST['index'];
  $sql = mysqli_query($connect,"SELECT * FROM expense_items WHERE item LIKE '%$keys%' ORDER BY item ASC ");
  
  if(mysqli_num_rows($sql)){
      while($rw=mysqli_fetch_array($sql)){
         ?>
         <div class="list_items" data-set="<?php echo $rw[0] ?>" index="<?php echo $index ?>" style="text-transform: capitalize;"><?php echo strtolower($rw[1])  ?></div>
        <?php
        }
       }else{
         echo 0;
       }
}

if($_POST['branch_check']){

  $id = $_POST['branch_check'];
  $status = 'yes_del';
   $sql = mysqli_query($connect,"SELECT * FROM clients WHERE branch_id='$id' ");
    if(mysqli_num_rows($sql)){
      $status = 'success';
    }
  $sql = mysqli_query($connect,"SELECT * FROM user_log WHERE user_branch='$id' ");
    if(mysqli_num_rows($sql)){
      $status = 'success';
    }
  $sql = mysqli_query($connect,"SELECT * FROM staff WHERE staff_branch='$id' ");
    if(mysqli_num_rows($sql)){
      $status = 'success';
    }
  echo $status;
}


if($_POST['del_tab']){

  $tab = $_POST['del_tab'];
  $id = $_POST['del_id'];

  $del = mysqli_query($connect,"DELETE FROM $tab WHERE id = '$id' ");
   if($del)
    $status = 'success';
   else
     $status = 'err';

  echo $status;

}

if($_POST['disable_usr']){
   
   $id = $_POST['disable_usr'];
    $upd = mysqli_query($connect,"UPDATE user_log SET action_status='02' WHERE id='$id' ");
      if(mysqli_affected_rows($connect)){
         echo 'success';
      }
}

if($_POST['restore_usr']){
   
   $id = $_POST['restore_usr'];
    $upd = mysqli_query($connect,"UPDATE user_log SET action_status='01' WHERE id='$id' ");
      if(mysqli_affected_rows($connect)){
         echo 'success';
      }
}

if($_POST['get_receipt']){
  
  $loan_id = $_POST['get_receipt'];
  $pay_id = $_POST['pay_id'];

  $sql = mysqli_query($connect,"SELECT CONCAT(c.first_name,' ',c.last_name) as 'client', c.contacts, c.residance, l.date_entry, l.loan_amount, l.interest, p.amount_paid, p.receipt_no, p.pay_date FROM clients c, loan_entries l, loan_payments p WHERE c.id = l.client AND p.loan = l.id AND l.id='$loan_id' AND p.id = '$pay_id' ");
   $r = mysqli_fetch_array($sql);


  ?>
<div style="width:95%;margin:auto;">
  <span id="print_rpt" style="display:inline-block;width:100%;text-align: right;margin:10px 0 20px;"><img src="../img_file/print-icon.svg" width="20" height="20"></span>
    <div style="display: grid;grid-template-columns: 1fr 2fr 3fr;width: 100%;">
      <div style="background-color: #fd7e14;" id="bg_color"></div>
      <div style="text-align: center;">
        <span class="top_header">GEP</span>
        <span class="top_header_small">Finance</span>
      </div>
      <div style="background-color: #fd7e14;"></div>
    </div>
    <div class="note_header">RECEIPT</div>
    <div class="text-grid">
      <div>Date</div>
      <div><?php echo date('d/m/Y') ?></div>
    </div>
    <div class="text-grid">
      <div>No</div>
      <div><?php echo $r['receipt_no'] ?></div>
    </div>
  </div>
  <div class="address_to_wrap">
    <div class="address_header">Addressed To</div>
      <div class="note_addr_wrap">
         <div>Account Holder</div>
         <div><?php echo $r['client'] ?></div>
       </div>
       <div class="note_addr_wrap">
          <div>Contacts</div>
          <div><?php echo $r['contacts'] ?></div>
        </div>      
        <div class="note_addr_wrap">
          <div>Address</div>
          <div><?php echo $r['residance'] ?></div>
       </div>
       <div class="note_addr_wrap">
          <div>Pay Date</div>
          <div><?php echo date("d/m/Y",strtotime($r['pay_date'])) ?></div>
       </div>
  </div>
  <div style="display:grid" class="grid-4-items grid_header">
    <div>Issue Date</div>
    <div>Loan</div>
    <div>Interest</div>
    <div>Principal</div>
  </div>
  <div style="display:grid;margin:15px 0;" class="grid-4-items">
    <div><?php echo $r['date_entry'] ?></div>
    <div><?php echo number_format($r['loan_amount']) ?></div>
    <div><?php echo $r['interest'] ?></div>
    <div><?php echo number_format(loan_status($connect,$loan_id,'payable_loan')) ?></div>
  </div>
  <div style="display:grid;font-weight: bold;" class="grid-4-items">
    <div></div>
    <div></div>
    <div>Paid</div>
    <div><?php echo number_format($r['amount_paid']) ?></div>
  </div>
  <div style="display:grid;font-weight: bold;border-bottom: solid 1px #CCC;" class="grid-4-items">
    <div></div>
    <div></div>
    <div>Balance</div>
    <div><?php echo number_format(loan_status($connect,$loan_id,'')) ?></div>
  </div>
  <div>
    <?php
      $sql = mysqli_query($connect,"SELECT * FROM header_tpl");
       $r = mysqli_fetch_array($sql);
        echo '<span style="font-size:11px;">'.$r['address'].', '.$r['contacts'].', '.$r['email'].'</span>';
    ?>
  </div>
<?php
}

if($_POST['get_login']){

  ?>
  <div style="width:80%;margin:15px auto;">
        <div class="form_header">Login Require</div>
        <form method="post" name="form" id="login_form" method="post">
            <div class="form-group">
                <div class="label">User Name</div>
                 <input type="text" id="user" name="user" class="text-input" autocomplete="off" value="" />
           </div>
           <div class="form-group">
                <div class="label">Password</div>
                 <input type="text" id="pass" name="pass" class="text-input" autocomplete="off" />
           </div>
           <div class="form-group">
               <button type="submit" name="btnSubmit" class="button-input" id="submit">Submit</button>
          </div>
          <div class="form-group">
             <span id="login_response" style="color:#F00;"></span>
          </div>
      </form>
  </div>
<?php
}

if(isset($_POST['disable_staff'])){

    $id = $_POST['disable_staff'];
      $upd = mysqli_query($connect,"UPDATE staff SET status='00' WHERE id='$id' ");
    if($upd AND mysqli_affected_rows($connect)){
       echo 'success';
    }
}

if(isset($_POST['restore_staff'])){

    $id = $_POST['restore_staff'];
      $upd = mysqli_query($connect,"UPDATE staff SET status='01' WHERE id='$id' ");
    if($upd AND mysqli_affected_rows($connect)){
       echo 'success';
    }
}

?>