<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Africa/Nairobi'); 

function return_usr($c,$usr){

  $sql = mysqli_query($c,"SELECT * FROM user_log WHERE id='$usr' ");
   if(mysqli_num_rows($sql)){
      $r = mysqli_fetch_array($sql);
        return $r['usr_name'];
   }else{
     return '-';
   }
} 

function po_address($c){
  
  $sql = mysqli_query($c,"SELECT * FROM header_tpl");
  $r = mysqli_fetch_array($sql);

   $add = '<div id="img_wrap"><img src="../img_file/'.$r[8].'" width="120px" height="100px"></div>
            <div id="addr_wrap">
              <span style="font-size:20px;display:inline-block;margin:5px 0;width:100%">'.$r[1].'</span>
                <span style="display:inline-block;margin:5px 0;width:100%;">'.$r[9].'&nbsp;'.$r[2].'&nbsp;'.$r[6].'<br><b>Tel:&nbsp;</b>'.$r[3].'<br><b>Website:&nbsp;</b>'.$r[4].',&nbsp;<b>Email:&nbsp;</b>'.$r[5].'</span></div>';
 
  return $add;
}

function return_period($start,$p) {

  $current = time();
  $loan_date_val = strtotime($start);
  $date_time = date_set_back($start, 1);
 // if($current >= $loan_date_val){
       
    $diff = abs($current - $loan_date_val);    
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));      
    
    if($p=='year'){
      $status=$years;
    }
    if($p=='month'){
      $status=($years*12)+$months;
    }else if($p=='day'){
      $status=(30*$months)+$days;
    }else{
      $status=$years;
    }

 return $status; 
}

function date_set_back($date, $days){ //return duration between months

  $now_time = strtotime($date);
  $selected_day = date("j",strtotime($date));
  $date_extension = "-".$days." day";
  $now_time = strtotime($date_extension, $now_time);
  return date("Y-m-d", $now_time);

}

function date_set_front($date, $days){ //return duration between months

  $now_time = strtotime($date);
  $selected_day = date("j",strtotime($date));
  $date_extension = "+".$days." day";
  $now_time = strtotime($date_extension, $now_time);
  return date("Y-m-d", $now_time);

}

function elaspe_period($start,$end, $p){ 
  
  $current = strtotime($start);
  $end_date = strtotime($end);
  
 // if($current >= $loan_date_val){    
    $diff = abs($current - $end_date);    
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));      
    
    if($p=='year'){
      $status=$years;
    }
    if($p=='month'){
      $status=($years*12)+$months;
    }else if($p=='day'){
      $status=(30*$months)+$days;
    }else{
      $status=$years;
    }

 return $status;
}

function status_period($start,$p,$s){
  
  $current = time();
  $loan_date = strtotime($start);
  
 // if($current >= $loan_date_val){ 
    $diff = abs($current - $loan_date);    
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));      
    
    if($p=='month'){
      $status=($years*12)+$months;
    }else if($p=='day'){
      $status=($months*30)+$days;
    }else{
      $status=$years;
    }
  return $status;
}

function loan_pay($c,$loan){
	$pay_amt=0;
	$sql = mysqli_query($c,"SELECT * FROM loan_payments WHERE loan='$loan' ");
	while($rw=mysqli_fetch_array($sql)){
		$pay_amt += $rw[1];
	}
  return $pay_amt;
}

function return_int($int_val,$duration,$principal,$loan){
   $interest = ($int_val/100) * $principal;
     if($duration=='day')
      $interest = ($loan * ($int_val/100)) / 30;
   return round ($interest,0);
}



function acc_principal($c,$l){
  $split = explode(", ", l);
  foreach ($split as $key ) {
  	   $qry = mysqli_query($c,"SELECT * FROM  loan_entries WHERE id='$key'");
          while($result=mysqli_fetch_array($qry)){
            $prinicple = round($result[0]/$result[1],0);
             for($x=0;$x<$result[1];$x++){

             }
          }
  }
return $principal;
}

