<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once('../xsert/connect.php');
require("sys_function.php");

if($_POST['sql']){
$sql = mysqli_query($connect,"SELECT * FROM loan_entries WHERE status='00' ");
$bal=0;
$count=0;
 while($rw=mysqli_fetch_array($sql)){
 	
 	$bal = loan_balances($connect,$rw[4],$rw[2],$rw[1],$rw[0],$rw[5],$rw[3],$rw[6],"balance").'<br>';
 	if($bal>0){
 		//$count+=1;
 		$sqls = mysqli_query($connect,"SELECT c.client_names, l.date_entry, l.loan_amount FROM loan_entries l, clients c WHERE c.id=l.client AND l.id='$rw[0]' ");
 		$rws = mysqli_fetch_array($sqls);
 		/*echo '<div style="width:90%;margin-bottom:10px;">'.$count.') '.$rws[0].' '.date('d-m-Y',strtotime($rws[1])).' '.number_format($rws[2]).' Balance '.number_format($bal).'</div>';*/
 		
 		$upd = mysqli_query($connect,"UPDATE loan_entries SET status='01' WHERE id='$rw[0]' ");
 		if(mysqli_affected_rows($connect)){
 			$count+=1;
 		}
 	}
  }

  echo $count.' Record(s) Affected...!!!';
}
?>