<?php

session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once('../xsert/connect.php');
require_once('sys_function.php');

if($_POST['data_manage']){

  $id = $_POST['loan_id'];
  $edit_date = $_POST['pay_date'];
  $reason = $_POST['edit_reason'];
  $edit_pay = str_replace(",","",$_POST['edit_pay']);
  $data_manage = $_POST['data_manage'];

  if($data_manage=='edit')
    $header = 'Loan Pay Adjustments';
  if($data_manage=='delete')
    $header = 'Delete Loan';

  $sql = mysqli_query($connect,"SELECT CONCAT(c.first_name,' ',c.last_name) as 'client', c.data_id, l.loan_amount, l.date_entry, p.amount_paid, p.pay_date, l.id as 'loan', b.branch_name FROM clients c, loan_entries l, loan_payments p, branches b WHERE c.branch_id = b.id AND c.id = l.client AND p.loan = l.id AND p.id = '$id' ");
   $r = mysqli_fetch_array($sql);

 if(!$reason){
  if($data_manage=='edit'){
   $upd = mysqli_query($connect,"UPDATE loan_payments SET amount_paid='$edit_pay', pay_date='$edit_date' WHERE id='$id' ");
     if(mysqli_affected_rows($connect))
      $status='success';
    }

   if($data_manage=='delete'){
      $del = mysqli_query($connect,"DELETE FROM loan_payments WHERE id='$id' ");
        if($del){
          if(loan_status($connect,$r['loan'],'') > 0){
                  mysqli_query($connect,"UPDATE loan_entries SET status='01' WHERE id='".$r['loan']."' ");
              }
          $status='success - '.loan_status($connect,$r['loan'],'');
        }
        else
          $status='err';
   }

 }else{
    

    if($data_manage=='edit'){

       $approve_tag = '<div style="margin: 15px 0;width:100%;"><a href="https://gepfinance.com/loandb/data_files/data_verify.php?temp_usr=1&pay_edit='.$edit_pay.'&pay_date='.$edit_date.'&pay_id='.$id.'&loan='.$r['loan'].'" style="text-decoration:none"><span style="border-radius:5px;padding:7px;color:#fff;font-weight:bold;background-color: #fd7e14;">Approve Adjustment</span></a></div>';
    }

    if($data_manage=='delete'){
      $del = mysqli_query($connect,"DELETE FROM loan_payments WHERE id='$id' ");
        if($del){
          if(loan_status($connect,$r['loan'],'') > 0){
                  mysqli_query($connect,"UPDATE loan_entries SET status='01' WHERE id='".$r['loan']."' ");
              }
          $status='success_'.loan_status($connect,$r['loan'],'');
        }
        else
          $status='err';

        $approve_tag = '<div style="margin: 15px 0;width:100%;"><a href="https://gepfinance.com/loandb/data_files/data_verify.php?temp_usr=1&del_pay='.$edit_pay.'&pay_date='.$edit_date.'&pay_id='.$id.'&issue_date=" style="text-decoration:none"><span style="border-radius:5px;padding:7px;color:#fff;font-weight:bold;background-color: #fd7e14;">Approve Delete</span></a></div>';
     }
  }

$content = '<div style="margin:10px auto;width:100%;">
 <h2>Data Modify Notification</h2>
  <div style="background-color: #fd7e14;height:30px;width:100%"></div>
  <div style="margin:10px 0;"><b>Action: '.$header.'</b></div>
  <div style="display:flex;"><div style="flex:50%;">Member</div><div style="flex:50%; padding:0 5px;">'.$r['client'].'</div></div>
   <div style="display:flex;"><div style="flex:50%">Branch</div><div style="flex:50%; padding:0 5px;">'.$r['branch_name'].'</div></div>
    <div style="display:flex;flex-wrap:wrap"><div style="flex:50%">Loan</div><div style="flex:50%">'.number_format($r['loan_amount']).'</div></div>
    <div style="display:flex;"><div style="flex:50%;">Issue Date</div><div style="flex:50%; padding:0 5px;">'.date('d/m/Y',strtotime($r['date_entry'])).'</div></div>
    <div style="width:100%;border-bottom:solid 1px #fd7e14;margin:10px 0;font-weight:bold;">Previous Pay Details</div>
    <div style="display:flex;"><div style="flex:50%;">Pay Date</div><div style="flex:50%; padding:0 5px;">'.date('d/m/Y',strtotime($r['pay_date'])).'</div></div>
    <div style="display:flex;"><div style="flex:50%;">Amount Paid</div><div style="flex:50%; padding:0 5px;">'.number_format($r['amount_paid']).'</div></div>';

if($data_manage=='edit'){

$content .= '<div style="width:100%;border-top:solid 1px #fd7e14;margin:10px 0;font-weight:bold;">New Adjustment</div>
    <div style="display:flex;"><div style="flex:50%;">Pay Date</div><div style="flex:50%; padding:0 5px;">'.date('d/m/Y',strtotime($edit_date)).'</div></div>
    <div style="display:flex;"><div style="flex:50%;">Amount Paid</div><div style="flex:50%;">'. number_format($edit_pay).'</div></div>';
  }

if($approve_tag)
    $content .= '<div style="display:flex;border-top:solid 1px #fd7e14"><div style="flex:50%;font-weight:bold;">Reason</div><div style="flex:50%; padding:0 5px;">'.$reason.'</div></div>'.$approve_tag.'</div>';
  
   echo $status;
 }
 
 $content .= '<div style="display:flex; border-top:solid 1px #fd7e14; "><div style="flex:50%;font-weight:bold;">User, '.$_SESSION['sess_user'].'</div><div style="flex:50%; padding:0 5px;"> Modify Date '.date('d/m/Y').'</div></div>';

$html_content = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body style="background-color:#eee;font-size:12px;font-family: Segoe UI;padding: 5px; ">
<div class="mail_wrap">
  <div style="width:80%;font-size:12px;padding: 5px 0px 5px;margin:10px auto;">'.$content.'</div>
</div>
</body>
</html>';

ini_set("include_path", '/home/opacworld/php:' . ini_get("include_path") );
include('Mail.php');
require 'Mail/mime.php';

$host = "localhost";
$username = "opacworld";
$password = "G5#_k*m%01";

$to = 'obacheisaac@gmail.com, workusent@gmail.com';
$from = 'GEP Info Center<info@gepfinance.com>';
$subject  = 'Data Management - '.date('d/m/Y');

$headers = array ('From' => $from,
'To' => $to,
'Subject' => $subject);

// create MIME object
$mime = new Mail_mime;

// add body parts
$text = 'Text version of email';
$mime->setTXTBody($text);

$html = '<html><body>HTML version of email</body></html>';
$mime->setHTMLBody($html_content);

// get MIME formatted message headers and body
$body = $mime->get();

// get MIME formatted message headers and body
$body = $mime->get();
$headers = $mime->headers($headers);

$smtp = Mail::factory('smtp',
array ('host' => $host,
'auth' => false,
'username' => $username,
'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
   $status = $mail->getMessage();
}

echo $status;

?>