function loan_period_date($index,$p,$d){
  $date_val = date("m",strtotime($d));
  $new_val = $index+$date_val;
  return date("m",strtotime($index));
} 

function add_months($months, DateTime $dateObject) 
    {
        $next = new DateTime($dateObject->format('Y-m-d'));
        $next->modify('last day of +'.$months.' week');

        if($dateObject->format('d') > $next->format('d')) {
            return $dateObject->diff($next);
        } else {
            return new DateInterval('P'.$months.'M');
    }
}

function endCycle($d1, $period_count, $d){

        if($d=='day'){

          $add_days = '+'.$period_count.' day';
        	$now_time = strtotime($d1);
          $now_time = strtotime($add_days, $now_time);
          $dateReturned = date("d-m-Y", $now_time);
        }else{

          if(date("m",strtotime($d1))=='01' && date("d",strtotime($d1))>28 ){
             $date = new DateTime(date("Y",strtotime($d1)).'-01-28');
           }else{
             $date = new DateTime($d1);
           } 
                  
           // call second function to add the months
           $newDate = $date->add(add_months($period_count, $date));        
           //formats final date to Y-m-d form
           $dateReturned = $newDate->format('d-m-Y'); 
          
      }
  return $dateReturned;
}

function interest_paid($c,$l){
	
                    $start_date="";
                   $qry = mysqli_query($c,"SELECT loan_amount, interest, period, date_entry, duration, id, status FROM  loan_entries WHERE id='$l'");
                     
                      while($rw=mysqli_fetch_array($qry)){
                        $pay_int=0;
                        if($rw[6]=='00'){
                             $status_period = $rw[2];                              
                              for($i=0;$i<$status_period;$i++){
                                  $pay_int += ($rw[1]/100)*$rw[0]; 
                                } 
                            $tot_int += $pay_int;
                        }
                        else{
                        $status_period = $rw[2]-return_period($rw[3],$rw[4]);
                         if($rw[2]==$status_period){
                                   $bal=0;
                                }else{
                                    for($i=0;$i<return_period($rw[3],$rw[4]);$i++){
                                        if($i==0){
                                           $start_date=$rw[3];
                                           }
                                         $final = endCycle($start_date, '1', $rw[4]);
                                         
                                         $sql_date = "SELECT * FROM loan_payments WHERE loan='$key' AND ";
                                             if($rw[4]=='day'){
                                              $sql_date .= " pay_date='".date("Y-m-d",strtotime($final))."' AND ";
                                            }
                                            if($rw[4]=='month'){
                                              $sql_date .= " monthname(pay_date)='".date("F",strtotime($final))."' AND ";
                                                }
                                                $sql_date .= " 1 ";
                                            $qry = mysqli_query($connect,$sql_date);
                                            while($result=mysqli_fetch_array($qry)){
                                              $tot_int += ($rw[1]/100)*$rw[0]; 
                                              $amt_paid = $result[1];
                                            }
                                        echo date("d-m-Y",strtotime($final)).' '.$rw[4].'<br/>';
                                      $start_date=$final;
                                     } 
                                }
                         
                      }                     
                  }
 }

 function expense_daily_totals($c,$e,$d){
  $sql = mysqli_query($c,"SELECT t.amount FROM expense e, expense_entries t WHERE e.id=t.expense AND e.expense_acc='$e' AND t.pay_date='$d'");
  while($rw=mysqli_fetch_array($sql)){
    $tot += $rw[0];
  }
  return $tot;
 }

