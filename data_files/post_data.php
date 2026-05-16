<?php

session_start();
error_reporting(E_ALL ^ E_NOTICE);

require_once('../xsert/connect.php');
require_once('../data_files/sys_function.php');
date_default_timezone_set('Africa/Nairobi'); 


if($_POST['ac_id']){

      $expense = $_POST['expense_id'];
      $amt = $_POST['amt'];
      $date = $_POST['date'];
      $mop = $_POST['mop'];
      $acc_from = $_POST['mop_account'];
      $ac = $_POST['ac_id'];
      $paid_to = $_POST['paid_to'];
      $qty = $_POST['qty'];
      $expense_status = $_POST['expense_status'];
      $order_no = $_POST['order_cost_expenses'];
      $currency = $_POST['currency'];
      $cur_val = $_POST['cur_val'];
      $acc_to = $_POST['acc_to'];
      $acc_to_no = $_POST['acc_to_no'];
      $cur_index = $_POST['cur_select'];
      $cost = $_POST['cost'];

      $index=0;

       foreach($expense as $array){

         if($array!=''){

            if($qty[$index]==0)
                $qty[$index]=1;
            
            $inst = mysqli_query($connect,"INSERT INTO expense VALUES('".date('d').rand(1000,9999)."','$ac','$expense[$index]','".str_replace(",","",$cost[$index])."','".str_replace(",","",$qty[$index])."','$date','$mop','$acc_from','$acc_to','$acc_to_no','$paid_to','".$_SESSION['session_id']."') ");

            if($inst)
              $status = 'success';
            else
              $status = 'err';                
         }

         $index += 1;
       }

  echo $status;
}

if($_POST['edit_branch']){
  
  $id = $_POST['edit_branch'];
  $branch = $_POST['branch_name'];
  $addr = $_POST['address'];
  $email = $_POST['email'];
  $contacts = $_POST['contacts'];

  $upd = mysqli_query($connect,"UPDATE branches SET branch_name = '$branch', contact_email = '$email', contact_phone = '$contacts', address = '$addr' WHERE id='$id' ");

    if(mysqli_affected_rows($connect)){
        $status = 'success';   
    }

  echo $status;
}

if($_POST['member_id']){

  //define constant
  define("FILEREPOSITORY",'profile/');

      $id= $_POST['member_id'];


// --set image attributes for upload
  if(is_uploaded_file($_FILES['img_file']['tmp_name'])){

         $photo_name = $_FILES['img_file']['name'];
         $file_type = $_FILES['img_file']['type'];
         $photo_upd = $_FILES['img_file']['tmp_name'];
         
         //get the extension of the file
         $base = basename($photo_name);
         $extension = substr($base, strlen($base)-4, strlen($base));
         $allowed_extension = array(".jpg",".png",".jpeg",".PNG");

  if(in_array($extension,$allowed_extension)){
             if(!is_dir(FILEREPOSITORY.date("Y-m-d"))){
                  mkdir(FILEREPOSITORY.date("Y-m-d"));
                }

             $dir = date("Y-m-d").'/'.$id.'_'.strtotime(date('Y-m-d H:i:s')).$extension; //returns directory for uploading image
             move_uploaded_file($photo_upd,FILEREPOSITORY.date("Y-m-d").'/'.$id.'_'.strtotime(date('Y-m-d H:i:s')).$extension); //uploads file to respective directory
  }else{
        $response = 'Un-Supported Image File Format. <a href="" id="status_id">Try Again.!</a>';
    }
  //--//  
}

  if($response){

      $status = $response;
  }else{

$photo_dir = $_POST['photo_dir'];
 if($dir)
  $photo_dir = $dir;

    //insert query
    $query = "UPDATE clients SET first_name = '".ucfirst(strtolower($_POST['first_name']))."', last_name = '".ucfirst(strtolower($_POST['last_name']))."', contacts = '".$_POST['contacts']."', email = '".$_POST['email']."', residance = '".$_POST['residence']."', business_name = '".$_POST['occupy']."', gender = '".$_POST['gender']."', city = '".$_POST['city']."', date_modify = '".date("Y-m-d H:i:s")."', photo_dir = '$photo_dir', branch_id = '".$_POST['branch_details']."', data_id = '".$_POST['edit_id']."' WHERE id='$id' ";

    $upd = mysqli_query($connect, $query);
        
      if(mysqli_affected_rows($connect))
          $status = 'success';
  
  }

   echo $status;
 }


