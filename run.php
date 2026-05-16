<?php
require("xsert/connect.php");
require('data_files/sys_function.php');

$rows=0;
/*$sql = mysqli_query($connect,"SELECT * FROM clients ");
$count=0;
while($r=mysqli_fetch_array($sql)){
 $q = mysqli_query($connect,"UPDATE clients SET data_id='$r[0]' WHERE id='$r[0]' ");
 if(mysqli_affected_rows($connect)){
  		$count+=1;
  }
}*/



/*$sql = mysqli_query($connect,"SELECT * FROM loan_entries WHERE status !='00' ");

 while($r = mysqli_fetch_array($sql)){
     
     //return loan$loan=0;
      $loan= $r['loan_amount'];
      $int = $r['interest'];
      $duration = $r['duration'];
      $period = $r['period'];
      $loan_payments = 0;
      $acc_pmt=0;
      $acc_pmt_bal = 0;
       
        if($period==0)
           $period=1;
      $pmt = round(($loan * ($int/100)+$loan) / $period);
      $loan_principal = (($loan * ($int/100))) + $loan;

  $period = status_period(date_set_back($r['date_entry'], 1),$r['duration'],'');
  
  $day_count = 0;
  $count=0;
  $rows = 0;
  $acc_day=0;
  
  for($x=0; $x <= $period; $x++){
       
   $day_count += 1; 
   $count+=1;
   
     $get_date = endCycle($r['date_entry'], $x, $r['duration']);

          if($x==0){
               $acc_pmt=0;
               $period = $period-1;
            }else{
              $acc_pmt += $pmt;
           }

    $qry = mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['id']."' AND pay_date='".date("Y-m-d",strtotime($get_date))."' ");

      if(mysqli_num_rows($qry)){
        while($rws = mysqli_fetch_array($qry)){
                $loan_payments += $rws[1];                
           }
         $acc_pmt_bal += $loan_principal - $loan_payments;
            if($loan_payments >= $loan_principal){
               $x = $period;
               $acc_pmt_bal = 0;
             }  
           }else{
               $acc_pmt_bal = $acc_pmt - $loan_payments;
          }
          
      if($day_count==30){
          $acc_day+=1;
          $day_count=0;
      }
      
}


echo $acc_day.'/'.$period.' - '.floor($period / 30 ).'<br>';

    if($acc_day!=0 AND ($acc_day == floor($period / 30 ))){
        $day_count=0;
         echo $acc_day . ' Due Balance ' . ' - ' . number_format($acc_pmt_bal) .   ' - ' . date('Y-m-d H:i:s',strtotime($get_date)) . '<br>'; 
        if($r['status']!='03'){
           $inst = mysqli_query($connect,"INSERT INTO loan_overdue VALUES('".rand(10,99).date('dm').rand(10,99)."','".$r['id']."','$acc_pmt_bal','".date('Y-m-d H:i:s',strtotime($get_date))."','".date('Y-m-d H:i:s')."' )");
            if($inst)
                $rows += 1;
           }
        }    
 }

 $sql = mysqli_query($connect,"SELECT * FROM loan_entries   WHERE status ='02' AND client='13501229' ");
  $row = 0;  
    while($r = mysqli_fetch_array($sql)){
       $id = $r['id'];              
       $period = status_period($r['date_entry'],$r['duration'],'');
       $loanDate = $r['date_entry'];
       $loan =  loan_status($connect,$id,'extend');
       $extend_date = date('Y-m-d',strtotime(endCycle($loanDate, $r['period'], $r['duration'])));
       $modify_date = date('Y-m-d',strtotime(endCycle($extend_date, $r['period'], $r['duration'])));
       $loanId = 'P'.date('dm').rand(1000,9999); 
         if($period>$r['period']){
           //for ($i=30; $i <= $period; $i+=$r['period']) {
            $i=30;
                  if (($period-$i) >= ($r['period']) ) {
                     $inst =  mysqli_query($connect,"INSERT INTO loan_entries VALUES('$loanId','".$r['client']."','$extend_date','$loan','','02','".date('Y-m-d H:i:s')."','".$r['interest']."','".$r['duration']."','".$r['period']."','".$r['user']."',NULL,'".$r['loan_officer']."','0')");
                       if(mysqli_affected_rows($connect)){
                         $rows+=1;
                           mysqli_query($connect,"UPDATE loan_entries SET status='03', modify_date = '".date_set_front($extend_date, 1)."' WHERE id = '$id'");
                         }
                   }else{
                     $inst =  mysqli_query($connect,"INSERT INTO loan_entries VALUES('$loanId','".$r['client']."','$extend_date','$loan','','01','".date('Y-m-d H:i:s')."','".$r['interest']."','".$r['duration']."','".$r['period']."','".$r['user']."',NULL,'".$r['loan_officer']."','0')");
                         if(mysqli_affected_rows($connect)){
                           $row+=1;
                           mysqli_query($connect,"UPDATE loan_entries SET status='03', modify_date = '".date_set_front($extend_date, 1)."' WHERE id = '$id'");
                    }
              mysqli_query($connect,"UPDATE loan_payments SET loan='$loanId' WHERE loan='".$r['id']."' AND pay_date >= '$extend_date' ");
            }

         /*Output remainder of 19
              if (($period - ($r['period']*2)) % 30 !== 0) {
                $loanId = 'P'.date('dm').rand(1000,9999);
                $extend_date = date('Y-m-d',strtotime(endCycle($r['date_entry'], $r['period'], $r['duration'])));
                  
                if(mysqli_affected_rows($connect)){
                    $rows+=1;
                    mysqli_query($connect,"UPDATE loan_payments SET loan='$loanId' WHERE loan='$id' AND pay_date >= '$extend_date' ");
                 } 
                 echo $period % 30 . "<br>";
               }

       } 
      //mysqli_query($connect,"UPDATE loan_entries SET status='03', modify_date = '".date_set_front($extend_date, 1)."' WHERE id = '$id'");
    }
  echo $rows.' & '.$row.' Row(s) Affected...!!!';*/
  $rw = 0;
   $sql = mysqli_query($connect, "SELECT * FROM loan_payments_old ");
     while($row = mysqli_fetch_array($sql)){
        $upd = mysqli_query($connect,"UPDATE loan_payments SET loan='".$row['loan']."' WHERE id = '".$row['id']."' AND loan LIKE '%P%' ");
         if(mysqli_affected_rows($connect)){
            $rows+=1;
           $sql2 = mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan = '".$row['loan']."' AND date_entry BETWEEN '2024-02-22 00:00:00' AND  '2024-02-26 00:00:00' ");
           if(mysqli_num_rows($sql2)){
              $r = mysqli_fetch_array($sql2);             
                mysqli_query($connect,"UPDATE loan_payments SET loan='".$row['loan']."' WHERE loan = '".$r['loan']."' ");
                  $rw+=1;
           }
        }
     }
      /*A         S
     -0001      -0001
     -P0001     -0001
     -0002      -0002
     -0003      -0003

     -P0001*/
echo $rows.' & '.$rw.' Row(s) Affected...!!!';

?>