function loan_balances($c,$period,$int,$loan,$id,$date,$d,$status,$a){
/*if($status=='00'){
            $period_status=0;
          }else{
          $period_status = ($p-return_period($date,$d));//returns period remain for loan
         }*/
if($status=='00'){

    $sql_query = mysqli_query($c,"SELECT pay_date FROM loan_payments WHERE loan='$id' ORDER BY pay_date DESC LIMIT 0,1 ");

     $results = mysqli_fetch_array($sql_query);
      $period_status = elaspe_period($date,$results[0], $d);
      $loop=$period_status;
    } else {         
          $period_status = ($period-return_period($date,$d));//returns period remain for loan
          $loop = $p-$period_status;
}   

  for($i=0; $i<$loop; $i++){

       if($i==0){
              $start_date=$date;
            } 

        $final = endCycle($start_date, '1', $d);
        $loop_date = endCycle($final, '1', $rw[3]);

        if($d=='day'){
            $sql=mysqli_query($c,"SELECT * FROM loan_payments WHERE loan='$id' AND  pay_date='".date("Y-m-d",strtotime($final))."'");
            if(mysqli_num_rows($sql)){
              while($rws = mysqli_fetch_array($sql)){
                if($i==0){ 
                   $actual_interest = ($loan*($int/100))/$period;                                    
                   $bal =  $loan-($rws[1]-$actual_interest);
                   if($rws[1]==($actual_interest)){
                     $int_paid += $rws[1];
                   }else if($rws[1]>$actual_interest){                                       
                     $int_paid += $actual_interest;                                        
                   }else if($rws[1]<$actual_interest){
                      $int_paid = $rws[1];
                      $acc_int = $actual_interest-$rws[1];
                   }                   
                 }else{
                    $actual_interest = ($bal*($int/100))/$period;
                    $acc_int += $actual_interest;
                   if($rws[1]==($acc_int)){
                     $int_paid += $rws[1];
                     $bal = $bal;
                     $acc_int=0;
                   }else if($rws[1]>$acc_int){ 
                     $int_paid += $acc_int;           
                     $bal = ($bal+$acc_int)-$rws[1];
                     $acc_int=0;                                          
                   }else if($rws[1]<$acc_int){
                      $int_paid += $rws[1];
                      $acc_int = $acc_int-$acc_int;                      
                   }
                   
              }
            $total += $rws[1];
            if($bal<=0){
                    $loan=0;
                    $interest=0;
                    $acc_int=0;
                 }                        
          }
       }else{
              if($bal>0){
                $interest = ($bal * ($int/100))/$period;
              }else{
                $interest = ($loan * ($int/100))/$period;
                $bal=$loan;
              } 
              $acc_int += $interest;    
    }
  }
  if($d=='month'){
            $sql=mysqli_query($c,"SELECT * FROM loan_payments WHERE loan='$id' AND pay_date >= '".date('Y-m-d',strtotime($final))."' AND pay_date < '".date('Y-m-d',strtotime($loop_date))."'  ");
            if(mysqli_num_rows($sql)){
              while($rws = mysqli_fetch_array($sql)){
                if($i==0){ 
                   $actual_interest = $loan*($int/100);  
                                    
                   $bal =  $loan-($rws[1]-$actual_interest-$acc_int);
                   if($rws[1]==($actual_interest)){
                     $int_paid+=$rws[1];
                   }else if($rws[1]>$actual_interest){                                       
                     $int_paid += $actual_interest;                                        
                   }else if($rws[1]<$actual_interest){
                      $int_paid = $rws[1];
                      $acc_int=$actual_interest-$rws[1];
                      $bal=$loan;
                   }
                   
                  }else{
                    $actual_interest = $bal*($int/100);
                    $acc_int += $actual_interest;
                   if($rws[1]==($acc_int)){
                     $int_paid += $rws[1];
                     $bal = $bal;
                     $acc_int=0;
                   }else if($rws[1]>$acc_int){ 
                     $int_paid += $acc_int;           
                     $bal = ($bal+$acc_int)-$rws[1];
                     $acc_int=0;                                          
                   }else if($rws[1]<$acc_int){                      
                      $int_paid += $rws[1];
                      $acc_int = $acc_int-$rws[1];                                            
                   }
                   
              }
            $total += $rws[1];
            if($bal<=0){
                    $loan=0;
                    $interest=0;
                    $acc_int=0;
                 }                        
          }
       }else{
              if($bal>0){
                $interest = $bal * ($int/100);
              }else{
                $interest = $loan * ($int/100);
                $bal=$loan;
              } 
              $acc_int += $interest;    
    }
  }
  $start_date=$final;

  if($bal<=0){
    mysqli_query($c,"UPDATE loan_entries SET status='00' WHERE id='$id' ");
  }
}
   if($a=='interest'){
     return $int_paid;
   } 

   if($a=='payments') {
    return ($total-$int_paid);
   }

   if($a=='balance'){
      if($bal>0){
          $current=($acc_int+$bal);
          }else{
          $current=$bal;
        }  
     return $current;
   }else if(!$a){
     return $total;
   }
}

