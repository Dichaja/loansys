<?php
require("../xsert/connect.php");
require("../data_files/sys_function.php");

error_reporting(E_ALL ^ E_NOTICE);

header('Content-Type: application/json');

        $count=0;
        
        /*foreach($month_array as $month_index){
          $count += 1;
          $month_tot = 0;
          $sql = "SELECT loan_amount, id, interest, period, date_entry  FROM loan_entries WHERE ";            
                if($month != ''){
                  $sql .= " monthname(date_entry)='$month' AND ";
                 }else{
                  $sql .= " monthname(date_entry)='$month_index' AND ";
                 }
                  if($year != ''){
                    $sql .= " date_format(date_entry,'%Y') = '$year' AND ";
                   }
                    $sql .= " 1 ";
              $qry = mysqli_query($connect,$sql); 
              $qry2 = mysqli_query($connect,$sql); 
          while($rw = mysqli_fetch_array($qry)){
            $month_tot += $rw[0];
          }
         */
$annual_days = 365;
$yr=$_POST['year'];
$prev_yr='';
for($y=1;$y<=5;$y++){
$prev_yr = $yr-$y; 

        $select = "SELECT amount_paid, loan FROM loan_payments WHERE ";
                if($month_qry != '')
                      $select .= " monthname(pay_date) = '$month_qry' AND ";                    
                  if($yr != '')
                      $select .= " date_format(pay_date,'%Y') = '$yr' AND ";                     
                       $select .= " 1 ";
                     $sqls = mysqli_query($connect,$select);

                     $pay=0;
                     $amt=0;
                     while($rw=mysqli_fetch_array($sqls)){
                       $pay += $rw[0];                                             
                     }
                  {
                  
                  $total_pay += $pay;
               }
        }
      print json_encode($total_pay);
?>