if($_POST['post_id']){

  $id = $_POST['post_id'];
  $loan_amt = str_replace(",", "", $_POST['loan_amount']);
  $fees_val = null;
  $last_fee = 0;
  $last_limit = 0;

$array = array(
    array('id' => '001', 'limit' => 200000, 'fee' => 10000),
    array('id' => '002', 'limit' => 500000, 'fee' => 15000),
    array('id' => '003', 'limit' => 2000000, 'fee' => 20000),
    array('id' => '004', 'limit' => 5000000, 'fee' => 30000),
    array('id' => '003', 'limit' => 5000001, 'fee' => 50000)
   );

foreach ($array as $item) {
    if ($loan_amt <= $item['limit'] ) {
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

  $upd = mysqli_query($connect,"UPDATE loan_entries SET date_entry='".$_POST['date']."', loan_amount = '$loan_amt', interest = '".$_POST['interest']."', duration = '".$_POST['duration']."', period = '".$_POST['period']."', loan_officer = '".$_POST['data_id']."', loan_fees='$fees_val' WHERE id='$id' ");

    if(mysqli_affected_rows($connect)){
         echo 'success';
       }else{
         echo 'err';
    }
}

if(isset($_POST['staff_no'])){

   $staff_no = $_POST['staff_no'];
   $first_name = $_POST['first_name'];
   $last_name = $_POST['last_name'];
   $gender = $_POST['gender'];
   $contacts = $_POST['contacts'];
   $email = $_POST['email'];
   $residance = $_POST['residance'];
   $job = $_POST['job_title'];
   $branch = $_POST['branch_details'];
   $nok = $_POST['nok'];
   $nok_contacts = $_POST['nok_contacts'];

   $inst = mysqli_query($connect,"INSERT INTO staff VALUES('".rand(10000,99999)."','$staff_no','$first_name','$last_name','$contacts','$email','$residance','$job','".date('Y-m-d')."', '$gender','01','$nok','$nok_contacts','$branch')");

      if($inst)
        echo 'success';
      else
        echo 'err';
}

if($_POST['edit_staff']){

  $id = $_POST['edit_staff'];

  $new_job = $_POST['new_job'];
  $job_title = $_POST['job_title'];

  if($new_job){
     $job_title = rand(1000,9999);
      $inst = mysqli_query($connect,"INSERT INTO staff_job VALUES('$job_title','$new_job','".date('Y-m-d H:i:s')."')");
       if($inst)
          $status = 'success';
        else
          $status = 'err';
       
  }

  $sql = mysqli_query($connect,"UPDATE staff SET staff_no = '".$_POST['edit_staffNo']."', first_name = '".ucfirst(strtolower($_POST['first_name']))."',last_name = '".ucfirst(strtolower($_POST['last_name']))."', contacts = '".$_POST['contacts']."', email = '".$_POST['email']."', residance = '".$_POST['residance']."', job = '$job_title', gender = '".$_POST['gender']."', nok = '".$_POST['nok']."', nok_contacts = '".$_POST['nok_contacts']."', branch_id = '".$_POST['branch_details']."' WHERE id='$id' ");

      if(mysqli_affected_rows($connect)){
        $status='success';
      }else{
        $status='err';
        $err=mysqli_error($connect);
      }
    echo $status.$err;
}

if($_POST['pay_loan']){

  $client_loan=$_POST['loan_client'];
  $date=$_POST['pay_date'];
  $loan_id = $_POST['loan'];
  $allow_dup = $_POST['allow_dup'];
  $loan_amount = $_POST['pay_loan'];
  $accTo = $_POST['accTo'];
  $accNo = $_POST['accNo'];
  $index=0;

  /*if(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan = '$loan_id' AND pay_date='$date'")) && !$allow_dup){
      $status = 'duplicate';
  }else*/
  foreach($loan_amount as $key=>$val){
    if($val){
      $receipt = date('m-').rand(100,999).rand(100,999).'/'.date('y');
      $id=date("d").rand(10000,99999);
      $sql = mysqli_query($connect,"INSERT INTO loan_payments VALUES ('$id','".str_replace(",", "",$val)."','".$date[$index]."','".$_SESSION['session_id']."','$client_loan','$loan_id','$receipt','".date('Y-m-d H:i')."',NULL,'','".$accTo[$index]."','".$accNo[$index]."') ");
          if($sql){
              $status='success_'.$id.'_'.loan_status($connect,$loan_id,'') . '-' . $accTo[$index] . '-' . $accNo[$index];
              if(loan_status($connect,$loan_id,'') <= 0){
                  mysqli_query($connect,"UPDATE loan_entries SET status='00' WHERE id='$loan_id' ");
               }
             }else{
              $status='err '.mysqli_error($connect);
         }
      }
    $index+=1;
  }
     
  echo $status;
} 

if($_POST['extend_loan']){
  
  $loan_id = $_POST['loan'];
  $period = $_POST['period'];
  $date = $_POST['new_date'];
  $bal_amt = $_POST['loan_balance'];
  $remark = $_POST['action'];
  $rand = date('d').rand(10000,99999);

    $sql = mysqli_query($connect,"SELECT l.client, l.interest, l.duration, l.loan_officer, g.guarantor, g.gender, g.email, g.residence, g.occupation, g.contacts, s.security, s.value, s.type, s.serial_no, s.desc FROM loan_entries l, loan_guarantor g, loan_security s WHERE l.id = g.loan AND l.id = s.loan AND l.id = '$loan_id' ");

    if(mysqli_num_rows($sql)){      

        $rw = mysqli_fetch_array($sql);
          $inst_qry = "INSERT INTO loan_entries VALUES('$rand','".$rw['client']."','$date','".str_replace(',','',$bal_amt)."','$remark','01','".date('Y-m-d H:i:s')."','".$rw['interest']."','".$rw['duration']."','$period','".$_SESSION['session_id']."',NULL,'".$rw['loan_officer']."',0)";

        $inst = mysqli_query($connect,$inst_qry);

        if($inst){
            mysqli_query($connect,"INSERT INTO loan_security VALUES('".date('d').rand(1000,9999)."','".$rw['security']."','".$rw['value']."','".$rw['serial_no']."','".$rw['desc']."','$rand')");
            mysqli_query($connect,"INSERT INTO loan_guarantor VALUES('".date('d').rand(1000,9999)."','".$rw['guarantor']."','".$rw['gender']."','".$rw['email']."','".$rw['residence']."','".$rw['occupation']."','$rand','".$rw['contacts']."') ");

            //update previous loan status to extended
            $upd = mysqli_query($connect,"UPDATE loan_entries SET status='03', modify_date='$date' WHERE id='$loan_id' ");
            if(mysqli_affected_rows($connect))
              echo 'success';
            else
              echo mysqli_error($connect);
        }else{
          echo 'err'.mysqli_error($connect);
        }
    }else{
        
        $sql = mysqli_query($connect,"SELECT * FROM loan_entries WHERE id='$loan_id' ");
          $rw = mysqli_fetch_array($sql);

        $inst_qry = "INSERT INTO loan_entries VALUES('$rand','".$rw['client']."','$date','".str_replace(',','',$bal_amt)."','$remark','01','".date('Y-m-d H:i:s')."','".$rw['interest']."','".$rw['duration']."','$period','".$_SESSION['session_id']."',NULL,'".$rw['loan_officer']."',0)";

       $inst = mysqli_query($connect,$inst_qry);
        if($inst){
            //update previous loan status to extended
              $upd = mysqli_query($connect,"UPDATE loan_entries SET status='03', modify_date='$date' WHERE id='$loan_id' ");
               if(mysqli_affected_rows($connect))
                 echo 'success';
               else
                 echo mysqli_error($connect);
        } else {
          echo 'err';
        }
    }
}


if($_POST['staff_id']){

    $staff = $_POST['staff_id'];
    $email = $_POST['email'];
    $usr = $_POST['usr_name'];
    $pwd = $_POST['pwd'];
    $usr_type = $_POST['usr_type'];
    $pwd2 = $_POST['pwd2'];
   
   if($pwd==$pwd2){
    $inst = mysqli_query($connect,"INSERT INTO user_log VALUES('".rand(1000,9999)."','$staff','$usr', MD5('$pwd'),'$usr_type',NULL,'01','".date('Y-m-d H:i:s')."','".$_POST['branch_details']."') ");
    if($inst){
      $status  = 'success';
    }else{
      $status = 'err';
      $response = mysqli_error($connect);
    }
  }else {
      $status = 'mis-match';
    }

    echo $status;
  }

if(isset($_POST['returnMop'])){
   
    $id = $_POST['returnMop'];
    $sql = mysqli_query($connect, "SELECT m.id, m.acc_name, m.acc_no FROM mop p, mop_accounts m WHERE p.id = m.mop AND p.id = '$id'");
         echo '<option value="" selected="selected">Select</option>';
      while($r = mysqli_fetch_array($sql)){
             echo '<option value="'.$r['id'].'">'.$r['acc_name'].' ('.$r['acc_no'].')</option>';
      }
 }
?>