function loan_status($connect,$loan_id,$status){

$qry = mysqli_query($connect,"SELECT * FROM loan_entries WHERE id='$loan_id' AND status!='00' ");
$row = mysqli_fetch_array($qry);
 //$sql = mysqli_query($connect,"SELECT l.loan_amount, l.interest, l.duration, l.period, l.date_entry, SUM(p.amount_paid) as 'payments', l.status FROM loan_entries l LEFT JOIN loan_payments p ON l.id = p.loan WHERE l.id='$loan_id' AND p.pay_date < '".date('Y-m-d',strtotime(endCycle($row['date_entry'], ($row['period']+7), $row['duration'])))."'");

 $sql = mysqli_query($connect,"SELECT l.loan_amount, l.interest, l.duration, l.period, l.date_entry, SUM(p.amount_paid) as 'payments', l.status FROM loan_entries l LEFT JOIN loan_payments p ON l.id = p.loan WHERE l.id='$loan_id' ");
    
    if(mysqli_num_rows($sql)){

      $r = mysqli_fetch_array($sql);
     
         //return loan$loan=0;
         $loan= $r['loan_amount'];
         $int = $r['interest'];
         $duration = $r['duration'];
         $p = $r['period'];
         $loan_payments = $r['payments'];
         $date_entry = $r['date_entry'];
      
      $pmt = round(($loan * ($int/100)+$loan) / $p);
      $loan_principal = (($loan * ($int/100))) + $loan;
      $period = status_period(date_set_back($date_entry, 1),$duration,'');
      

      if(($r['period'] - $period) < 0 && ($r['period'] - $period) >= -7)
            $acc_pmt_bal = $loan_principal - $loan_payments;
          else
            $acc_pmt_bal = ($period * $pmt) - $loan_payments;

       if($status=='payments'){
         return $loan_payments;
       }else if($status=='payable_loan'){
         return $loan_principal;
       }else if($status == 'pmt'){
         return $pmt;
       }else if($r['status']=='03'){
         return 0;
       }else if($status=='extend'){
        $extend_date = date('Y-m-d',strtotime(endCycle($date_entry, ($p+7), $r['duration'])));
         $sql2 = mysqli_query($connect,"SELECT SUM(p.amount_paid) as 'payments' FROM loan_entries l LEFT JOIN loan_payments p ON l.id = p.loan WHERE l.id='$loan_id' AND p.pay_date < '$extend_date'");
           $row = mysqli_fetch_array($sql2);
           $loan_payments = $row['payments'];
           $acc_pmt = $p * $pmt;
           $acc_pmt_bal = $acc_pmt - $loan_payments;
          return $acc_pmt_bal+$pmt;
       }else{
        if($loan_principal - $loan_payments<=0){
          return 0;
          mysqli_query($connect,"UPDATE loan_entries SET status='00' WHERE id='$loan_id' ");
        }else{
          if($period > $r['period'])
              return $acc_pmt_bal;
            else
              return $loan_principal - $loan_payments;
         }
      }
   }
}

