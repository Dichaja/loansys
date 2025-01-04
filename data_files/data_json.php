<?php
require_once("../xsert/connect.php");
require_once("sys_function.php");
error_reporting(E_NOTICE ^ E_ALL);

header('Content-Type: application/json');

if(isset($_GET['year'])){
  $year = $_GET['year'];
}else{
  $year = date('Y');
}

/*$qry = "SELECT SUM(l.loan_amount) as 'total_loan', monthname(l.date_entry) as 'month' FROM clients c, loan_entries l, branches b ";
    if(!$_SESSION['general_user'])
       $qry .= ", user_log u ";
         $qry .= " WHERE c.id = l.client AND c.branch_id = b.id AND ";
      if(!$_SESSION['general_user'])
             $qry .= " u.user_branch = c.branch_id AND ";
          if(!$_SESSION['general_user'])
                $qry .= "u.id='".$_SESSION['session_id']."' AND ";
             if($year)
                 $qry .= " date_format(l.date_entry,'%Y') = '$year' AND ";
              $qry .= " l.id NOT LIKE 'P%' AND 1 GROUP BY month ORDER BY date_format(l.date_entry,'%m') ";
     $sql = mysqli_query($connect,$qry);

     if(mysqli_num_rows($sql)){
          while($r=mysqli_fetch_array($sql)){
             $array_loan[]=array('account'=>'Loans','month'=>$r['month'],'amount'=>$r['total_loan'],'month_bal'=>round(loan_performance($connect,'due-balance',$r['month'],$year)), 'year'=>$year);
            }
          }*/ 
   $array_loan = array();
   $qry = "SELECT monthname(l.date_entry) as 'month' FROM loan_entries l WHERE date_format(l.date_entry,'%Y') = '$year' GROUP BY month ORDER BY date_format(l.date_entry,'%m') ASC ";
     $sql = mysqli_query($connect,$qry);
     if(mysqli_num_rows($sql)){
          while($r=mysqli_fetch_array($sql)){
            
               $q = "SELECT l.id, l.loan_amount, l.status FROM clients c, loan_entries l, branches b ";
                  if(!$_SESSION['general_user'])
                      $q .= ", user_log u ";
                       $q .= " WHERE c.id = l.client AND c.branch_id = b.id AND monthname(l.date_entry)='".$r['month']."' AND ";
                          if(!$_SESSION['general_user'])
                           $q .= " u.user_branch = c.branch_id AND ";
                             if(!$_SESSION['general_user'])
                          $q .= "u.id='".$_SESSION['session_id']."' AND ";
                      if($year)
                       $q .= " date_format(l.date_entry,'%Y') = '$year' AND ";
                    $q .= " 1 ";
              $result = mysqli_query($connect,$q);
              $tot_amt=0;
              //echo $q;
               while($rw = mysqli_fetch_array($result)){
                 if($rw['status']!='03')
                    $tot_amt += $rw['loan_amount'];
                 }
              $array_loan[] = array(
                     'account' => 'Loans',
                     'month' => $r['month'],
                     'amount' => $tot_amt,
                     'month_bal'=>round(loan_performance($connect,'due-balance',$r['month'],$year)), 'year'=>$year
                  );
            }
          }

$qry = "SELECT monthname(l.date_entry) as 'month' FROM loan_entries l WHERE date_format(l.date_entry,'%Y') = '$year' GROUP BY month ORDER BY date_format(l.date_entry,'%m') ASC ";
     $sql = mysqli_query($connect,$qry);
     if(mysqli_num_rows($sql)){
          while($r=mysqli_fetch_array($sql)){
            
               $q = "SELECT l.id, l.loan_amount, l.status FROM clients c, loan_entries l, branches b ";
                  if(!$_SESSION['general_user'])
                      $q .= ", user_log u ";
                       $q .= " WHERE c.id = l.client AND c.branch_id = b.id AND monthname(l.date_entry)='".$r['month']."' AND ";
                          if(!$_SESSION['general_user'])
                           $q .= " u.user_branch = c.branch_id AND ";
                             if(!$_SESSION['general_user'])
                          $q .= "u.id='".$_SESSION['session_id']."' AND ";
                      if($year)
                       $q .= " date_format(l.date_entry,'%Y') = '$year' AND ";
                    $q .= " 1 ";
              $result = mysqli_query($connect,$q);
              $tot_amt=0;
              //echo $q;
               while($rw = mysqli_fetch_array($result)){
                 if(substr($rw['id'], 0,1)=='P')
                    $tot_amt += $rw['loan_amount'];
                 }
              $array_loan[] = array(
                     'account' => 'Extend',
                     'month' => $r['month'],
                     'amount' => $tot_amt,
                     'month_bal'=>0, 
                     'year'=>$year
                  );
            }
          }

//monthly loan payments
$qry  = "SELECT SUM(p.amount_paid) as 'total_pay', monthname(p.pay_date) as 'month' FROM clients c, loan_entries l, branches b, loan_payments p ";
    if(!$_SESSION['general_user'])
       $qry .= ", user_log u ";
         $qry .= " WHERE c.id = l.client AND c.branch_id = b.id AND p.loan = l.id AND ";
      if(!$_SESSION['general_user'])
             $qry .= " u.user_branch = c.branch_id AND ";
          if(!$_SESSION['general_user'])
                       $qry .= "u.id='".$_SESSION['session_id']."' AND ";
              if($year)
                   $qry .= " date_format(p.pay_date,'%Y') = '$year' AND ";
               $qry .= ' 1 GROUP BY month ';

$sql = mysqli_query($connect, $qry);
if(mysqli_num_rows($sql)){
    while($r=mysqli_fetch_array($sql)){
     $array_loan[]=array('account'=>'Payments','month'=>$r[1],'amount'=>$r[0], 'year'=>$year);
    }
}

print json_encode($array_loan);

?>