<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');

//custom function to filter
function filterData(&$str){
	 $str = preg_replace("/\t/","\\t", $str);
	 $str = preg_replace("/\r?\n/", "\\n", $str);
	 if(strstr($str,'"')) 
	 	$str = '"'.str_replace('"', '""',$str).'"';
}

$fileName = "loan-payments_".date('Y-m-d').".xls";

$fields = array('NO','MEMBER_ID','MEMBER NAMES','BRANCH','LOAN DATE','LOAN AMOUNT','DATE','AMOUNT PAID','BALANCE','STATUS');
$excelData = implode("\t", array_values($fields)). "\n";

$check_date = date('Y-m-d');

    if(isset($_GET)){

     $search_date = $_GET['search_date'];
      if($search_date)
        $check_date = date('Y-m-d',strtotime($search_date));

        $branch = $_GET['branch'];
        $pay_status = $_GET['pay_status'];
        $search_names = $_GET['mem_names'];
        $loan_pay_id = $_GET['id'];
        $loan = $_GET['loan'];

      if($loan_pay_id){
         $s = mysqli_query($connect,"SELECT c.id, CONCAT(c.first_name,' ', c.last_name) as 'client_name' FROM clients c, loan_entries l, loan_payments p WHERE c.id = l.client AND l.id = p.loan AND p.id='$loan_pay_id' ");
         $r = mysqli_fetch_array($s);
         $search_names = $r['client_name'];
       }
    }   

 $qry = "SELECT c.id, CONCAT(c.first_name,' ', c.last_name) as 'client_name', l.loan_amount, l.interest, l.period, l.date_entry, l.duration, p.amount_paid, p.pay_date, l.id as 'loan', p.id as 'payments', l.status, b.branch_name, c.data_id FROM clients c LEFT JOIN loan_entries l ON c.id = l.client LEFT JOIN loan_payments p ON p.loan = l.id LEFT JOIN branches b ON b.id = c.branch_id ";

             if(!$_SESSION['general_user'])
                       $qry .=" LEFT JOIN user_log u ON c.branch_id = u.user_branch ";
                  $qry .= " WHERE l.id != 'NULL' AND ";
               if(!$_SESSION['general_user'])
                    $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                   if($branch)
                       $qry .= " b.id = '$branch' AND ";
                     if($search_names)
                        $qry .= " CONCAT(c.first_name,' ', c.last_name) LIKE '%$search_names%' AND ";
                      if($loan)
                          $qry .= " l.id = '$loan' AND ";
                  $qry .= " 1 GROUP BY c.id, loan ORDER BY client_name, l.date_entry ASC ";

       $sql = mysqli_query($connect,$qry);

                  $count = 0;
                  $general_pay = 0;
                  $tot_loan = 0;
                  $display_paid = '1';
                  $display_due = '1';
                  $tot_due = 0;
                  $tot_paid = 0;

                  if($pay_status){
                     if($pay_status=='00')
                       $display_paid='';
                     if($pay_status=='01')
                       $display_due='';
                     
                  }

             if(mysqli_num_rows($sql)){

                    while($r = mysqli_fetch_array($sql)){

                      $status = '';
                      $return_row = '01';
                      $tot_loan += $r['loan_amount'];                     
                      
                if($r['status']=='00'){  
                  $q_str = "SELECT p.pay_date FROM loan_entries l , loan_payments p WHERE l.id = p.loan AND l.id='".$r['loan']."' ORDER BY p.pay_date DESC LIMIT 0,1";
                   $q = mysqli_query($connect,$q_str);
                     $rw = mysqli_fetch_array($q);
                      if(date('Y-m-d',strtotime($check_date)) > $rw[0])
                        $return_row = '00';
                      $date_null_val = NULL;
                   }

            if(!$search_date && $_POST['post_search'] OR $_GET['id']){

                    $status = 'Over-due';
                    $loan_bal = 0;
                    $acc_pmt = 0;

                    $period = status_period($r['date_entry'],$r['duration'],'');

                  for($x=1; $x<$period; $x++){
                     
                     $day_count = $x+1;     
                     $return_date = endCycle($r['date_entry'], $x, $r['duration']);

                      $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date='".date('Y-m-d',strtotime($return_date))."' ORDER BY pay_date ASC ");

                    $acc_pmt += loan_status($connect,$r['loan'],'pmt');

                    if(mysqli_num_rows($qry)){
                       while ($rs = mysqli_fetch_array($qry)) {
                    
                          $tot_pay += $rs['amount_paid'];
                          $loan_bal += loan_status($connect,$r['loan'],'pmt') - $rs['amount_paid'];
                          
                          $count += 1;
                          $status = 'Paid';

                          if($loan_bal<0){
                            $status = 'Advance Pay';
                            $loan_bal = 0;
                          }

                if($tot_pay == loan_status($connect,$r['loan'],'payable_loan'))
                     $x = $period;
                     $bal = $loan_bal;

                       $lineData = array($count, $r['data_id'],$r['client_name'],$r['branch_name'],date('d/m/y',strtotime($r['date_entry'])),number_format($r['loan_amount']),$return_date,number_format($rs['amount_paid']),number_format($loan_bal),$status); 
                       array_walk($lineData,'filterData');
                       $excelData .= implode("\t",array_values($lineData)) . "\n";
                  }
              }else{

              	$count += 1;
                     $loan_bal =  ($acc_pmt - $tot_pay);
                     
                     $status = 'Over-due';

                          if($loan_bal<0){
                            $status = 'Advance Pay';
                            $loan_bal = 0;
                          }
                      $bal = $loan_bal;

                     $lineData = array($count, $r['data_id'],$r['client_name'],$r['branch_name'],date('d/m/y',strtotime($r['date_entry'])),number_format($r['loan_amount']),$return_date,0,number_format($loan_bal),$status); 
                       array_walk($lineData,'filterData');
                       $excelData .= implode("\t",array_values($lineData)) . "\n";
                 }
              }
            $tot_bal += $bal;
            $general_pay = $tot_pay;

        } else if($r['pay_date']!=NULL && $display_paid && strtotime($r['date_entry']) <= strtotime($check_date)){

        	$status = 'Paid'; 
              $query = mysqli_query($connect,"SELECT * FROM loan_payments WHERE pay_date='$check_date' AND loan = '".$r['loan']."'");

                if(mysqli_num_rows($query) && $return_row=='01'){

                    $row = mysqli_fetch_array($query);
                    $count += 1;
                    $general_pay += $row['amount_paid'];
                    $tot_pay = 0;

                    $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }

                          $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                          $tot_bal += $loan_bal;

                    if($display_paid){
                      $tot_paid+=1;
                         if($loan_bal<0)
                             $loan_bal=0;
                       $lineData = array($count, $r['data_id'],$r['client_name'],$r['branch_name'],date('d/m/y',strtotime($r['date_entry'])),number_format($r['loan_amount']),date('d/m/y',strtotime($row['pay_date'])),number_format($row['amount_paid']),number_format($loan_bal),$status); 
                       array_walk($lineData,'filterData');
                       $excelData .= implode("\t",array_values($lineData)) . "\n";

                   }
                  }else{

                    $status = 'Over-due';
                    $tot_pay = 0;
                    
                         $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }

                          $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                          $tot_bal += $loan_bal;
                    
                    if($return_row=='01' && $display_due){
                         $count += 1;
                         $tot_due += 1;

                        $lineData = array($count, $r['data_id'],$r['client_name'],$r['branch_name'],date('d/m/y',strtotime($r['date_entry'])),number_format($r['loan_amount']),'-',0,number_format($loan_bal),$status); 
                       array_walk($lineData,'filterData');
                       $excelData .= implode("\t",array_values($lineData)) . "\n";
                     }
                    }
                  }else if($r['pay_date']==$date_null_val && $return_row=='01' && strtotime($r['date_entry']) <= strtotime($check_date)){

                    $status = 'Over-due';
                    $count += 1;
                    $tot_pay = 0;
                    

                         $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }

                          $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                          $tot_bal += $loan_bal;

                    if($display_due){
                      $tot_due += 1;

                        $lineData = array($count, $r['data_id'],$r['client_name'],$r['branch_name'],date('d/m/y',strtotime($r['date_entry'])),number_format($r['loan_amount']),'-',0,number_format($loan_bal),$status); 
                       array_walk($lineData,'filterData');
                       $excelData .= implode("\t",array_values($lineData)) . "\n";
                     }
              }
        }
}
 //headers for download
header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=\"$fileName\"");

echo $excelData;

?> 