function loan_performance($connect,$status,$month,$year){

  $total = 0;

  $qry = "SELECT l.id as 'loan_id', l.loan_amount, l.interest, l.duration, l.period, l.date_entry, l.status, l.loan_fees FROM clients c, loan_entries l, branches b ";
    if(!$_SESSION['general_user'])
       $qry .= ", user_log u ";
         $qry .= " WHERE c.id = l.client AND c.branch_id = b.id AND date_format(l.date_entry,'%Y') = '$year' AND ";
      if(!$_SESSION['general_user'])
             $qry .= " u.user_branch = c.branch_id AND ";
          if(!$_SESSION['general_user'])
                       $qry .= "u.id='".$_SESSION['session_id']."' AND ";
            if($month)
                $qry .= " monthname(l.date_entry) = '".$month."' AND ";
                  $qry .= ' 1 ';
          $sql = mysqli_query($connect,$qry);

      while ($r = mysqli_fetch_array($sql)) {
        $count+=1;
        if($status=='payments')
          $total += loan_status($connect,$r['loan_id'],$status);
        else if($status=='total_loans' AND $r['status'] != '03'){
          $total += $r['loan_amount'];
        } else if($status=='borrowers' AND $r['status'] != '03'){
          $total += 1;
        } else if($status == 'interest'){
          $total += return_int($r['interest'],$r['duration'],0,$r['loan_amount']);
        } else if($status == 'loan_fees'){
          $total += $r['loan_fees'];
        } else if($status == 'default_status'){
           if($r['status']=='02'){
             $total+=1;
           }
        } else if($status=="due-balance"){
          if($r['status']=='01' || $r['status']=='02')
            $total += loan_status($connect,$r['loan_id'],$status);
        }
      }
   return $total;
}

function return_date($date, $days){ //return duration between months

  $now_time = strtotime($date);
  $selected_day = date("j",strtotime($date));
  $date_extension = "+".$days." day";
  $now_time = strtotime($date_extension, $now_time);
  return date("Y/m/d", $now_time);
}

function extend_loan($connect,$loan){
$sql = mysqli_query($connect,"SELECT * FROM loan_entries WHERE id='$loan' ");
  $row = 0;  
    while($r = mysqli_fetch_array($sql)){
       $id = $r['id'];              
       $period = status_period($r['date_entry'],$r['duration'],'');
       $loanDate = $r['date_entry'];
       $loan =  loan_status($connect,$id,'extend');
       $extend_date = date('Y-m-d',strtotime(endCycle($loanDate, ($r['period']+8), $r['duration'])));
       $modify_date = date('Y-m-d',strtotime(endCycle($extend_date, $r['period'], $r['duration'])));
       $loanId = 'P'.date('dm').rand(1000,9999); 
         if($period > $r['period']){
             $inst =  mysqli_query($connect,"INSERT INTO loan_entries VALUES('$loanId','".$r['client']."','$extend_date','$loan','','01','".date('Y-m-d H:i:s')."','".$r['interest']."','".$r['duration']."','".$r['period']."','".$r['user']."',NULL,'".$r['loan_officer']."','0')");
                    if(mysqli_affected_rows($connect)){
                         mysqli_query($connect,"UPDATE loan_entries SET status='03', modify_date = '".date_set_back($extend_date, 1)."' WHERE id = '$id'");                    
          mysqli_query($connect,"UPDATE loan_payments SET loan='$loanId' WHERE loan='".$r['id']."' AND pay_date >= '$extend_date' ");
        }
     }
  }
}

function summaryTot($c,$date,$account,$tab,$date_feild){
  $totAmt=0;
   $sql = mysqli_query($c, "SELECT * FROM $tab WHERE $date_feild='".date('Y-m-d',strtotime($date))."' ");
    if(mysqli_num_rows($sql)){
       while($r = mysqli_fetch_array($sql)){
           if($account=='loan_fees')
             $totAmt += $r['loan_fees'];
           if($account=='loan')
             $totAmt += $r['amount_paid'];
       }
    }
  return $totAmt;
}
?>